<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'transactionDate' => $this->transactionDate,
            'paymentAmount' => $this->paymentAmount,
            'paymentCurrency' => $this->paymentCurrency,
            'paymentType' => $this->paymentType,
            'paymentStatus' => $this->paymentStatus,
            'paymentMethod' => $this->paymentMethod,
            'paymentGatewayProcessor' => $this->paymentGatewayProcessor,
            'inConnectionWithBookingReference' => $this->connectedBookingReference,
        ];
    }
}
