<?php

declare(strict_types=1);

namespace App\Services\Booking;

interface IBookingService
{
    public function bookFlight(array $flightData): array;

    public function createPassengerRecord(array $passengerData, string $bookingReference);

    public function retrieveBookingInformation(string $bookingReference): ?array;

    public static function generateTicketNumber(string $validatingAirline): string;

    public function generateBookingReference(): string;

    public function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null);

    public function getHotelBookingByBookingReference(string $bookingReference);

    public function cancelHotelBooking(string $bookingReference);

    public function getFlightSegmentsByBookingReference(string $bookingReference);

    public function getFlightPassengersByPNR(string $bookingReference);

    public function cancelFlightBooking(string $bookingReference);

    public function finalizeFlightReservation(string $bookingReference): ?array;
}