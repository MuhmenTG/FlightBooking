<?php
namespace App\DTO;

class FlightSelectionDTO {

    public string $airline;
    public string $flightNumber;
    public string $departureFrom;
    public string $departureDateTime;
    public string $departureTerminal;
    public string $arrivelTo;
    public string $arrivelDateTime;
    public string $arrivelTerminal;
    public string $flightDuration;

    public function __construct($booking) {
        $this->airline = $booking["carrierCode"];
        $this->flightNumber = $booking["number"];
        $this->departureFrom = $booking["departure"]["iataCode"];
        $this->departureDateTime = $booking["departure"]["at"];
        $this->departureTerminal = $booking["departure"]["terminal"] ?? "";
        $this->arrivelTo = $booking["arrival"]["iataCode"];
        $this->arrivelDateTime = $booking["arrival"]["at"];
        $this->arrivelTerminal = $booking["arrival"]["terminal"] ?? "";
        $this->flightDuration = $booking["duration"];
     
    }
}