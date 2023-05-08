<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationHelper;
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
        $validator = ValidationHelper::validateFlightSearchRequest($request);
        
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
        $nonStop = boolval($request->input('nonStop'));
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
            $nonStop  
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
        $validator = ValidationHelper::validateFlightConfirmationRequest($request);

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
        $validator = ValidationHelper::validateFlightPayRequest($request);
        
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }

        $bookingReference = $request->input('bookingReference');
        $cardNumber = $request->input('cardNumber');
        $expireMonth = $request->input('expireMonth');
        $expireYear = $request->input('expireYear');
        $cvcDigits = $request->input('cvcDigits');
        $grandTotal = intval($request->input('grandTotal'));

        if ($request->input('supportPackage')) {
            $grandTotal += 750;
        }

        if ($request->input('changableTicket')) {
            $grandTotal += 750;
        }
        
        if ($request->input('cancellationableTicket')) {
            $grandTotal += 750;
        }
        
        try {
            $booking = BookingService::payFlightConfirmation($bookingReference, $cardNumber, $expireMonth, $expireYear, $cvcDigits, $grandTotal);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($booking, Response::HTTP_OK);
    }

}


