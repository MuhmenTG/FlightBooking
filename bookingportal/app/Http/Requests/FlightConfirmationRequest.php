<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightConfirmationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            //
            'itineraries' => 'required|array',
            'itineraries.*.segments.*.duration' => 'required|string',
            'itineraries.*.segments' => 'required|array',
            'passengers' => 'required|array',
            'passengers.*.title' => 'required|string',
            'passengers.*.firstName' => 'required|string',
            'passengers.*.lastName' => 'required|string',
            'passengers.*.dateOfBirth' => 'required|string',
            'passengers.*.email' => 'required|email',
            'passengers.*.passengerType' => 'required|string',
        ];
    }
}
