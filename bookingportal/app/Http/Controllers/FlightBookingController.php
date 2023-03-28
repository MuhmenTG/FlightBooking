<?php

namespace App\Http\Controllers;

use App\Factories\BookingFactory;
use App\Factories\PaymentFactory;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;


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

    public function searchFlights(Request $request) 
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

        $response = $this->httpRequest($url, $accessToken, "get");

        if($response == null){
            return response()->json("No flight result found", 404);
        }

        return $response;
    }

    public function chooseFlightOffer(Request $request) 
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';

        $jsonFlightData = $request->json()->all();
        $accessToken = $request->bearerToken();


        $data = array(
            "data" => array(
                "type" => "flight-offers-pricing",
                "flightOffers" => [$jsonFlightData]
            )
        );


        $response = $this->httpRequest($url, $accessToken, "post", $data);
        if($response){
            return $response;
        }
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

        $calculatedTotalPrice = BookingFactory::getTotalPrice($flightData);

        
       // $transaction = PaymentFactory::createCharge($calculatedTotalPrice, "dkk", $cardNumber, $expireYear, $expireMonth, $cvcDigts, $bookingReferenceNumber);

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


