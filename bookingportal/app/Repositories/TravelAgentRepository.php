<?php

declare(strict_types=1);
namespace App\Repositories;
use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use App\Services\Booking\BookingService;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Collection;

class TravelAgentRepository
{
    public function findHotelBookingByReference(string $bookingReference): ?HotelBooking
    {
        return HotelBooking::ByHotelBookingReference($bookingReference)->first();
    }
    
    public function cancelHotelBooking(string $bookingReference): int
    {
        return HotelBooking::where(HotelBooking::COL_HOTELBOOKINGREFERENCE, $bookingReference)->update(['cancelled' => true]);
    }
    
    public function findFlightSegmentsByBookingReference(string $bookingReference): Collection
    {
        return FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();
    }
    
    public function findFlightPassengersByPNR(string $bookingReference): Collection
    {
        return PassengerInfo::ByBookingReference($bookingReference)->get();
    }
    
    public function cancelFlightSegments(string $bookingReference): int
    {
        return FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update(['is_cancelled' => true]);
    }
    
    public function cancelFlightPassengers(string $bookingReference): int
    {
        return PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->update(['is_cancelled' => true]);
    }
    
    public function bookPassengers(string $bookingReference, FlightOfferPassengerDTO $passenger){
        $passengerInfo = new PassengerInfo();
        $passengerInfo->setPNR($bookingReference);
        $passengerInfo->setPaymentInfoId(1);
        $passengerInfo->setTitle($passenger->title);
        $passengerInfo->setFirstName($passenger->firstName);
        $passengerInfo->setLastName($passenger->lastName);
        $passengerInfo->setDateOfBirth($passenger->dateOfBirth);
        $passengerInfo->setEmail($passenger->email);
        $passengerInfo->setPassengerType($passenger->passengerType);
        $passengerInfo->setTicketNumber('Ticket not issued yet');
        return $passengerInfo->save();
    }

    public function generateTicketNumbers(string $bookingReference): void
    {
        $ticketRecord = FlightBooking::ByBookingReference($bookingReference)->first();
        $airlineTicketNumberIssuer = $ticketRecord->getAirline();
        $unTicketedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();

        foreach ($unTicketedPassengers as $passenger) {
            $ticketNumber = BookingService::generateTicketNumber($airlineTicketNumberIssuer);
            $passenger->setTicketNumber($ticketNumber);
            $passenger->save();
        }
    }

    public function getUnpaidFlightBookings(string $bookingReference): Collection
    {
        return FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 0)->get();
    }

    public function markBookingAsPaid(string $bookingReference): void
    {
        FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update([FlightBooking::COL_ISPAID => 1]);
    }

    public function getPaidFlightBookings(string $bookingReference): Collection
    {
        return FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 1)->get();
    }

    public function getPassengerEmail(Collection $bookedPassengers): ?string
    {
        $email = null;
        foreach ($bookedPassengers as $bookedPassenger) {
            $email = $bookedPassenger->getEmail();
        }
        return $email;
    }

    public function createFlightBookingSegment(string $bookingReference, FlightSelectionDTO $flightSegment): FlightBooking
    {
        $flightBooking = new FlightBooking();
        $flightBooking->setBookingReference($bookingReference);
        $flightBooking->setAirline($flightSegment->airline);
        $flightBooking->setFlightNumber($flightSegment->flightNumber);
        $flightBooking->setDepartureFrom($flightSegment->departureFrom);
        $flightBooking->setDepartureDateTime($flightSegment->departureDateTime);

        if (isset($flightSegment->departureTerminal)) {
            $flightBooking->setDepartureTerminal($flightSegment->departureTerminal);
        }

        $flightBooking->setArrivalTo($flightSegment->arrivelTo);
        $flightBooking->setArrivalDate($flightSegment->arrivelDateTime);

        if (isset($flightSegment->arrivelTerminal)) {
            $flightBooking->setArrivalTerminal($flightSegment->arrivelTerminal);
        }

        $flightBooking->setFlightDuration($flightSegment->flightDuration);
        $flightBooking->setIsBookingConfirmed(true);
        $flightBooking->setIsPaid(false);
        $flightBooking->save();

        return $flightBooking;
    }

}

