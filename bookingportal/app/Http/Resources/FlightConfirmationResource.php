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
            'departureDateTime' => $this->formatDateTime($this->departureDateTime),
            'arrivalTo' => $this->arrivalTo,
            'arrivalTerminal' => $this->arrivalTerminal,
            'arrivalDate' => $this->formatDateTime($this->arrivalDate),
            'flightDuration' => $this->formatFlightDuration($this->flightDuration),
            'bookingStatus' => $bookingStatus = $this->isBookingConfirmed ? 'Confirmed' : 'Not Confirmed',
            'paymentStatus' => $paymentStatus = $this->isPaid ? 'Paid' : 'Not Paid',   
        ];
    }

    private function formatDateTime($dateTime)
    {
        // Format date and time as desired, for example:
        return \Carbon\Carbon::parse($dateTime)->format('Y-m-d H:i:s');
    }

    private function formatFlightDuration($flightDuration) {
        preg_match('/PT(\d+H)?(\d+M)?/', $flightDuration, $matches);
        $hours = isset($matches[1]) ? intval(substr($matches[1], 0, -1)) : 0;
        $minutes = isset($matches[2]) ? intval(substr($matches[2], 0, -1)) : 0;
        return ($hours > 0 ? $hours . ' hour' . ($hours > 1 ? 's' : '') . ' and ' : '') . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }
    
}
