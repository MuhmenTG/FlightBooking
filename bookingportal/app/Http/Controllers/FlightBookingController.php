<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use App\Services\AmadeusService;
use App\Services\BookingService;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
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
            return ResponseHelper::validationErrorResponse($validator->errors());
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
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FLIGHTS_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
    
        return $amadeusResponse;
    }

    public function chooseFlightOffer(Request $request)
    {
        $jsonFlightData = $request->json()->all();
              
        $accessToken = $request->bearerToken();

        if (empty($jsonFlightData)) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::EMPTY_FLIGHT_ARRAY, Response::HTTP_BAD_REQUEST);
        }

        try {
            $amadeusResponse = AmadeusService::AmadeusChooseFlightOffer($jsonFlightData, $accessToken);
            return $amadeusResponse;
        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }     
    }
    
    public function FlightConfirmation(Request $request)
    {
        $validator = ValidationHelper::validateFlightConfirmationRequest($request);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        try {
            $bookedFlight = BookingService::bookFlight($request->json()->all());

            return ResponseHelper::jsonResponseMessage($bookedFlight, Response::HTTP_OK);

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function payFlightConfirmation(Request $request)
    {
        $validator = ValidationHelper::validateFlightPayRequest($request);
        
        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $bookingReference = $request->input('bookingReference');
        $cardNumber = $request->input('cardNumber');
        $expireMonth = $request->input('expireMonth');
        $expireYear = $request->input('expireYear');
        $cvcDigits = $request->input('cvcDigits');
        $grandTotal = intval($request->input('grandTotal'));

        $ticketRecord = FlightBooking::ByBookingReference($bookingReference)->first();
        $airlineTicketNumberIssuer = $ticketRecord->getAirline();

        $unTicketedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();

        foreach ($unTicketedPassengers as $passenger) {
            $ticketNumber = BookingService::generateTicketNumber($airlineTicketNumberIssuer);
        
            $passenger->setTicketNumber($ticketNumber);
            $passenger->save();
        }

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
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return ResponseHelper::jsonResponseMessage($booking, Response::HTTP_OK);
    }

}


