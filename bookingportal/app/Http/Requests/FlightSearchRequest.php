<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'originLocationCode' => 'required|string',
            'destinationLocationCode' => 'required|string',
            'departureDate' => 'required|string',
            'adults' => 'required|integer',
            'returnDate' => 'nullable|string',
            'children' => 'nullable|integer',
            'infants' => 'nullable|integer',
            'travelClass' => 'nullable|string',
            'includedAirlineCodes' => 'nullable|string',
            'excludedAirlineCodes' => 'nullable|string',
            'nonStop' => 'nullable|boolean',
        ];
     }
}
