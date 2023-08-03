<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnquirySupportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'bookingReference' => 'nullable|string',
        ];
    }
}
