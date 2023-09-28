<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PassengerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'passengerGender' => $this->gender,
            'passengerFirstName' => $this->firstName,
            'passengerLastName' => $this->lastName,
            'passengerDateOfBirth' => $this->dateOfBirth,
            'passengerEmail' => $this->email,
            'passengerType' => $this->passengerType,
            'passengerticketNumber' => $this->ticketNumber,
            /* OBS: Jeg har rettet en stavefejl */
            'connectedBookingReference' => $this->bookingReference,
            'connectedPaymentReference' => $this->PaymentInfoId,
        ];
    }
}