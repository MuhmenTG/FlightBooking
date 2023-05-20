<?php

namespace App\Services\Amadeus;

interface IAmadeusService
{

    /**
    * Generate the Amadeus search URL based on the provided parameters.
    *
    * @param string $originLocationCode The origin location code.
    * @param string $destinationLocationCode The destination location code.
    * @param string $departureDate The departure date.
    * @param string $numberOfAdults The number of adults.
    * @param string|null $returnDate The return date (optional).
    * @param int|null $children The number of children (optional).
    * @param int|null $infant The number of infants (optional).
    * @param string|null $travelClass The travel class (optional).
    * @param string|null $includedAirlineCodes The included airline codes (optional).
    * @param string|null $excludedAirlineCodes The excluded airline codes (optional).
    * @param bool|null $nonStop The non-stop flag (optional).
    * @return string The generated Amadeus search URL.
    */
    public function AmadeusFlightSearchUrl(
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

    /** Prepare flight offer data for Amadeus validation.
    *
    * @param array $jsonFlightData The flight data in JSON format.
    * @return array The formatted flight offer data.
    */
    public function prepareFlightOfferDataForAmadeusValidating(array $jsonFlightData): array;

    /**
    * Get the list of hotels from Amadeus API for the specified city code.
    *
    * @param string $cityCode The city code.
    * @param string $accessToken The access token.
    * @return mixed The hotel list.
    */
    public function AmadeusHotelListUrl(string $cityCode) : string;

    public function AmadeusSpecificHotelsRoomAvailabilityUrl(
        string $hotelIds,
        string $adults,
        string $checkInDate,
        string $checkOutDate,
        string $roomQuantity,
        ?string $priceRange = null,
        ?string $paymentPolicy = null,
        ?string $boardType = null
    );

    public function reviewSelectedHotelOfferInfo(string $hotelOfferId, string $accessToken);
}
