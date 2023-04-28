<?php

declare(strict_types=1);
namespace App\Services;

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
use DateTime;
use Exception;
use InvalidArgumentException;

class BookingService {

    public static function createFlightBookingRecord(array $flightData, string $bookingReference)
    {
        if (!isset($flightData["itineraries"]) || !is_array($flightData["itineraries"])) {
            throw new InvalidArgumentException("Invalid flight data provided.");
        }
        foreach ($flightData["itineraries"] as $itinerary) {
            if (!isset($itinerary["segments"]) || !is_array($itinerary["segments"])) {
                continue;
            }
            foreach ($itinerary["segments"] as $segment) {
                if (!is_array($segment) || empty($segment)) {
                    continue;
                }
                $flightSegment = new FlightSelectionDTO($segment);
                $flightBooking = new FlightBooking();
                $flightBooking->setBookingReference($bookingReference);
                $flightBooking->setAirline($flightSegment->airline);
                $flightBooking->setFlightNumber($flightSegment->flightNumber);
                $flightBooking->setDepartureFrom($flightSegment->departureFrom);
                $flightBooking->setDepartureDateTime($flightSegment->departureDateTime);
                if (isset($flightSegment->departureTerminal)) {
                    $flightBooking->setDepartureTerminal($flightSegment->departureTerminal);
                }
                $flightBooking->setArrivelTo($flightSegment->arrivelTo);
                $flightBooking->setArrivelDate($flightSegment->arrivelDateTime);
                if (isset($flightSegment->arrivelTerminal)) {
                    $flightBooking->setArrivelTerminal($flightSegment->arrivelTerminal);
                }
                $flightBooking->setFlightDuration($flightSegment->flightDuration);
                $flightBooking->setIsBookingConfirmed(true);
                $flightBooking->setIsPaid(false);
                $flightBooking->save();
            }
        }
        $bookedSegments = FlightBooking::ByBookingReference($bookingReference)->get();
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

    public static function bookFlight(array $flightData): array
    {
        
        if (empty($flightData)) {
            throw new \InvalidArgumentException('Empty flight data');
        }

        $bookingReferenceNumber = BookingService::generateBookingReference();

        $passengerData = $flightData[PassengerInfo::PASSENGERS_ARRAY];
        if(!$passengerData){
            throw new Exception('Could not find passenger records');
        }

        $issuingAirline = $flightData[PassengerInfo::VALIDATINGAIRLINE][0];
        if(!$issuingAirline){
            throw new Exception('Could not find issueing airline');
        }

        $passengers = BookingService::createPassengerRecord($passengerData, $issuingAirline, $bookingReferenceNumber);
        if(!$passengers){
            throw new Exception('Could not create passenger record');
        }

        $flightSegments = BookingService::createFlightBookingRecord($flightData, $bookingReferenceNumber);
        if(!$flightSegments){
            throw new Exception('Could not create flight segments record');
        }

        return [
            'success' => true,
            'bookingReference' => $bookingReferenceNumber
        ];
    }

    public static function payFlightConfirmation(string $bookingReference, string $cardNumber, string $expireMonth, string $expireYear, string $cvcDigits, int $grandTotal)
    {
        $unPaidflightBooking = FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 0)->get();

        $bookedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();

        if($unPaidflightBooking->count() == 0){
            throw new Exception('Invalid booking');
        }

        $grandTotal = $grandTotal * 100;

        $transaction = PaymentService::createCharge($grandTotal, "dkk", $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReference);

        if(!$transaction){
            throw new Exception('Could not create transaction');
        }

        $isPaymentOK = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update([FlightBooking::COL_ISPAID => 1]);

        if($isPaymentOK){
            $paidflightBooking = FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 1)->get();
        }

        $booking = [
            'success' => true,
            'itinerary' => $paidflightBooking,
            'passengers' => $bookedPassengers,
            'transaction' => $transaction
        ];

        $email = null;
        foreach($bookedPassengers as $bookedPassenger){
            $email = $bookedPassenger->getEmail();
        }

        SendEmail::sendEmailWithAttachments("Muhmen", $email, $bookingReference, "Booking");

        return $booking;
    }

    public static function createPassengerRecord(array $passengerData, string $validatingAirlineCodes, string $bookingReference)
    {
        $passengers = [];
        foreach ($passengerData as $data) {
            $passengers[] = new FlightOfferPassengerDTO($data);
        }
        foreach ($passengers as $passenger) {
            $passengerInfo = new PassengerInfo();
            $passengerInfo->setPNR($bookingReference);
            $passengerInfo->setPaymentInfoId(1);
            $passengerInfo->setTitle($passenger->title);
            $passengerInfo->setFirstName($passenger->firstName);
            $passengerInfo->setLastName($passenger->lastName);
            $passengerInfo->setDateOfBirth($passenger->dateOfBirth);
            $passengerInfo->setEmail($passenger->email);
            $passengerInfo->setPassengerType($passenger->passengerType);
            $passengerInfo->setTicketNumber(BookingService::generateTicketNumber($validatingAirlineCodes));
            $passengerInfo->save();
        }       
        $bookedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();
        return $bookedPassengers;
    }

    public static function retrieveBookingInformation(string $bookingReference)
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

        return false;
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

    public static function generateBookingReference() : string{
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
    
    
}