<?php
namespace App\Services;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use PhpParser\Node\Expr\Exit_;
ini_set('max_execution_time', 300);
class AmadeusService {

    const CONTENT_TYPE_JSON = 'application/json';
    const AUTHORIZATION_BEARER = 'Bearer ';
    const HEADER_ACCEPT = 'Accept';
    const HEADER_AUTHORIZATION = 'Authorization';
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_HTTP_METHOD_OVERRIDE = 'X-HTTP-Method-Override';
    const HTTP_METHOD_GET = 'GET';
    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_BAD_REQUEST = 400;
    const HTTP_STATUS_UNAUTHORIZED = 401;
    const HTTP_STATUS_NOT_FOUND = 404;
    const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
    const FLIGHT_DATA = 'data';
    const FLIGHT_OFFERS_PRICING = 'flight-offers-pricing';

    public static function AmadeusSearch(
        string $accessToken,
        string $originLocationCode,
        string $destinationLocationCode,
        string $departureDate,
        string $numberOfAdults,
        ?string $returnDate = null,
        ?int $children = null,
        ?int $infant = null,
        ?string $travelClass = null,
        ?string $includedAirlineCodes = null,
        ?string $excludedAirlineCodes = null,
        bool $nonStop = null
      ) {
        
        $url = 'https://test.api.amadeus.com/v2/shopping/flight-offers';
        $queryParams = [
          'originLocationCode' => $originLocationCode,
          'destinationLocationCode' => $destinationLocationCode,   
          'departureDate' => $departureDate,
          'adults' => $numberOfAdults,
          'currencyCode' => 'DKK'
        ];
      
        if ($returnDate !== null) {
          $queryParams['returnDate'] = $returnDate;
        }
        
        if ($children !== null) {
          $queryParams['children'] = $children;
        }
      
        if ($infant !== null) {
          $queryParams['infants'] = $infant;
        }
      
        if ($travelClass !== null) {
          $queryParams['travelClass'] = $travelClass;
        }
      
        if ($includedAirlineCodes !== null) {
          $queryParams['includedAirlineCodes'] = $includedAirlineCodes;
        }
      
        if ($excludedAirlineCodes !== null) {
          $queryParams['excludedAirlineCodes'] = $excludedAirlineCodes;
        }
   
        if ($nonStop !== null) {
            if ($nonStop) {
                $queryParams['nonStop'] = 'true';
            } else {
                $queryParams['nonStop'] = 'false';
            }
        }

        $searchData = Arr::query($queryParams);
        $url .= '?' . $searchData;
      
        $response = AmadeusService::httpRequest($url, $accessToken, "GET");
      
        if($response == null){
          return false;
        }
      
        return $response;
      }      

    public static function AmadeusChooseFlightOffer(array $jsonFlightData, string $accessToken)
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';
     
        $data = array(
            self::FLIGHT_DATA => array(
                "type" => self::FLIGHT_OFFERS_PRICING,
                "flightOffers" => array($jsonFlightData)
            )
        );

        $response = AmadeusService::httpRequest($url, $accessToken, "POST", $data);
        if ($response) {
            return $response;
        }
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

    public static function httpRequest(string $url, string $accessToken, string $method = self::HTTP_METHOD_GET, array $data = null)
    {  
        $client = new \GuzzleHttp\Client();
        try {
            $headers = [
                self::HEADER_ACCEPT => self::CONTENT_TYPE_JSON,
                self::HEADER_AUTHORIZATION => self::AUTHORIZATION_BEARER . $accessToken,
            ];

            if ($method === self::HTTP_METHOD_GET) {
                $response = $client->get($url, [
                    'headers' => $headers,
                ]);
            } else {
                $headers[self::HEADER_CONTENT_TYPE] = self::CONTENT_TYPE_JSON;
                $headers[self::HEADER_HTTP_METHOD_OVERRIDE] = self::HTTP_METHOD_GET;

                $response = $client->post($url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            }

            switch ($response->getStatusCode()) {
                case self::HTTP_STATUS_OK:
                    return $response->getBody();
                case self::HTTP_STATUS_BAD_REQUEST:
                    return response()->json(['error' => 'Choose another flight']);
                case self::HTTP_STATUS_UNAUTHORIZED:
                    return response()->json(['error' => 'Unauthorized']);
                case self::HTTP_STATUS_NOT_FOUND:
                    return response()->json(['error' => 'Not Found']);
                default:
                    return response()->json(['error' => 'Something went wrong']);
            }
        } catch (GuzzleException $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

}