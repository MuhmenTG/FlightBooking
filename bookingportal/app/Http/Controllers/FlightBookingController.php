<?php

namespace App\Http\Controllers;

use App\DTO\FlightSelectionDTO;
use App\Factories\BookingFactory;
use App\Factories\PaymentFactory;
use App\Mail\SendEmail;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
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
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
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
            return response()->json("No flight result found", Response::HTTP_NOT_FOUND);
        }

        return $response;
    }

    public function chooseFlightOffer(Request $request)
    {
        $url = 'https://test.api.amadeus.com/v1/shopping/flight-offers/pricing';
    
        $jsonFlightData = $request->json()->all();
        $accessToken = $request->bearerToken();
    
        if (empty($jsonFlightData)) {
            return response()->json(['message' => 'Empty flight data'], Response::HTTP_BAD_REQUEST);
        }
    
        $data = array(
            "data" => array(
                "type" => "flight-offers-pricing",
                "flightOffers" => [$jsonFlightData]
            )
        );
    
        try {
            $response = $this->httpRequest($url, $accessToken, "POST", $data);
            if ($response) {
                return $response;
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Request failed', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }
    

    public function FlightConfirmation(Request $request)
    {
        
        $flightData = $request->json()->all();

        $bookingReferenceNumber = BookingFactory::generateBookingReference();
        
        $passengers = BookingFactory::createPassengerRecord($flightData["passengers"], $bookingReferenceNumber);
        if(!$passengers){
            return response()->json('Could not create passenger record', Response::HTTP_BAD_REQUEST);
        }

        $flightSegments = BookingFactory::createFlightBookingRecord($flightData, $bookingReferenceNumber);
        if(!$flightSegments){
            return response()->json('Could not create flight segments record', Response::HTTP_BAD_REQUEST);
        }
         
        $booking = [
            'success' => true,
            'bookingReference' => $bookingReferenceNumber
        ];

        return response()->json($booking, 200);
        
    }

    public function payFlightConfirmation(Request $request){
        
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


        $unPaidflightBooking = FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 0)->get();
        $bookedPassengers = PassengerInfo::ByBookingReference($bookingReference)->get();
        foreach($bookedPassengers as $bookedPassenger){
            $email = $bookedPassenger->getEmail();
        }

        if($unPaidflightBooking->count() == 0){
            return response()->json('Invalid booking', Response::HTTP_NOT_FOUND);
        }

        $transaction = PaymentFactory::createCharge($grandTotal, "dkk", $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReference);
        if(!$transaction){
            return response()->json('Could not create transaction', Response::HTTP_BAD_REQUEST);
        }

        FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update([FlightBooking::COL_ISPAID => 1]);
        $paidflightBooking = FlightBooking::ByBookingReference($bookingReference)->where(FlightBooking::COL_ISPAID, 1)->get();

        $booking = [
            'success' => true,
            'itinerary' => $paidflightBooking,
            'passengers' => $bookedPassengers,
            'transaction' => $transaction
        ];


        SendEmail::sendEmailWithAttachments("Muhmen", $email, $bookingReference);
    

        return response()->json($booking, 200);

        
    }

}


