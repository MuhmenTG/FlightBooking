<?php

namespace App\DTO;

use Illuminate\Support\Arr;

class FlightOfferPassengerDTO 
{
    public string $firstName;
    public string $lastName;
    public string $dateOfBirth;
    public string $email;
    public string $passengerType;
    public function __construct(array $passengerData)
    {
        $this->firstName = $passengerData['firstName'];
        $this->lastName = $passengerData['lastName'];
        $this->dateOfBirth = $passengerData['dateOfBirth'];
        $this->email = $passengerData['email'];
        $this->passengerType = $passengerData['passengerType'];

    }
}
