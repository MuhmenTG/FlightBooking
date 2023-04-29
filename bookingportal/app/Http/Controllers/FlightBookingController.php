<?php

namespace App\Http\Controllers;

use App\Services\AmadeusService;
use App\Services\BookingService;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

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
            return $accessTtoken;
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
            'adults'                    => 'required|integer',
            'returnDate'                => 'nullable|string',
            'children'                  => 'nullable|integer',
            'infants'                   => 'nullable|integer',
            'travelClass'               => 'nullable|string',
            'includedAirlineCodes'      => 'nullable|string',
            'excludedAirlineCodes'      => 'nullable|string',
            'nonStop'                   => 'nullable|boolean',
            'maxPrice'                  => 'nullable|integer|min:0',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        
        $originLocationCode = $request->input('originLocationCode');
        $destinationLocationCode = $request->input('destinationLocationCode');
        $departureDate = $request->input('departureDate');
        $returnDate = $request->input('returnDate');
        $adults = $request->input('adults');
        $children = intval($request->input('children'));
        $infants = intval($request->input('infants'));
        $travelClass = $request->input('travelClass');
        $includedAirlineCodes = $request->input('includedAirlineCodes');
        $excludedAirlineCodes = $request->input('excludedAirlineCodes');
        // $nonStop = $request->input('nonStop');
        // $maxPrice = intval($request->input('maxPrice'));
    

        $accessToken = $request->bearerToken();
        
        $amadeusResponse = AmadeusService::AmadeusSearch(
            $accessToken,
            $originLocationCode,
            $destinationLocationCode,
            $departureDate,
            $adults,
            $returnDate,
            $children,
            $infants,
            $travelClass,
            $includedAirlineCodes,
            $excludedAirlineCodes,
            // $nonStop,
            // $maxPrice,    
        );

        if(!$amadeusResponse){
            return response()->json(['Could not find any flights'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
        return $amadeusResponse;
    }
    

    public function chooseFlightOffer(Request $request)
    {
        $jsonFlightData = $request->json()->all();
        
        $accessToken = $request->bearerToken();

        if (empty($jsonFlightData)) {
            return response()->json(['message' => 'Empty flight data'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $amadeusResponse = AmadeusService::AmadeusChooseFlightOffer($jsonFlightData, $accessToken);
            return $amadeusResponse;
        } catch (Exception $e) {
            return response()->json(['message' => 'Request failed', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }     
    }
    
    public function FlightConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [    
            'itineraries' => 'required|array',
            'itineraries.*.segments.*.duration' => 'required|string',
            'itineraries.*.segments' => 'required|array',
            'passengers' => 'required|array',
            'passengers.*.title' => 'required|string',
            'passengers.*.firstName' => 'required|string',
            'passengers.*.lastName' => 'required|string',
            'passengers.*.dateOfBirth' => 'required|string',
            'passengers.*.email' => 'required|email',
            'passengers.*.passengerType' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }

        try {
            $bookingReferenceNumber = BookingService::bookFlight($request->json()->all());

            return response()->json($bookingReferenceNumber, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function payFlightConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bookingReference'     => 'required|string',
            'grandTotal'           => 'required|string',
            'cardNumber'           => 'required|string',
            'expireMonth'          => 'required|string',
            'expireYear'           => 'required|string',
            'cvcDigits'            => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }

        $bookingReference = $request->input('bookingReference');
        $cardNumber = $request->input('cardNumber');
        $expireMonth = $request->input('expireMonth');
        $expireYear = $request->input('expireYear');
        $cvcDigits = $request->input('cvcDigits');
        $grandTotal = intval($request->input('grandTotal'));

        try {
            $booking = BookingService::payFlightConfirmation($bookingReference, $cardNumber, $expireMonth, $expireYear, $cvcDigits, $grandTotal);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($booking, Response::HTTP_OK);
    }

}


