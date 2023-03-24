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
        $this->available = isset($data['data']['available']) ? (bool) $data['data']['available'] : "";
        $this->hotelId = isset($data['data']['hotel']['hotelId']) ? (string) $data['data']['hotel']['hotelId'] : "";
        $this->name = isset($data['data']['hotel']['name']) ? (string) $data['data']['hotel']['name'] : "";
        $this->cityCode = isset($data['data']['hotel']['cityCode']) ? (string) $data['data']['hotel']['cityCode'] : "";
        $this->countryCode = isset($data['data']['hotel']['address']['countryCode']) ? (string) $data['data']['hotel']['address']['countryCode'] : "";
        $this->amenities = isset($data['data']['hotel']['amenities']) ? (array) $data['data']['hotel']['amenities'] : "";
        $this->checkInDate = isset($data['data']['offers'][0]['checkInDate']) ? (string) $data['data']['offers'][0]['checkInDate'] : "";
        $this->checkOutDate = isset($data['data']['offers'][0]['checkOutDate']) ? (string) $data['data']['offers'][0]['checkOutDate'] : "";
        $this->rateCode = isset($data['data']['offers'][0]['rateCode']) ? (string) $data['data']['offers'][0]['rateCode'] : "";
        $this->rateFamilyEstimatedCode = isset($data['data']['offers'][0]['rateFamilyEstimated']['code']) ? (string) $data['data']['offers'][0]['rateFamilyEstimated']['code'] : "";
        $this->rateFamilyEstimatedType = isset($data['data']['offers'][0]['rateFamilyEstimated']['type']) ? (string) $data['data']['offers'][0]['rateFamilyEstimated']['type'] : "";
        $this->description =  isset($data['data']['offers'][0]['description']['text']) ? (string) $data['data']['offers'][0]['description']['text'] : "";
        $this->commissionPercentage = (float) isset($data['data']['offers'][0]['commission']['percentage']) ? (float) $data['data']['offers'][0]['commission']['percentage'] : null;
        $this->roomType = isset($data['data']['offers'][0]['room']['type']) ? (string) $data['data']['offers'][0]['room']['type'] : "";
        $this->guestsAdults = isset($data['data']['offers'][0]['guests']['adults']) ? $data['data']['offers'][0]['guests']['adults'] : null;
        $this->priceCurrency = isset($data['data']['offers'][0]['price']['currency']) ? $data['data']['offers'][0]['price']['currency'] : "";
        $this->priceBase = (float) isset($data['data']['offers'][0]['price']['base']) ? $data['data']['offers'][0]['price']['base'] : null;
        $this->priceTotal = (float) isset($data['data']['offers'][0]['price']['total']) ? $data['data']['offers'][0]['price']['total'] : null;
        $this->priceTaxes = isset($data['data']['offers'][0]['price']['taxes']) ? $data['data']['offers'][0]['price']['taxes'] : [];
        $this->policiesCheckInOutCheckIn = isset($data['data']['offers'][0]['policies']['checkInOut']['checkin']) ? $data['data']['offers'][0]['policies']['checkInOut']['checkin'] : "";
        $this->policiesCheckInOutCheckOut = isset($data['data']['offers'][0]['policies']['checkInOut']['checkout']) ? $data['data']['offers'][0]['policies']['checkInOut']['checkout'] : "";
        $this->policiesCancellationDeadline = isset($data['data']['offers'][0]['policies']['cancellation']['deadline']) ? $data['data']['offers'][0]['policies']['cancellation']['deadline'] : "";
        
    }
}