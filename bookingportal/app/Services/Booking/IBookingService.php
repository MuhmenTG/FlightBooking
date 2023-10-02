<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\PassengerInfo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IBookingService
 * This interface defines the methods that a booking service must implement.
 */
interface IBookingService
{
    /**
     * Book a flight using provided flight data.
     *
     * @param array $flightData The flight data for booking.
     * @return array The result of the booking operation.
     */
    public function bookFlight(array $flightData): array;

    /**
     * Create a passenger record for a booking.
     *
     * @param array $passengerData The passenger data to create a record.
     * @param string $bookingReference The booking reference associated with the passenger.
     */
    public function createPassengerRecord(array $passengerData, string $bookingReference);

    /**
     * Retrieve booking information by booking reference.
     *
     * @param string $bookingReference The booking reference to retrieve information for.
     * @return array|null The booking information if found, or null if not found.
     */
    public function retrieveBookingInformation(string $bookingReference): ?array;

    /**
     * Generate a booking reference.
     *
     * @return string The generated booking reference.
     */
    public function generateBookingReference(): string;

    /**
     * Send a contact form request.
     *
     * @param string $name The name of the person making the request.
     * @param string $email The email address of the person making the request.
     * @param string $subject The subject of the request.
     * @param string $message The message content of the request.
     * @param string|null $bookingReference The associated booking reference (optional).
     */
    public function sendRquestContactForm(string $name, string $email, string $subject, string $message, string $bookingReference = null);

    /**
     * Get flight segments by booking reference.
     *
     * @param string $bookingReference The booking reference to retrieve flight segments for.
     */
    public function getFlightSegmentsByBookingReference(string $bookingReference);

    /**
     * Cancel a flight booking by booking reference.
     *
     * @param string $bookingReference The booking reference to cancel.
     */
    public function cancelFlightBooking(string $bookingReference);

    /**
     * Get the email address of passengers by booking reference.
     *
     * @param string $bookingReference The booking reference to retrieve passenger email addresses for.
     * @return string The email address of passengers.
     */
    public function getPassengerEmail(string $bookingReference): string;

    /**
     * Finalize a flight reservation by booking reference.
     *
     * @param string $bookingReference The booking reference to finalize.
     * @return Collection|null The finalized flight reservation if successful, or null if not.
     */
    public function finalizeFlightReservation(string $bookingReference): ?Collection;

    /**
     * Get flight passengers by PNR (Passenger Name Record).
     *
     * @param string $bookingReference The booking reference to retrieve passengers for.
     * @return Collection A collection of passenger information.
     */
    public function getFlightPassengersByPNR(string $bookingReference): Collection;

    /**
     * Get a user enquiry by ID.
     *
     * @param int $enquiryId The ID of the user enquiry to retrieve.
     */
    public function getUserEnquiryById(int $enquiryId);

    /**
     * Get all user enquiries.
     *
     * @return Collection A collection of user enquiries.
     */
    public function getAllUserEnquiries();

    /**
     * Get a specific passenger in a booking by passenger ID and booking reference.
     *
     * @param int $passengerId The ID of the passenger to retrieve.
     * @param string $bookingReference The booking reference associated with the passenger.
     */
    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference);

    /**
     * Update passenger information.
     *
     * @param PassengerInfo $passenger The passenger information to update.
     * @param string $firstName The updated first name.
     * @param string $lastName The updated last name.
     * @param string $dateOfBirth The updated date of birth.
     * @param string $email The updated email address.
     * @return PassengerInfo The updated passenger information.
     */
    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo;

    /**
     * Get all confirmed bookings.
     *
     * @return Collection A collection of confirmed bookings.
     */
    public function getAllConfirmedBookings();

    /**
     * Generate a booking confirmation PDF.
     *
     * @param mixed $bookingComplete The booking completion data.
     * @return string The file path or URL of the generated PDF.
     */
    public function generateBookingConfirmationPDF($bookingComplete): string;
}
