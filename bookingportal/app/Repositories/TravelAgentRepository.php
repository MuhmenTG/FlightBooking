<?php

declare(strict_types=1);
namespace App\Repositories;
use App\DTO\FlightOfferPassengerDTO;
use App\DTO\FlightSelectionDTO;
use App\Models\Airline;
use App\Models\AirportInfo;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use App\Models\Payment;
use App\Models\UserEnquiry;
use Illuminate\Database\Eloquent\Collection;
use Stripe\Charge;

class TravelAgentRepository implements ITravelAgentRepository
{
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
        return FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update([FlightBooking::COL_ISCANCELLED => true]);
    }
    
    public function cancelFlightPassengers(string $bookingReference): int
    {
        return PassengerInfo::where(PassengerInfo::COL_BOOKINGREFERENCE, $bookingReference)->update([FlightBooking::COL_ISCANCELLED => true]);
    }
    
    public function createPayment(Charge $charge, int $amount, string $currency, string $bookingreference): ?Payment
    {
        $payment = new Payment();
        $payment->setPaymentAmount($amount);
        $payment->setPaymentCurrency($currency);
        $payment->setPaymentType('Online');
        $payment->setPaymentStatus('Completed');
        $payment->setPaymentTransactionId($charge->id);
        $payment->setPaymentMethod("MasterCard");
        $payment->setPaymentGatewayProcessor("Stripe Api");
        $payment->setConnectedBookingReference($bookingreference);
        $payment->save();
        if($payment){
            return $payment;
    
        }
        return null;

    }
    
    public function bookPassengers(string $bookingReference, FlightOfferPassengerDTO $passenger) : bool
    {
        $passengerInfo = new PassengerInfo();
        $passengerInfo->setBookingReference($bookingReference);
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

    public function getBookingPayment(string $bookingReference) {
        $paymentDetails = Payment::ByConnectedBookingReference($bookingReference)->first();
        return $paymentDetails;
    }

    public function generateTicketNumbers(string $bookingReference)
    {
        $ticketRecord = FlightBooking::ByBookingReference($bookingReference)->first();
        $airlineTicketNumberIssuer = $ticketRecord->getAirline();
        $unTicketedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();

        foreach ($unTicketedPassengers as $passenger) {
            $ticketNumber = $this->generateTicketNumber($airlineTicketNumberIssuer);
            $passenger->setTicketNumber($ticketNumber);
            $passenger->save();
        }
    }

    public function generateTicketNumber(string $validatingAirline) : string {
        $validatingCarrier = Airline::ByIataDesignator($validatingAirline)->first();
        if($validatingCarrier){
            $validatingAirlineDigits = $validatingCarrier->getThreeDigitAirlineCode();
        }
        $validatingAirlineDigits = "010";
        if(!$validatingAirlineDigits){
            return $validatingAirline;
        }
        
        $ticketNumber = '';
        for($i = 0; $i < 11; $i++) {
            $ticketNumber .= mt_rand(0, 9);
        }
        
        $generatedTicketNumber = $validatingAirlineDigits."-".$ticketNumber;
        return $generatedTicketNumber;
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
        $bookings = FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 1)->get();
        return $bookings;
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
        $flightBooking->setAirline($this->getAirlineNameByAirlineIcao($flightSegment->airline));
        $flightBooking->setFlightNumber($flightSegment->flightNumber);
        $flightBooking->setDepartureFrom($this->getFullAirportNameByIcao($flightSegment->departureFrom));
        $flightBooking->setDepartureDateTime($flightSegment->departureDateTime);

        if (isset($flightSegment->departureTerminal)) {
            $flightBooking->setDepartureTerminal($flightSegment->departureTerminal);
        }

        $flightBooking->setArrivalTo($this->getFullAirportNameByIcao($flightSegment->arrivelTo));
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

    private function getFullAirportNameByIcao(string $airportIcao) : string{
        $airport = AirportInfo::ByAirportIcao($airportIcao)->first();
        $airportName = $airport->getAirportName();
        $cityName = $airport->getCity();
        $fullAirportName = "{$airportName} - {$cityName}";
        return $fullAirportName;
    }

    private function getAirlineNameByAirlineIcao(string $airlineIcao) : string {
        $airline = Airline::ByIataDesignator($airlineIcao)->first();
        if($airline){
            return $airline->getAirlineName();
        }
        return $airlineIcao;
    }

    public function getUserEnquiryById (int $enquiryId){
       $specificUserEnquiry = UserEnquiry::byId($enquiryId)->first();
       return $specificUserEnquiry;
    }

    public function getAllUserEnquries() : Collection{
        $userEnquiries = UserEnquiry::all();
        return $userEnquiries;
    }

    
    public function registerEnquiry(string $name, string $email, string $subject, string $message){
        $enquiry = new UserEnquiry();
        $enquiry->setName($name);
        $enquiry->setEmail($email);
        $enquiry->setSubject($subject);
        $enquiry->setBookingreference($bookingReference ?? "Ikke relevant");
        $enquiry->setMessage($message);
        $enquiry->setTime(time());
        return $enquiry->save();
    } 

    public function getSpecificPassengerInBooking(int $passengerId, string $bookingReference){
        return PassengerInfo::where(PassengerInfo::COL_ID, $passengerId)
            ->where(PassengerInfo::COL_BOOKINGREFERENCE, $bookingReference)
            ->first();
    }

    public function updatePassenger(PassengerInfo $passenger, string $firstName, string $lastName, string $dateOfBirth, string $email): PassengerInfo
    {
        $passenger->setFirstName($firstName);
        $passenger->setLastName($lastName);
        $passenger->setDateOfBirth($dateOfBirth);
        $passenger->setEmail($email);
        $passenger->save();
        return $passenger;
    }

    
    public function getAllConfirmedBookings(){
        return FlightBooking::with('passengers')
        ->where(FlightBooking::COL_ISPAID, 1)
        ->get();
    }
}

