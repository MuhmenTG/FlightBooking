<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FlightConfirmationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'flightSegmentNumber' => $this->id,
            'bookingReference' => $this->bookingReference,
            'airline' => $this->airline,
            'flightNumber' => $this->flightNumber,
            'departureFrom' => $this->departureFrom,
            'departureTerminal' => $this->departureTerminal,
            'departureDateTime' => $this->departureDateTime,
            'formattedDepartureDateTime' => $this->formatDateTime($this->departureDateTime),
            'arrivalTo' => $this->arrivalTo,
            'arrivalTerminal' => $this->arrivalTerminal,
            'arrivalDate' => $this->arrivalDate,
            'formattedArrivalDate' => $this->formatDateTime($this->arrivalDate),
            'flightDuration' => $this->flightDuration,
            'bookingStatus' => $this->isBookingConfirmed,
            'paymentStatus' => $this->isPaid,
        ];
    }

    private function formatDateTime($dateTime)
    {
        // Format date and time as desired, for example:
        return \Carbon\Carbon::parse($dateTime)->format('Y-m-d H:i:s');
    }

    private function formatDuration($duration)
    {
        // Format duration as desired, for example:
      //  return \Carbon\CarbonInterval::createFromDateString($duration)->cascade()->forHumans();
    }
}
