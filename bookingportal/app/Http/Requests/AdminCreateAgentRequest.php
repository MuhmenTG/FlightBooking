<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateAgentRequest extends FormRequest
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
            //
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email',
            'status' => 'required|string',
            'isAdmin' => 'required|boolean',
            'isAgent' => 'required|boolean',
        ];
    }
}