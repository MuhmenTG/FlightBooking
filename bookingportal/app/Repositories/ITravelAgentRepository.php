<?php

declare(strict_types=1);
namespace App\Repositories;
use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use App\Models\UserEnquiry;
use App\Services\Booking\BookingService;
use Illuminate\Database\Eloquent\Collection;

interface ITravelAgentRepository
{
    public function findFlightSegmentsByBookingReference(string $bookingReference): Collection;
    
    public function findFlightPassengersByPNR(string $bookingReference): Collection;
    
    public function cancelFlightSegments(string $bookingReference): int;
    
    public function cancelFlightPassengers(string $bookingReference): int;
    
    public function bookPassengers(string $bookingReference, FlightOfferPassengerDTO $passenger) : bool;

    public function generateTicketNumbers(string $bookingReference);

    public function generateTicketNumber(string $validatingAirline) : string;
    
    public function getUnpaidFlightBookings(string $bookingReference): Collection;

    public function markBookingAsPaid(string $bookingReference): void;
    
    public function getPaidFlightBookings(string $bookingReference): Collection;
    
    public function getPassengerEmail(Collection $bookedPassengers): ?string;

    public function getBookingPayment(string $bookingReference);
    
    public function createFlightBookingSegment(string $bookingReference, FlightSelectionDTO $flightSegment): FlightBooking;
    
    public function getUserEnquiryById (int $enquiryId);

    public function getAllUserEnquries() : Collection;
    
    public function registerEnquiry(string $name, string $email, string $subject, string $message);

    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference);

    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo;

    public function getAllConfirmedBookings();
}

