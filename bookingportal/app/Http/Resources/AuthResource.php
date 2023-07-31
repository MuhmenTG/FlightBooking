<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'user' => [
                'id' => $this->id,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'emailConfirmation' => $this->emailConfirmation,
                'status' => $this->status,
                'isAgent' => $this->isAgent,
                'isAdmin' => $this->isAdmin,
                'firstTimeLoggedIn' => $this->firstTimeLoggedIn,
                'registeredAt' => $this->registeredAt,
                'deactivatedAt' => $this->deactivatedAt,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ],
            'token' => $this->token,
        ];
    }
}
