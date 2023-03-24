<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Validator;

class ValidateFlightOffer implements ValidationRule
{
    public function validate($attribute, $value)
    {
        $validator = Validator::make($value, [
            'type' => 'required|string|in:flight-offer',
            'id' => 'required|string',
            'source' => 'required|string|in:GDS',
            'instantTicketingRequired' => 'required|boolean',
            'nonHomogeneous' => 'required|boolean',
            'oneWay' => 'required|boolean',
            'lastTicketingDate' => 'required|date_format:Y-m-d',
            'numberOfBookableSeats' => 'required|integer|min:1',
            'itineraries.*.duration' => 'required|string',
            'itineraries.*.segments.*.departure.iataCode' => 'required|string|size:3',
            'itineraries.*.segments.*.departure.terminal' => 'string',
            'itineraries.*.segments.*.departure.at' => 'required|date_format:Y-m-d\TH:i:s',
            'itineraries.*.segments.*.arrival.iataCode' => 'required|string|size:3',
            'itineraries.*.segments.*.arrival.terminal' => 'string',
            'itineraries.*.segments.*.arrival.at' => 'required|date_format:Y-m-d\TH:i:s',
            'itineraries.*.segments.*.carrierCode' => 'required|string',
            'itineraries.*.segments.*.number' => 'required|string',
            'itineraries.*.segments.*.aircraft.code' => 'required|string',
            'itineraries.*.segments.*.operating.carrierCode' => 'required|string',
            'itineraries.*.segments.*.duration' => 'required|string',
            'itineraries.*.segments.*.id' => 'required|string',
            'itineraries.*.segments.*.numberOfStops' => 'required|integer|min:0',
            'itineraries.*.segments.*.blacklistedInEU' => 'required|boolean',
            'price.currency' => 'required|string|size:3',
            'price.total' => 'required|string',
            'price.base' => 'required|string',
            'price.fees.*.amount' => 'required',
        ]);

        return !$validator->fails();
    }
}
