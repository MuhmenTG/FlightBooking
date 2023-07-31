<?php
//declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Resources\FlightConfirmationResource;
use App\Http\Resources\PassengerResource;
use App\Http\Resources\PaymentResource;
use App\Services\Amadeus\IAmadeusService;
use App\Services\Booking\IBookingService;
use App\Services\Payment\IPaymentService;
use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

class FlightBookingController extends Controller
{

    protected $IBookingService;
    protected $IAmadeusService;
    protected $IPaymentService;

    public function __construct(IBookingService $IbookingService,  IAmadeusService $IAmadeusService, IPaymentService $IPaymentService)
    {
        $this->IBookingService = $IbookingService;
        $this->IAmadeusService = $IAmadeusService;
        $this->IPaymentService = $IPaymentService;
    }

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

    /**
    * Search flights based on the request parameters.
    *
    * @param Request $request The HTTP request object.
    * @return mixed The search results.
    */
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
        $children = $request->input('children') !== null ? (int)$request->input('children') : 0;
        $infants = $request->input('infants') !== null ? (int)$request->input('infants') : 0;
        $travelClass = $request->input('travelClass');
        $includedAirlineCodes = $request->input('includedAirlineCodes');
        $excludedAirlineCodes = $request->input('excludedAirlineCodes');
        $nonStop = boolval($request->input('nonStop'));
        $accessToken = $request->bearerToken();
        
        $constructedSearchUrl = $this->IAmadeusService->AmadeusFlightSearchUrl(
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
        
        $data = $this->sendhttpRequest($constructedSearchUrl, $accessToken);
        return $data;
        
    }

    /**
    * Choose a flight offer based on the request data.
    *
    * @param Request $request The HTTP request object.
    * @return mixed The chosen flight offer.
    */
    public function chooseFlightOffer(Request $request)
    {
        $jsonFlightData = $request->json()->all();
        $accessToken = $request->bearerToken();
              
        if (empty($jsonFlightData)) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::EMPTY_FLIGHT_ARRAY, Response::HTTP_BAD_REQUEST);
        }

        try {
            $selectedFormatedFlightOption = $this->IAmadeusService->prepareFlightOfferDataForAmadeusValidating($jsonFlightData);
            return $this->sendhttpRequest(getenv('CHOOSE_FLIGHT_API_URL'), $accessToken, self::HTTP_METHOD_POST, $selectedFormatedFlightOption);

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }     
    }
    
    /**
    * Confirm a flight booking (Not paid yet).
    *
    * @param Request $request The HTTP request object.
    * @return mixed The flight booking confirmation.
    */
    public function FlightConfirmation(Request $request)
    {
        $validator = ValidationHelper::validateFlightConfirmationRequest($request);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        try {
            $bookedFlight = $this->IBookingService->bookFlight($request->json()->all());

            return ResponseHelper::jsonResponseMessage($bookedFlight, Response::HTTP_OK);

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * Pay for a flight confirmation.
    *
    * @param Request $request The HTTP request object.
    * @return mixed The payment result.
    */
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
        
        $booking = $this->IBookingService->getFlightSegmentsByBookingReference($bookingReference);
        if(count($booking) == 0){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_BAD_REQUEST);
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
            $booking = $this->IBookingService->finalizeFlightReservation($bookingReference);            
            $payment = $this->IPaymentService->createCharge($grandTotal, Constants::CURRENCY_CODE, $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReference);      
            $bookedFlightSegments = FlightConfirmationResource::collection($booking['itinerary']);
            $bookedFlightPassenger = PassengerResource::collection($booking['passengers']);
            $paymentDetails = new PaymentResource($payment);
            $bookingComplete = [
                $bookedFlightSegments,
                $bookedFlightPassenger,
                $paymentDetails,
            ];
        } catch (Exception $e) {
            $alreadyPaidBooking = $this->IBookingService->retrieveBookingInformation($bookingReference);
            return ResponseHelper::jsonResponseMessage($alreadyPaidBooking, Response::HTTP_ALREADY_REPORTED);
        }

        return ResponseHelper::jsonResponseMessage($bookingComplete, Response::HTTP_BAD_REQUEST);
    }

}


