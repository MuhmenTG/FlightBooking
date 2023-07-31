<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupportRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'supportRequestId' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'bookingreference' => $this->bookingreference,
            'message' => $this->message,
            'sentTime' => $this->time,
            'solvedSatus' => $this->isSolved,
        ];
    }
}
