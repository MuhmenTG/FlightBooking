<?php
namespace App\Services\Amadeus;
 
use App\Helpers\Constants;
use Illuminate\Support\Arr;

//ini_set('max_execution_time', 300);
class AmadeusService implements IAmadeusService {


    public  function AmadeusFlightSearchUrl(
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
      ) : string {
        
        $url = getenv(Constants::SEARCH_FLIGHT_API_URL);
        
        $queryParams = [
          Constants::ORIGIN_LOCATION_CODE => $originLocationCode,
          Constants::DESTINATION_LOCATION_CODE => $destinationLocationCode,   
          Constants::DEPARTURE_DATE => $departureDate,
          Constants::ADULTS => $numberOfAdults,
          Constants::CURRENCY => Constants::CURRENCY_CODE
        ];
      
        if ($returnDate !== null) {
            $queryParams[Constants::RETURN_DATE] = $returnDate;
        }

        if ($children !== null) {
            $queryParams[Constants::CHILDREN] = $children;
        }

        if ($infant !== null) {
            $queryParams[Constants::INFANTS] = $infant;
        }

        if ($travelClass !== null) {
            $queryParams[Constants::TRAVEL_CLASS] = $travelClass;
        }

        if ($includedAirlineCodes !== null) {
            $queryParams[Constants::INCLUDED_AIRLINE_CODES] = $includedAirlineCodes;
        }

        if ($excludedAirlineCodes !== null) {
            $queryParams[Constants::EXCLUDED_AIRLINE_CODES] = $excludedAirlineCodes;
        }

        if ($nonStop !== null) {
            if ($nonStop) {
                $queryParams[Constants::NON_STOP] = Constants::TRUE;
            } else {
                $queryParams[Constants::NON_STOP] = Constants::FALSE;
            }
        }

        $params = Arr::query($queryParams);
        $url .= '?' . $params;

        return $url;
    }
    
    public function AmadeusCitySearchUrl(string $cityKeyWord) : string {
        $url = getenv(Constants::SEARCH_CITY_LOCATIONS_URL);
        
        $queryParams = [
            Constants::KEY_WORD_CITY => $cityKeyWord
        ];

        $params = Arr::query($queryParams);
        
        $url .= '&' . $params;

        return $url;
    }


    public function prepareFlightOfferDataForAmadeusValidating(array $jsonFlightData) : array
    {
        $data = [
            Constants::FLIGHT_DATA => [
                "type" => Constants::FLIGHT_OFFERS_PRICING,
                "flightOffers" => [$jsonFlightData]
            ]
        ];

        return $data;
    }
}
