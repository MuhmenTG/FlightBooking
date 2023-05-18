<?php

declare(strict_types=1);
namespace App\Services\Booking;

use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\DTO\HotelSelectionDTO;
use App\Mail\SendEmail;
use App\Models\Airline;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use App\Models\Payment;
use App\Models\UserEnquiry;
use App\Repositories\TravelAgentRepository;
use App\Services\Booking\IBookingService;
use DateTime;
use Exception;
use InvalidArgumentException;

class BookingService implements IBookingService {

    protected $bookingRepository;

    public function __construct(TravelAgentRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function createFlightBookingRecord(array $flightData, string $bookingReference)
    {
        if (!isset($flightData["itineraries"]) || !is_array($flightData["itineraries"])) {
            throw new InvalidArgumentException("Invalid flight data provided.");
        }

        $bookedSegments = [];

        foreach ($flightData["itineraries"] as $itinerary) {
            if (!isset($itinerary["segments"]) || !is_array($itinerary["segments"])) {
                continue;
            }

            foreach ($itinerary["segments"] as $segment) {
                if (!is_array($segment) || empty($segment)) {
                    continue;
                }

                $flightSegment = new FlightSelectionDTO($segment);

                $bookedSegment = $this->bookingRepository->createFlightBookingSegment($bookingReference, $flightSegment);

                $bookedSegments[] = $bookedSegment;
            }
        }

        return $bookedSegments;
    }

    public static function createHotelRecord(HotelSelectionDTO $HotelSelectionDTO, string $bookingReference, string $firstName, string $lastName, string $email, string $paymentId){

        $hotelBooking = new HotelBooking();
        $hotelBooking->setHotelBookingReference($bookingReference);
        $date = new DateTime();
        $hotelBooking->setIssueDate($date);
        $hotelBooking->setHotelId($HotelSelectionDTO->hotelId ?? "0");
        $hotelBooking->setHotelOfferId($HotelSelectionDTO->hotelOfferId);
        $hotelBooking->setHotelName($HotelSelectionDTO->name);
        $hotelBooking->setHotelLocation($HotelSelectionDTO->countryCode);
        $hotelBooking->setHotelCity($HotelSelectionDTO->cityCode);
        $hotelBooking->setHotelContact("Something place holder");
        $hotelBooking->setCheckInDate($HotelSelectionDTO->checkInDate);
        $hotelBooking->setCheckOutDate($HotelSelectionDTO->checkOutDate);
        $hotelBooking->setRoomType($HotelSelectionDTO->roomType);
        $hotelBooking->setNumberOfAdults($HotelSelectionDTO->guestsAdults);
        $hotelBooking->setMainGuestFirstName($firstName);
        $hotelBooking->setMainGuestLasName($lastName);
        $hotelBooking->setMainGuestEmail($email);
        $hotelBooking->setPoliciesCheckInOutCheckIn($HotelSelectionDTO->policiesCheckInOutCheckIn);
        $hotelBooking->setPoliciesCheckInOutCheckOut($HotelSelectionDTO->policiesCheckInOutCheckOut);
        $hotelBooking->setPoliciesCancellationDeadline($HotelSelectionDTO->policiesCancellationDeadline);
        $hotelBooking->setDescription($HotelSelectionDTO->description);
        $hotelBooking->setPaymentInfoId($paymentId);
        $hotelBooking->save();
        return $hotelBooking;
    }

    public function bookFlight(array $flightData): array
    {
        
        if (empty($flightData)) {
            throw new \InvalidArgumentException('Empty flight data');
        }

        $bookingReferenceNumber = $this->generateBookingReference();

        $passengerData = $flightData[PassengerInfo::PASSENGERS_ARRAY];
        if(!$passengerData){
            throw new Exception('Could not find passenger records');
        }

        $passengers = $this->createPassengerRecord($passengerData, $bookingReferenceNumber);
        if(!$passengers){
            throw new Exception('Could not create passenger record');
        }

        $flightSegments = $this->createFlightBookingRecord($flightData, $bookingReferenceNumber);
        if(!$flightSegments){
            throw new Exception('Could not create flight segments record');
        }

        return [
            'success' => true,
            'bookingReference' => $bookingReferenceNumber
        ];
    }

    public function payFlightConfirmation(string $bookingReference, string $cardNumber, string $expireMonth, string $expireYear, string $cvcDigits, int $grandTotal): ?array
    {
        $this->bookingRepository->generateTicketNumbers($bookingReference);

        $unPaidFlightBooking = $this->bookingRepository->getUnpaidFlightBookings($bookingReference);

        if ($unPaidFlightBooking->count() == 0) {
            throw new Exception('Invalid booking');
        }

        $transaction = $this->bookingRepository->createPaymentTransaction($grandTotal, $cardNumber, $expireMonth, $expireYear, $cvcDigits, $bookingReference);

        if (!$transaction) {
            throw new Exception('Could not create transaction');
        }

        $this->bookingRepository->markBookingAsPaid($bookingReference);

        $paidFlightBooking = $this->bookingRepository->getPaidFlightBookings($bookingReference);
        $bookedPassengers = $this->bookingRepository->findFlightPassengersByPNR($bookingReference);

        $booking = [
            'success' => true,
            'itinerary' => $paidFlightBooking,
            'passengers' => $bookedPassengers,
            'transaction' => $transaction
        ];

        $email = $this->bookingRepository->getPassengerEmail($bookedPassengers);

        SendEmail::sendEmailWithAttachments("Muhmen", $email, $bookingReference, "Booking");

        return $booking;
    }

    public function createPassengerRecord(array $passengerData, string $bookingReference)
    {
        $passengers = [];
        foreach ($passengerData as $data) {
            $passengers[] = new FlightOfferPassengerDTO($data);
        }
        foreach ($passengers as $passenger) {
            $this->bookingRepository->bookPassengers($bookingReference, $passenger);
        }       
        return $this->bookingRepository->findFlightPassengersByPNR($bookingReference);
    }

    public static function retrieveBookingInformation(string $bookingReference) : ?array
    {
        $bookedFlightSegments = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();
        $bookedFlightPassenger = PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->get();

        $bookedHotel = HotelBooking::byHotelBookingReference($bookingReference)->first();

        $paymentDetails = Payment::ByNote($bookingReference)->first();

        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            return [
                'success' => true,
                'PAX' => $bookedFlightPassenger,
                'flight' => $bookedFlightSegments,
                'payment' => $paymentDetails
            ];
        }

        if ($bookedHotel) {
            return [
                'success' => true,
                'hotelVoucher' => $bookedHotel,
                'payment' => $paymentDetails
            ];
        }

        return null;
    }

