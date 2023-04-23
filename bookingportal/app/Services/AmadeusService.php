<?php
namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class AmadeusService {

    public static function AmadeusSearch(string $originLocationCode, string $destinationLocationCode, string $departureDate, 
    string $returnDate, string $numberOfAdults, string $accessToken){
         
        $url = 'https://test.api.amadeus.com/v2/shopping/flight-offers';

        $queryParams = [
            'originLocationCode' => $originLocationCode,
            'destinationLocationCode' => $destinationLocationCode,   
            'departureDate' => $departureDate,
            'returnDate' => $returnDate,
            'adults' => $numberOfAdults
        ];
    
        $searchData = Arr ::query($queryParams);
        $url .= '?' . $searchData;
    
        $accessToken = "qFocfRbQIyxYglLazG1nWBsqO3qv";
    
    
        $response = AmadeusService::httpRequest($url, $accessToken, "GET");
    
        if($response == null){
            return false;
        }
    }

    public static function AmadeusChooseFlightOffer(array $jsonFlightData, $accessToken)
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';
     
        $data = array(
            "data" => array(
                "type" => "flight-offers-pricing",
                "flightOffers" => [$jsonFlightData]
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
            'currency'      => 'DKK'
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


    private static function httpRequest(string $url, string $accessTtoken, string $method = 'GET',  array $data = null)
    {  
        $client = new \GuzzleHttp\Client();
        try {
            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $accessTtoken
            ];

            if ($method === 'GET') {
                $response = $client->get($url, [
                    'headers' => $headers,
                ]);
            } else {
                $headers['Content-Type'] = 'application/json';
                $headers['X-HTTP-Method-Override'] = 'GET';

                $response = $client->post($url, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            }
            if($response->getStatusCode() == 200){
                return $response->getBody();
            }
            return response()->json("Could not find", 404);
        } catch (GuzzleException $exception) {
            print($exception);
            return null;
        }
        
    }
}