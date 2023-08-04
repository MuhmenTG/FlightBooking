<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlightSearchRequest extends FormRequest
{
      
    public function authorize(): bool
    {
        return true;
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
