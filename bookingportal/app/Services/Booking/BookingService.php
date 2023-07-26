<?php

declare(strict_types=1);
namespace App\Services\Booking;

use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\Mail\ISendEmailService;
use App\Models\Airline;
use App\Models\PassengerInfo;
use App\Models\Payment;
use App\Repositories\ITravelAgentRepository;
use App\Services\Booking\IBookingService;
use Exception;
use InvalidArgumentException;

class BookingService implements IBookingService {

    protected $bookingRepository;
    protected $IEmailSendService;

    public function __construct(ITravelAgentRepository $bookingRepository, ISendEmailService $IEmailSendService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->IEmailSendService = $IEmailSendService;
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

    public function finalizeFlightReservation(string $bookingReference): ?array
    {
        $this->bookingRepository->generateTicketNumbers($bookingReference);

        $unPaidFlightBooking = $this->bookingRepository->getUnpaidFlightBookings($bookingReference);

        if ($unPaidFlightBooking->count() == 0) {
            throw new Exception('Booking already paid');
        }

        $this->bookingRepository->markBookingAsPaid($bookingReference);

        $paidFlightBooking = $this->bookingRepository->getPaidFlightBookings($bookingReference);
        $bookedPassengers = $this->bookingRepository->findFlightPassengersByPNR($bookingReference);

        $booking = [
            'success' => true,
            'itinerary' => $paidFlightBooking,
            'passengers' => $bookedPassengers,
        ];

        $email = $this->bookingRepository->getPassengerEmail($bookedPassengers);

        $this->IEmailSendService->sendEmailWithAttachments("Muhmen", $email, $bookingReference, "Booking");

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

    public function retrieveBookingInformation(string $bookingReference) : ?array
    {
        $bookedFlightSegments = $this->bookingRepository->findFlightSegmentsByBookingReference($bookingReference);
        $bookedFlightPassenger = $this->bookingRepository->findFlightPassengersByPNR($bookingReference);
        
        $paymentDetails = Payment::ByNote($bookingReference)->first();

        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            return [
                'success' => true,
                'PAX' => $bookedFlightPassenger,
                'flight' => $bookedFlightSegments,
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

    public function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null)
    {
       
        $registeredEnquiry = $this->bookingRepository->registerEnquiry($name, $email, $subject, $message);
        if($registeredEnquiry){
            
        $userCopy = $this->IEmailSendService->sendEmailWithAttachments($name, $email, $subject, $message);
        if($userCopy){
            return true;
        }   
        }         
        return false;
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

    public function getUserEnquiryById(int $enquiryId)
    {
       return $this->bookingRepository->getUserEnquiryById($enquiryId);
    }

    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference){
        return $this->bookingRepository->getSpecificPassengerInBooking($passengerId, $bookingReference);
    }

    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo
    {
        return $this->bookingRepository->updatePassenger($passenger, $firstName, $lastName, $dateOfBirth, $email);
    }
}