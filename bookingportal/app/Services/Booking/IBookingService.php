<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\PassengerInfo;
use Illuminate\Database\Eloquent\Collection;

interface IBookingService
{
    public function bookFlight(array $flightData): array;

    public function createPassengerRecord(array $passengerData, string $bookingReference);

    public function retrieveBookingInformation(string $bookingReference): ?array;

    public function generateBookingReference(): string;

    public function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null);

    public function getFlightSegmentsByBookingReference(string $bookingReference);

    public function cancelFlightBooking(string $bookingReference);

    public function getPassengerEmail(string $bookingReference) : string;

    public function finalizeFlightReservation(string $bookingReference): ?Collection;

    public function getFlightPassengersByPNR(string $bookingReference) : Collection;

    public function getUserEnquiryById(int $enquiryId);

    public function getAllUserEnquiries();
    
    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference);

    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo;

    public function getAllConfirmedBookings();

    public function generateBookingConfirmationPDF($bookingComplete) : string;
}