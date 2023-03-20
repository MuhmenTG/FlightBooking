<?php

namespace App\Http\Controllers;

use App\Factories\BookingFactory;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Http\JsonResponse;



class FlightBookingController extends Controller
{

    //We have made this only because there is not any frontend yet, where acess token comes from
    public function getAccessToken()
    {
        $url = 'https://test.api.amadeus.com/v1/security/oauth2/token';

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($url, [
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => 'xlUodVi30L0U8snyBsa1qenY4BNyUjMA',
                    'client_secret' => 'A2GpGXyewfl0G3gu'
                ]
            ]);

            $response = $response->getBody();
            $accessTtoken = json_decode($response)->access_token;
            echo $accessTtoken;
        } catch (GuzzleException $exception) {
            dd($exception);
        }
        
    }

    public function searchFlights(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'originLocationCode'        => 'required|string',
            'destinationLocationCode'   => 'required|string',
            'departureDate'             => 'required|string',
            'returnDate'                => 'nullable|string',
            'adults'                    => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }
        
        $url = 'https://test.api.amadeus.com/v2/shopping/flight-offers';

        $originLocationCode = $request->input('originLocationCode');
        $destinationLocationCode = $request->input('destinationLocationCode');
        $departureDate = $request->input('departureDate');
        $returnDate = $request->input('returnDate');
        $adults =  $request->input('adults');
        $accessToken = $request->bearerToken();

        $data = [
            'originLocationCode'      => $originLocationCode,
            'destinationLocationCode' => $destinationLocationCode,
            'departureDate'           => $departureDate,
            'returnDate'              => $returnDate,
            'adults'                  => $adults
        ];

        $searchData = Arr::query($data);
        $url .= '?' . $searchData;

        $accessTtoken = 'hSj7NAANBJCM1TgKww3GuKp8le66';

        $response = $this->httpRequest($url, $accessToken, "get");

        if($response == null){
            return response()->json("No results found", 404);
        }

        return $response;
    }

    public function selectFlightOffer(Request $request) 
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';

        $jsonFlightData = $request->json()->all();

        $data = array(
            "data" => array(
                "type" => "flight-offers-pricing",
                "flightOffers" => [$jsonFlightData]
            )
        );

        $accessTtoken = 'hSj7NAANBJCM1TgKww3GuKp8le66';

        $response = $this->httpRequest($url, $accessTtoken, "post", $data);

        return $response;
    }

    public function flightConfirmation(Request $request)
    {
        
        /*$validator = Validator::make($request->json(), [
            'passengers' => 'required|array',
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }*/
    
        $flightData = $request->json()->all();

        $bookingReferenceNumber = BookingFactory::generateBookingReference();
        
        $passengers = BookingFactory::createPassengerRecord($flightData["passengers"], $bookingReferenceNumber);
        
        $flightSegments = BookingFactory::createFlightBookingRecord($flightData, $bookingReferenceNumber);

        
        $booking = [
            'success' => true,
            'PAX'  => $passengers,
            'flight' => $flightSegments,    
        ];

        return response()->json($booking, 200);
        
    }

}


