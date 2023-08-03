<?php
//declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Http\Requests\FlightConfirmationRequest;
use App\Http\Requests\FlightSearchRequest;
use App\Http\Requests\PayFlightConfirmationRequest;
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
use Illuminate\Support\Facades\Validator;

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
    public function searchFlights(FlightSearchRequest $request)
    {
        //$accessToken = $request->bearerToken();
        $accessToken = $this->getAccessToken();
        $constructedSearchUrl = $this->IAmadeusService->AmadeusFlightSearchUrl(
            $request->input('originLocationCode'),
            $request->input('destinationLocationCode'),
            $request->input('departureDate'),
            $request->input('adults'),
            $request->input('returnDate'),
            $request->input('children', 0),
            $request->input('infants', 0),
            $request->input('travelClass'),
            $request->input('includedAirlineCodes'),
            $request->input('excludedAirlineCodes'),
            boolval($request->input('nonStop'))
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
        if (empty($request->json()->all())) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::EMPTY_FLIGHT_ARRAY, Response::HTTP_BAD_REQUEST);
        }

        try {
            $selectedFormatedFlightOption = $this->IAmadeusService->prepareFlightOfferDataForAmadeusValidating($request->json()->all());
            return $this->sendhttpRequest(getenv('CHOOSE_FLIGHT_API_URL'), $this->getAccessToken(), self::HTTP_METHOD_POST, $selectedFormatedFlightOption);

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
    public function FlightConfirmation(FlightConfirmationRequest $request)
    {
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
    public function payFlightConfirmation(PayFlightConfirmationRequest $request)
    {
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
            $booking = FlightConfirmationResource::collection($this->IBookingService->finalizeFlightReservation($bookingReference)); 
            $passengers = PassengerResource::collection($this->IBookingService->getFlightPassengersByPNR($bookingReference));           
            $payment = new PaymentResource($this->IPaymentService->createCharge($grandTotal, Constants::CURRENCY_CODE, $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReference));      
            $bookingComplete = [
               "itinerary" => $booking,
               "passengers" => $passengers,
               "payment"   => $payment
            ];
            return ResponseHelper::jsonResponseMessage($bookingComplete, Response::HTTP_OK);

        } catch (Exception $e) {
            return ResponseHelper::jsonResponseMessage("Booking confirmation already paid", Response::HTTP_ALREADY_REPORTED);
        }

        return ResponseHelper::jsonResponseMessage($bookingComplete, Response::HTTP_BAD_REQUEST);
    }

}


