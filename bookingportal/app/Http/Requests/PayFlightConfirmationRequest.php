<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayFlightConfirmationRequest extends FormRequest
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
            'bookingReference' => 'required|string',
            'grandTotal' => 'required|string',
            'cardNumber' => 'required|numeric|digits:16',
            'expireMonth' => 'required|numeric|between:1,12',
            'expireYear' => 'required|string|digits:4|numeric|min:2023|max:2040',   
            'cvcDigits' => 'required|string|digits:3',            
            'supportPackage' => 'nullable|boolean',
            'changableTicket' => 'nullable|boolean',
            'cancellationableTicket' => 'nullable|boolean',
        ];
    }
}