    public static function generateTicketNumber(string $validatingAirline) : string {
        $validatingCarrier = Airline::ByIataDesignator($validatingAirline)->first();
        $validatingAirlineDigits = $validatingCarrier->ByThreeDigitAirlineCode();
        
        $ticketNumber = '';
        for($i = 0; $i < 11; $i++) {
            $ticketNumber .= mt_rand(0, 9);
        }
        
        $generatedTicketNumber = $validatingAirlineDigits."-".$ticketNumber;
        return $generatedTicketNumber;
    }

    public function generateBookingReference() : string{
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $bookingNumber = '';
        for ($i = 0; $i < 6; $i++) {
            $bookingNumber .= $characters[rand(0, strlen($characters) - 1)];
        }
        $bookingNumber = strtoupper($bookingNumber);
        return $bookingNumber;
    }

    public static function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null){
        
        $enquiry = new UserEnquiry();
        $enquiry->setName($name);
        $enquiry->setEmail($email);
        $enquiry->setSubject($subject);
        $enquiry->setBookingreference($bookingReference ?? "Ikke relevant");
        $enquiry->setMessage($message);
        $enquiry->setTime(time());
        $enquiry->save();
        
        $userCopy = SendEmail::sendEmailWithAttachments($name, $email, $subject, $message);
        if($userCopy){
            return true;
        }            
        return false;
    }

    public function getHotelBookingByBookingReference(string $bookingReference)
    {
        return $this->bookingRepository->findHotelBookingByReference($bookingReference);
    }

    public function cancelHotelBooking(string $bookingReference)
    {
        return $this->bookingRepository->cancelHotelBooking($bookingReference);
    }

    public function getFlightSegmentsByBookingReference(string $bookingReference)
    {
        return $this->bookingRepository->findFlightSegmentsByBookingReference($bookingReference);
    }

    public function getFlightPassengersByPNR(string $bookingReference)
    {
        return $this->bookingRepository->findFlightPassengersByPNR($bookingReference);
    }

    public function cancelFlightBooking(string $bookingReference)
    {
        $this->bookingRepository->cancelFlightSegments($bookingReference);
        $this->bookingRepository->cancelFlightPassengers($bookingReference);
    }

}