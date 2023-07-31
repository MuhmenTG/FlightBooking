<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\PassengerInfo;

interface IBookingService
{
    public function bookFlight(array $flightData): array;

    public function createPassengerRecord(array $passengerData, string $bookingReference);

    public function retrieveBookingInformation(string $bookingReference): ?array;

    public static function generateTicketNumber(string $validatingAirline): string;

    public function generateBookingReference(): string;

    public function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null);

    public function getFlightSegmentsByBookingReference(string $bookingReference);

    public function getFlightPassengersByPNR(string $bookingReference);

    public function cancelFlightBooking(string $bookingReference);

    public function finalizeFlightReservation(string $bookingReference): ?array;

    public function getUserEnquiryById(int $enquiryId);

    public function getAllUserEnquiries();
    
    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference);

    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo;

    public function getAllConfirmedBookings();
}