<?php

declare(strict_types=1);
namespace App\Repositories;

use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Stripe\Charge;

/**
 * Interface ITravelAgentRepository
 * This interface defines the methods that a travel agent repository must implement.
 */
interface ITravelAgentRepository
{
    /**
     * Find flight segments by booking reference.
     *
     * @param string $bookingReference The booking reference to search for.
     * @return Collection A collection of flight segments.
     */
    public function findFlightSegmentsByBookingReference(string $bookingReference): Collection;

    /**
     * Find flight passengers by PNR (Passenger Name Record).
     *
     * @param string $bookingReference The booking reference to search for.
     * @return Collection A collection of passenger information.
     */
    public function findFlightPassengersByPNR(string $bookingReference): Collection;

    /**
     * Cancel flight segments by booking reference.
     *
     * @param string $bookingReference The booking reference to cancel flight segments for.
     * @return int The number of canceled flight segments.
     */
    public function cancelFlightSegments(string $bookingReference): int;

    /**
     * Cancel flight passengers by booking reference.
     *
     * @param string $bookingReference The booking reference to cancel flight passengers for.
     * @return int The number of canceled flight passengers.
     */
    public function cancelFlightPassengers(string $bookingReference): int;

    /**
     * Book passengers for a flight.
     *
     * @param string $bookingReference The booking reference for which to book passengers.
     * @param FlightOfferPassengerDTO $passenger The passenger information to book.
     * @return bool True if booking was successful, false otherwise.
     */
    public function bookPassengers(string $bookingReference, FlightOfferPassengerDTO $passenger): bool;

    /**
     * Generate ticket numbers for a booking reference.
     *
     * @param string $bookingReference The booking reference for which to generate ticket numbers.
     */
    public function generateTicketNumbers(string $bookingReference);

    /**
     * Generate a ticket number for a validating airline.
     *
     * @param string $validatingAirline The validating airline code.
     * @return string The generated ticket number.
     */
    public function generateTicketNumber(string $validatingAirline): string;

    /**
     * Get unpaid flight bookings for a booking reference.
     *
     * @param string $bookingReference The booking reference to retrieve unpaid flight bookings for.
     * @return Collection A collection of unpaid flight bookings.
     */
    public function getUnpaidFlightBookings(string $bookingReference): Collection;

    /**
     * Mark a booking as paid.
     *
     * @param string $bookingReference The booking reference to mark as paid.
     */
    public function markBookingAsPaid(string $bookingReference): void;

    /**
     * Get paid flight bookings for a booking reference.
     *
     * @param string $bookingReference The booking reference to retrieve paid flight bookings for.
     * @return Collection A collection of paid flight bookings.
     */
    public function getPaidFlightBookings(string $bookingReference): Collection;

    /**
     * Get the email address of passengers from a collection of booked passengers.
     *
     * @param Collection $bookedPassengers A collection of booked passengers.
     * @return string|null The email address of passengers, or null if not found.
     */
    public function getPassengerEmail(Collection $bookedPassengers): ?string;

    /**
     * Get the payment information for a booking reference.
     *
     * @param string $bookingReference The booking reference to retrieve payment information for.
     * @return mixed The payment information.
     */
    public function getBookingPayment(string $bookingReference);

    /**
     * Create a flight booking segment.
     *
     * @param string $bookingReference The booking reference to create the segment for.
     * @param FlightSelectionDTO $flightSegment The flight segment information.
     * @return FlightBooking The created flight booking segment.
     */
    public function createFlightBookingSegment(string $bookingReference, FlightSelectionDTO $flightSegment): FlightBooking;

    /**
     * Get a user enquiry by ID.
     *
     * @param int $enquiryId The ID of the user enquiry to retrieve.
     * @return mixed The user enquiry information.
     */
    public function getUserEnquiryById(int $enquiryId);

    /**
     * Get all user enquiries.
     *
     * @return Collection A collection of user enquiries.
     */
    public function getAllUserEnquries(): Collection;

    /**
     * Register a new user enquiry.
     *
     * @param string $name The name of the user making the enquiry.
     * @param string $email The email address of the user making the enquiry.
     * @param string $subject The subject of the enquiry.
     * @param string $message The message content of the enquiry.
     */
    public function registerEnquiry(string $name, string $email, string $subject, string $message);

    /**
     * Get a specific passenger in a booking by passenger ID and booking reference.
     *
     * @param int $passengerId The ID of the passenger to retrieve.
     * @param string $bookingReference The booking reference associated with the passenger.
     * @return mixed The passenger information.
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
     * Create a payment record.
     *
     * @param Charge $charge The Stripe charge object.
     * @param int $amount The payment amount.
     * @param string $currency The currency of the payment.
     * @param string $bookingReference The booking reference associated with the payment.
     * @return Payment|null The created payment record or null if unsuccessful.
     */
    public function createPayment(Charge $charge, int $amount, string $currency, string $bookingReference): ?Payment;

    /**
     * Get all confirmed bookings.
     *
     * @return Collection A collection of confirmed bookings.
     */
    public function getAllConfirmedBookings();
}
