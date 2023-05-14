<?php
namespace App\Repositories;

use Amadeus\ReferenceData\Locations\Hotel;
use App\Models\Booking;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
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
        return PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->get();
    }
    
    public function cancelFlightSegments(string $bookingReference): int
    {
        return FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update(['is_cancelled' => true]);
    }
    
    public function cancelFlightPassengers(string $bookingReference): int
    {
        return PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->update(['is_cancelled' => true]);
    }
    
}

