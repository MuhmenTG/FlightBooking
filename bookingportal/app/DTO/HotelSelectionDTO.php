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
    public float $commissionPercentage;
    public string $roomType;
    public string $roomTypeEstimatedCategory;
    public int $roomTypeEstimatedBeds;
    public string $roomTypeEstimatedBedType;
    public int $guestsAdults;
    public string $priceCurrency;
    public float $priceBase;
    public float $priceTotal;
    public array $priceTaxes;
    public string $policiesGuaranteePaymentType;
    public array $policiesGuaranteeAcceptedPaymentsCreditCards;
    public array $policiesGuaranteeAcceptedPaymentsMethods;
    public string $policiesCheckInOutCheckIn;
    public string $policiesCheckInOutCheckOut;
    public string $policiesCancellationDeadline;

    public function __construct(array $data)
    {
        $this->available = $data['data']['available'];
        $this->hotelId = $data['data']['hotel']['hotelId'];
        $this->name = $data['data']['hotel']['name'];
        $this->cityCode = $data['data']['hotel']['cityCode'];
        $this->countryCode = $data['data']['hotel']['address']['countryCode'];
        $this->amenities = $data['data']['hotel']['amenities'];
        $this->checkInDate = $data['data']['offers'][0]['checkInDate'];
        $this->checkOutDate = $data['data']['offers'][0]['checkOutDate'];
        $this->rateCode = $data['data']['offers'][0]['rateCode'];
        $this->rateFamilyEstimatedCode = $data['data']['offers'][0]['rateFamilyEstimated']['code'];
        $this->rateFamilyEstimatedType = $data['data']['offers'][0]['rateFamilyEstimated']['type'];
        $this->category = $data['data']['offers'][0]['category'];
        $this->description = $data['data']['offers'][0]['description']['text'];
        $this->commissionPercentage = (float) $data['data']['offers'][0]['commission']['percentage'];
        $this->roomType = $data['data']['offers'][0]['room']['type'];
        $this->roomTypeEstimatedCategory = $data['data']['offers'][0]['room']['typeEstimated']['category'];
        $this->roomTypeEstimatedBeds = $data['data']['offers'][0]['room']['typeEstimated']['beds'];
        $this->roomTypeEstimatedBedType = $data['data']['offers'][0]['room']['typeEstimated']['bedType'];
        $this->guestsAdults = $data['data']['offers'][0]['guests']['adults'];
        $this->priceCurrency = $data['data']['offers'][0]['price']['currency'];
        $this->priceBase = (float) $data['data']['offers'][0]['price']['base'];
        $this->priceTotal = (float) $data['data']['offers'][0]['price']['total'];
        $this->priceTaxes = $data['data']['offers'][0]['price']['taxes'];
        $this->policiesGuaranteePaymentType = $data['data']['offers'][0]['policies']['guarantee']['paymentType'] ?? "";
        /*$this->policiesGuaranteeAcceptedPaymentsCreditCards = $data['data']['offers'][0]['policies']['guarantee']['acceptedPayments']['creditCards'];
        $this->policiesGuaranteeAcceptedPaymentsMethods = $data['data']['offers'][0]['policies']['guarantee']['acceptedPayments']['methods'];
        $this->policiesCheckInOutCheckIn = $data['data']['offers'][0]['policies']['checkInOut']['checkin'];
        $this->policiesCheckInOutCheckOut = $data['data']['offers'][0]['policies']['checkInOut']['checkout'];
        $this->policiesCancellationDeadline = $data['data']['offers'][0]['policies']['cancellation']['deadline'];

        */
    }
}