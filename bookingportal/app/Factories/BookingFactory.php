<?php

declare(strict_types=1);
namespace App\Factories;

use App\DTO\FlightSelectionDTO;
use App\DTO\HotelSelectionDTO;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use DateTime;
use Illuminate\Support\Arr;

class BookingFactory{

    public static function createFlightBookingRecord(array $flightData, string $bookingReference)
    {
        
        foreach ($flightData["itineraries"] as $itinerary) {
            foreach ($itinerary["segments"] as $segment) {
                $flightSegment = new FlightSelectionDTO($segment);
                $flightBooking = new FlightBooking();
                $flightBooking->setBookingReference($bookingReference);
                $flightBooking->setAirline($flightSegment->airline);
                $flightBooking->setFlightNumber($flightSegment->flightNumber);
                $flightBooking->setDepartureFrom($flightSegment->departureFrom);
                $flightBooking->setDepartureDateTime($flightSegment->departureDateTime);
                $flightBooking->setDepartureTerminal($flightSegment->departureTerminal ?? null);
                $flightBooking->setArrivelTo($flightSegment->arrivelTo);
                $flightBooking->setArrivelDate($flightSegment->arrivelDateTime);
                $flightBooking->setArrivelTerminal($flightSegment->arrivelTerminal ?? null);
                $flightBooking->setFlightDuration($flightSegment->flightDuration);
                $flightBooking->setIsBookingConfirmed(true);
                $flightBooking->setIsPaid(false);
                $flightBooking->save();
            }
        }
        $bookedSegments = FlightBooking::ByBookingReference($bookingReference)->get();
        return $bookedSegments;
    }

    public static function createHotelRecord(HotelSelectionDTO $HotelSelectionDTO, string $bookingReference, string $firstName, string $lastName, string $email, string $paymentId){

        $hotelBooking = new HotelBooking();
        $hotelBooking->setHotelBookingReference($bookingReference);
        $date = new DateTime();
        $hotelBooking->setIssueDate($date);
        $hotelBooking->setHotelId($HotelSelectionDTO->hotelId);
        $hotelBooking->setHotelName($HotelSelectionDTO->name);
        $hotelBooking->setHotelLocation($HotelSelectionDTO->countryCode);
        $hotelBooking->setHotelCity($HotelSelectionDTO->cityCode);
        $hotelBooking->setHotelContact("Something place holder");
        $hotelBooking->setCheckInDate($HotelSelectionDTO->checkInDate);
        $hotelBooking->setCheckOutDate($HotelSelectionDTO->checkInDate);
        $hotelBooking->setRoomType($HotelSelectionDTO->roomType);
        $hotelBooking->setNumberOfAdults($HotelSelectionDTO->guestsAdults);
        $hotelBooking->setMainGuestFirstName($firstName);
        $hotelBooking->setMainGuestLasName($lastName);
        $hotelBooking->setMainGuestEmail($email);
        $hotelBooking->setPoliciesCheckInOutCheckIn($HotelSelectionDTO->policiesCheckInOutCheckIn);
        $hotelBooking->setPoliciesCheckInOutCheckOut($HotelSelectionDTO->policiesCheckInOutCheckOut);
        $hotelBooking->setPoliciesCancellationDeadline($HotelSelectionDTO->policiesCancellationDeadline);
        $hotelBooking->setDescription($HotelSelectionDTO->description);
        $hotelBooking->setPaymentInfoId($paymentId);
        $hotelBooking->save();
        $bookedHotel = HotelBooking::ByHotelBookingReference($bookingReference)->get();
        return $bookedHotel;
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
    
    public static function getTotalPrice($response)
    {
        $travelerPricings = $response['travelerPricings'];
        $totalPrice = 0;
    
        if (count($travelerPricings) > 1) {
            foreach ($travelerPricings as $travelerPricing) {
                $totalPrice += $travelerPricing['price']['total'];
            }
        } else {
            $totalPrice = $travelerPricings[0]['price']['total'];
        }
    
        return $totalPrice;
    }
    
}