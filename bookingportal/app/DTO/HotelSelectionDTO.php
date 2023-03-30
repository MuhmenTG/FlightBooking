<?php

namespace App\DTO;

class HotelSelectionDTO
{
    public bool $available;
    public string $hotelId;
    public string $name;
    public string $cityCode;
    public string $countryCode;
    public array $amenities;
    public string $checkInDate;
    public string $checkOutDate;
    public string $rateCode;
    public string $rateFamilyEstimatedCode;
    public string $rateFamilyEstimatedType;
    public string $category;
    public string $description;
    public ?float $commissionPercentage;
    public string $roomType;
    public int $guestsAdults;
    public string $priceCurrency;
    public float $priceBase;
    public float $priceTotal;
    public array $priceTaxes;
    public string $policiesGuaranteePaymentType;
    public string $policiesCheckInOutCheckIn;
    public string $policiesCheckInOutCheckOut;
    public string $policiesCancellationDeadline;

    public function __construct(array $data)
    {
        $hotelData = $data['data']['hotel'];
        $offerData = $data['data']['offers'][0];

        $this->available = $data['data']['available'] ?? false;
        $this->hotelId = $hotelData['hotelId'] ?? '';
        $this->name = $hotelData['name'] ?? '';
        $this->cityCode = $hotelData['cityCode'] ?? '';
        $this->countryCode = $hotelData['address']['countryCode'] ?? '';
        $this->amenities = $hotelData['amenities'] ?? [];
        $this->checkInDate = $offerData['checkInDate'] ?? '';
        $this->checkOutDate = $offerData['checkOutDate'] ?? '';
        $this->rateCode = $offerData['rateCode'] ?? '';
        $this->rateFamilyEstimatedCode = $offerData['rateFamilyEstimated']['code'] ?? '';
        $this->rateFamilyEstimatedType = $offerData['rateFamilyEstimated']['type'] ?? '';
        $this->description = $offerData['description']['text'] ?? '';
        $this->commissionPercentage = isset($offerData['commission']['percentage']) ? (float) $offerData['commission']['percentage'] : null;
        $this->roomType = $offerData['room']['type'] ?? '';
        $this->guestsAdults = $offerData['guests']['adults'] ?? 0;
        $this->priceCurrency = $offerData['price']['currency'] ?? '';
        $this->priceBase = (float) $offerData['price']['base'] ?? 0;
        $this->priceTotal = (float) $offerData['price']['total'] ?? 0;
        $this->priceTaxes = $offerData['price']['taxes'] ?? [];
        $this->policiesCheckInOutCheckIn = $offerData['policies']['checkInOut']['checkin'] ?? '';
        $this->policiesCheckInOutCheckOut = $offerData['policies']['checkInOut']['checkout'] ?? '';
        $this->policiesCancellationDeadline = $offerData['policies']['cancellation']['deadline'] ?? '';
    }
}
