<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\Http\Resources\FlightConfirmationResource;
use App\Mail\ISendEmailService;
use App\Models\PassengerInfo;
use App\Repositories\ITravelAgentRepository;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use App\Services\Booking\IBookingService;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class BookingService implements IBookingService {

    protected $bookingRepository;
    protected $IEmailSendService;

    public function __construct(ITravelAgentRepository $bookingRepository, ISendEmailService $IEmailSendService)
    {
        $this->bookingRepository = $bookingRepository;
        $this->IEmailSendService = $IEmailSendService;
    }
    
    /**
    * {@inheritDoc}
    */
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

    
    /**
    * {@inheritDoc}
    */
    public function generateBookingConfirmationPDF($bookingComplete) : string
    {
        $pdf = FacadePdf::loadView('booking_confirmation', compact('bookingComplete'));
        return $pdf->output();
    }

    
    /**
    * {@inheritDoc}
    */
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

    
    /**
    * {@inheritDoc}
    */
    public function finalizeFlightReservation(string $bookingReference): ?Collection
    {
        $this->bookingRepository->generateTicketNumbers($bookingReference);

        $unPaidFlightBooking = $this->bookingRepository->getUnpaidFlightBookings($bookingReference);

        if ($unPaidFlightBooking->count() == 0) {
            throw new Exception('Booking already paid');
        }

        $this->bookingRepository->markBookingAsPaid($bookingReference);

        $paidFlightBooking = $this->bookingRepository->getPaidFlightBookings($bookingReference);
 
        return $paidFlightBooking;
    }

    /**
    * {@inheritDoc}
    */
    public function getPassengerEmail(string $bookingReference) : string{
        $bookedPassengers = $this->bookingRepository->findFlightPassengersByPNR($bookingReference);
        $email = $this->bookingRepository->getPassengerEmail($bookedPassengers);
        if($email){
            return $email;
        }
        return null;
    }

    /**
    * {@inheritDoc}
    */
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

    /**
    * {@inheritDoc}
    */
    public function retrieveBookingInformation(string $bookingReference) : ?array
    {
        $bookedFlightSegments = $this->bookingRepository->findFlightSegmentsByBookingReference($bookingReference);
        $bookedFlightPassenger = $this->bookingRepository->findFlightPassengersByPNR($bookingReference);
        $paymentDetails = $this->bookingRepository->getBookingPayment($bookingReference);
        
        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            return [
                'passengers' => $bookedFlightPassenger,
                'flight' => $bookedFlightSegments,
                'payment' => $paymentDetails
            ];
        }               

        return null;
    }

    
    /**
    * {@inheritDoc}
    */
    public function generateBookingReference() : string{
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $bookingNumber = '';
        for ($i = 0; $i < 6; $i++) {
            $bookingNumber .= $characters[rand(0, strlen($characters) - 1)];
        }
        $bookingNumber = strtoupper($bookingNumber);
        return $bookingNumber;
    }

    
    /**
    * {@inheritDoc}
    */
    public function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null)
    {
       
        $registeredEnquiry = $this->bookingRepository->registerEnquiry($name, $email, $subject, $message);
        if($registeredEnquiry){
            
        $userCopy = $this->IEmailSendService->sendEmailWithAttachments($name, $email, $subject, $message, "");
        if($userCopy){
            return true;
        }   
        }         
        return false;
    }

    /**
    * {@inheritDoc}
    */
    public function getFlightSegmentsByBookingReference(string $bookingReference)
    {
        return $this->bookingRepository->findFlightSegmentsByBookingReference($bookingReference);
    }

    /**
    * {@inheritDoc}
    */
    public function getFlightPassengersByPNR(string $bookingReference) : Collection
    {
        return $this->bookingRepository->findFlightPassengersByPNR($bookingReference);
    }

    /**
    * {@inheritDoc}
    */
    public function cancelFlightBooking(string $bookingReference)
    {
        $this->bookingRepository->cancelFlightSegments($bookingReference);
        $this->bookingRepository->cancelFlightPassengers($bookingReference);
    }
    
    /**
    * {@inheritDoc}
    */
    public function getUserEnquiryById(int $enquiryId)
    {
       return $this->bookingRepository->getUserEnquiryById($enquiryId);
    }

    /**
    * {@inheritDoc}
    */
    public function getAllUserEnquiries()
    {
        return $this->bookingRepository->getAllUserEnquries();
    }
    
    /**
    * {@inheritDoc}
    */
    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference){
        return $this->bookingRepository->getSpecificPassengerInBooking($passengerId, $bookingReference);
    }
    
    /**
    * {@inheritDoc}
    */
    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo
    {
        return $this->bookingRepository->updatePassenger($passenger, $firstName, $lastName, $dateOfBirth, $email);
    }

    
    /**
    * {@inheritDoc}
    */
    public function getAllConfirmedBookings(){
       return $this->bookingRepository->getAllConfirmedBookings();
    }
}