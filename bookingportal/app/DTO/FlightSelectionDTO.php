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

    public function __construct($segment) {
        $this->airline = $segment["carrierCode"];
        $this->flightNumber = $segment["number"];
        $this->departureFrom = $segment["departure"]["iataCode"];
        $this->departureDateTime = $segment["departure"]["at"];
        $this->departureTerminal = $segment["departure"]["terminal"] ?? "";
        $this->arrivelTo = $segment["arrival"]["iataCode"];
        $this->arrivelDateTime = $segment["arrival"]["at"];
        $this->arrivelTerminal = $segment["arrival"]["terminal"] ?? "";
        $this->flightDuration = $segment["duration"];
    }
}
