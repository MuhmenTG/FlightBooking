<?php
namespace App\Services;
 
use App\DTO\AmadeusFlightOfferData as DTOAmadeusFlightOfferData;
use App\Helpers\Constants;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use PHPUnit\TextUI\Configuration\Constant;

//ini_set('max_execution_time', 300);
class AmadeusService {


    public static function AmadeusSearchUrl(
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

    public static function prepareFlightOfferDataForAmadeusValidating(array $jsonFlightData) : array
    {
        $data = [
            Constants::FLIGHT_DATA => [
                "type" => Constants::FLIGHT_OFFERS_PRICING,
                "flightOffers" => [$jsonFlightData]
            ]
        ];

        return $data;
    }

    public static function AmadeusGetHotelList(string $cityCode, string $accessToken){
        
        $listOfHotelByCityUrl = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city";
        $data = ['cityCode' => $cityCode];
        $searchData = Arr::query($data);
        $listOfHotelByCityUrl .= '?' . $searchData;

        $hotelResponse = AmadeusService::httpRequest($listOfHotelByCityUrl, $accessToken);

        if (empty($hotelResponse)) {
            return response()->json(['message' => 'Error retrieving hotel data'], 500);
        }

        $hotelResponse = json_decode($hotelResponse, true);

        if (!isset($hotelResponse['data'])) {
            return response()->json(['message' => 'No hotels found in the specified city'], 404);
        }

        $hotelIds = implode(',', array_map(function ($item) {
            return $item['hotelId'];
        }, $hotelResponse['data']));

        return $hotelIds;

    }

    public static function AmadeusGetSpecificHotelsRoomAvailability(string $hotelIds, string $adults, string $checkInDate, string $checkOutDate, string $roomQuantity, string $priceRange = null,
    string $paymentPolicy = null, string $boardType = null,  string $accessToken)
    {
       
        $isCommaSeparatedString = implode(",", explode(",", $hotelIds)) === $hotelIds;

        if (!$isCommaSeparatedString || empty($hotelIds)) {
            throw new InvalidArgumentException("Invalid hotelIds parameter. Expecting a non-empty array.");
        }

        if (!is_numeric($adults) || $adults < 1) {
            throw new InvalidArgumentException("Invalid adults parameter. Expecting a positive integer.");
        }

        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $checkInDate)) {
            throw new InvalidArgumentException("Invalid checkInDate parameter. Expecting date format yyyy-mm-dd.");
        }
    
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $checkOutDate)) {
            throw new InvalidArgumentException("Invalid checkOutDate parameter. Expecting date format yyyy-mm-dd.");
        }
    
        $specificHotelOfferUrl = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        $data = [
            'hotelIds'      => $hotelIds,
            'adults'        => $adults,
            'checkInDate'   => $checkInDate,
            'checkOutDate'  => $checkOutDate,
            'roomQuantity'  => $roomQuantity,
            'currencyCode'  => 'DKK'
        ];

        if ($priceRange !== null) {
            $data['priceRange'] = $priceRange;
        }
    
        if ($paymentPolicy !== null) {
            $data['paymentPolicy'] = $paymentPolicy;
        }
    
        if ($boardType !== null) {
            $data['boardType'] = $boardType;
        }

        $searchData = Arr::query($data);
        $specificHotelOfferUrl .= '?' . $searchData;

        $response = AmadeusService::httpRequest($specificHotelOfferUrl, $accessToken);
        if($response !== 400){
            
            return $response;
        }
    }

    public static function AmadeusGetSpecificHotelsRoomAvailability1(string $hotelIds, string $adults, string $checkInDate, string $checkOutDate, string $roomQuantity, string $priceRange = null,
    string $paymentPolicy = null, string $boardType = null, string $accessToken)
{

    $hotelIdsString = trim($hotelIds, ","); // Remove trailing commas from the hotelIds string
    

    $hotelIdChunks = explode(",", $hotelIdsString); // Split the hotelIds string into chunks of 30

    $finalHotelList = [];

    $numChunks = ceil(count($hotelIdChunks) / 30); // Calculate the number of chunks

    for ($i = 0; $i < $numChunks; $i++) {
        $chunk = array_slice($hotelIdChunks, $i * 30, 30); // Get the next chunk of 30 hotel IDs
        
        $chunkedHotelIdsString = implode(",", $chunk);

        if (!is_numeric($adults) || $adults < 1) {
            throw new InvalidArgumentException("Invalid adults parameter. Expecting a positive integer.");
        }

        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $checkInDate)) {
            throw new InvalidArgumentException("Invalid checkInDate parameter. Expecting date format yyyy-mm-dd.");
        }

        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $checkOutDate)) {
            throw new InvalidArgumentException("Invalid checkOutDate parameter. Expecting date format yyyy-mm-dd.");
        }

        $specificHotelOfferUrl = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        $data = [
            'hotelIds'      => $chunkedHotelIdsString,
            'adults'        => $adults,
            'checkInDate'   => $checkInDate,
            'checkOutDate'  => $checkOutDate,
            'roomQuantity'  => $roomQuantity,
            'currencyCode'  => 'DKK'
        ];

        if ($priceRange !== null) {
            $data['priceRange'] = $priceRange;
        }

        if ($paymentPolicy !== null) {
            $data['paymentPolicy'] = $paymentPolicy;
        }

        if ($boardType !== null) {
            $data['boardType'] = $boardType;
        }

        $searchData = Arr::query($data);
        $specificHotelOfferUrl .= '?' . $searchData;

        echo $chunkedHotelIdsString;exit;
        $response = AmadeusService::httpRequest($specificHotelOfferUrl, $accessToken);

        if ($response !== 200) {
            throw new Exception("Error getting hotel offers: " );
        }

        $hotels = json_decode($response, true);

        $finalHotelList = array_merge($finalHotelList, $hotels);
    }

    return $finalHotelList;
}


    public static function reviewSelectedHotelOfferInfo(string $hotelOfferId, string $accessToken)
    {

        $url = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        if($hotelOfferId == null){
            throw new InvalidArgumentException("Invalid hotelOfferId found");
        }

        $url .= '/' . $hotelOfferId;

        $response = AmadeusService::httpRequest($url, $accessToken);
        if($response){
            return $response;
        }
    }

    public static function httpRequest(string $url, string $accessToken, string $method = Constants::HTTP_METHOD_GET, array $data = null)
    {  
        $client = new \GuzzleHttp\Client();
        try {
            $headers = [
                Constants::HEADER_ACCEPT => Constants::CONTENT_TYPE_JSON,
                Constants::HEADER_AUTHORIZATION => Constants::AUTHORIZATION_BEARER . $accessToken,
            ];

            if ($method === Constants::HTTP_METHOD_GET) {
                $response = $client->get($url, [
                    'headers' => $headers,
                ]);
            } else {
                $headers[Constants::HEADER_CONTENT_TYPE] = Constants::CONTENT_TYPE_JSON;
                $headers[Constants::HEADER_HTTP_METHOD_OVERRIDE] = Constants::HTTP_METHOD_GET;

                $response = $client->post($url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            }

            switch ($response->getStatusCode()) {
                case Constants::HTTP_STATUS_OK:
                    return $response->getBody();
                case Constants::HTTP_STATUS_BAD_REQUEST:
                    return response()->json(['error' => 'Choose another flight']);
                case Constants::HTTP_STATUS_UNAUTHORIZED:
                    return response()->json(['error' => 'Unauthorized']);
                case Constants::HTTP_STATUS_NOT_FOUND:
                    return response()->json(['error' => 'Not Found']);
                default:
                    return response()->json(['error' => 'Something went wrong']);
            }
        } catch (GuzzleException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

}