<?php

declare(strict_types=1);
namespace App\Factories;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use Illuminate\Support\Arr;

class BookingFactory{

    public static function createFlightBookingRecord(array $flightData, string $bookingReference)
    {
        
        foreach ($flightData["itineraries"] as $itinerary) {
            foreach ($itinerary["segments"] as $segment) {
                $flightBooking = new FlightBooking();
                $flightBooking->setBookingReference($bookingReference);
                $flightBooking->setAirline($segment["carrierCode"]);
                $flightBooking->setFlightNumber($segment["number"]);
                $flightBooking->setDepartureFrom($segment["departure"]["iataCode"]);
                $flightBooking->setDepartureDateTime($segment["departure"]["at"]);
                $flightBooking->setDepartureTerminal($segment["departure"]["terminal"] ?? null);
                $flightBooking->setArrivelTo($segment["arrival"]["iataCode"]);
                $flightBooking->setArrivelDate($segment["arrival"]["at"]);
                $flightBooking->setArrivelTerminal($segment["arrival"]["terminal"] ?? null);
                $flightBooking->setFlightDuration($segment["duration"]);
                $flightBooking->setIsBookingConfirmed(true);
                $flightBooking->save();
            }
        }
        $bookedSegments = FlightBooking::ByBookingReference($bookingReference)->get();
        return $bookedSegments;
    }

    public static function createPassengerRecord(array $passengersData, string $bookingReference)
    {
        foreach ($passengersData as $passenger) {
            $passengerInfo = new PassengerInfo();
            $passengerInfo->setPNR($bookingReference);
            $passengerInfo->setPaymentInfoId(1);
            $passengerInfo->setFirstName($passenger["firstName"]);
            $passengerInfo->setLastName($passenger["lastName"]);
            $passengerInfo->setDateOfBirth($passenger["dateOfBirth"]);
            $passengerInfo->setEmail($passenger["email"]);
            $passengerInfo->setPassengerType($passenger["passengerType"]);
            $passengerInfo->setTicketNumber(BookingFactory::generateTicketNumber(14));
            $passengerInfo->save();
        }
        
        $bookedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();
        return $bookedPassengers;
    }

    public static function generateTicketNumber($length) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }

    public static function generateBookingReference() : string{
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = '';
        for ($i = 0; $i < 6; $i++) {
            $result .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $result;
    }
}