<?php

namespace App\Services\Amadeus;

interface IAmadeusService
{
    public function AmadeusSearchUrl(
        string $originLocationCode,
        string $destinationLocationCode,
        string $departureDate,
        string $numberOfAdults,
        ?string $returnDate = null,
        ?int $children = 0,
        ?int $infant = 0,
        ?string $travelClass = null,
        ?string $includedAirlineCodes = null,
        ?string $excludedAirlineCodes = null,
        ?bool $nonStop = null
    ): string;

    public function prepareFlightOfferDataForAmadeusValidating(array $jsonFlightData): array;

    public function AmadeusGetHotelList(string $cityCode, string $accessToken);

    public function AmadeusGetSpecificHotelsRoomAvailability(
        string $hotelIds,
        string $adults,
        string $checkInDate,
        string $checkOutDate,
        string $roomQuantity,
        ?string $priceRange = null,
        ?string $paymentPolicy = null,
        ?string $boardType = null,
        string $accessToken
    );

    public function AmadeusGetSpecificHotelsRoomAvailability1(
        string $hotelIds,
        string $adults,
        string $checkInDate,
        string $checkOutDate,
        string $roomQuantity,
        ?string $priceRange = null,
        ?string $paymentPolicy = null,
        ?string $boardType = null,
        string $accessToken
    );

    public function reviewSelectedHotelOfferInfo(string $hotelOfferId, string $accessToken);

    public function httpRequest(string $url, string $accessToken, string $method = 'GET', array $data = null);
}
