<?php

namespace App\DTO;

class HotelSelectionDTO
{
    private bool $available;
    private string $hotelId;
    private string $name;
    private string $cityCode;
    private string $countryCode;
    private array $amenities;
    private string $checkInDate;
    private string $checkOutDate;
    private string $rateCode;
    private string $rateFamilyEstimatedCode;
    private string $rateFamilyEstimatedType;
    private string $category;
    private string $description;
    private ?float $commissionPercentage;
    private string $roomType;
    private int $guestsAdults;
    private string $priceCurrency;
    private float $priceBase;
    private float $priceTotal;
    private array $priceTaxes;
    private string $policiesGuaranteePaymentType;
    private string $policiesCheckInOutCheckIn;
    private string $policiesCheckInOutCheckOut;
    private string $policiesCancellationDeadline;

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
