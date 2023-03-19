<?php

use App\Models\FlightBooking;
use App\Models\PassengerInfo;

class BookingFactory{

    public static function createFlightBookingRecord(array $flightData): string
    {
        $bookingReference = "something";
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
        return $bookingReference;
    }

    public static function createPassengerRecord(array $passengersData, string $bookingReference): void
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
    }

    public static function generateTicketNumber($length) {
        $result = '';
        for($i = 0; $i < $length; $i++) {
            $result .= mt_rand(0, 9);
        }
        return $result;
    }
}