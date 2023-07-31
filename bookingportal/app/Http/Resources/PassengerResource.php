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
            'passengeId' => $this->id,
            'passengerTitle' => $this->title,
            'passengerFirstName' => $this->firstName,
            'passengerLastName' => $this->lastName,
            'passengerDateOfBirth' => $this->dateOfBirth,
            'passengerEmail' => $this->email,
            'passengerType' => $this->passengerType,
            'passengerticketNumber' => $this->ticketNumber,
            'conntecedBookingReference' => $this->bookingReference,
            'connectedPaymentReference' => $this->PaymentInfoId,
       ];
    }
}
