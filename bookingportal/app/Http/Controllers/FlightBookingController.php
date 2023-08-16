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
use App\Mail\ISendEmailService;
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
    protected $ISendEmailService;

    public function __construct(IBookingService $IbookingService,  IAmadeusService $IAmadeusService, IPaymentService $IPaymentService, ISendEmailService $ISendEmailService)
    {
        $this->IBookingService = $IbookingService;
        $this->IAmadeusService = $IAmadeusService;
        $this->IPaymentService = $IPaymentService;
        $this->ISendEmailService = $ISendEmailService;
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
        $validated = $request->validated();
        
        $accessToken = $request->bearerToken(); 
        //$accessToken = $this->getAccessToken();
        $constructedSearchUrl = $this->IAmadeusService->AmadeusFlightSearchUrl(
            $request->get('originLocationCode'),
            $request->get('destinationLocationCode'),
            $request->get('departureDate'),
            $request->get('adults'),
            $request->get('returnDate'),
            $request->get('children', 0),
            $request->get('infants', 0),
            $request->get('travelClass'),
            $request->get('includedAirlineCodes'),
            $request->get('excludedAirlineCodes'),
            boolval($request->get('nonStop'))
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
            return $this->sendhttpRequest(getenv('CHOOSE_FLIGHT_API_URL'), $request->bearerToken(), self::HTTP_METHOD_POST, $selectedFormatedFlightOption);

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
        
        $validated = $request->validated();
        
       $booking = $this->IBookingService->getFlightSegmentsByBookingReference($request->get('bookingReference'));
        if(count($booking) == 0){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_BAD_REQUEST);
        }
        
        try {
            $payment = new PaymentResource($this->IPaymentService->createCharge(intval($request->get('grandTotal')), Constants::CURRENCY_CODE, $request->get('cardNumber'), $request->get('expireYear'),  $request->get('expireMonth'), $request->get('cvcDigits'), $request->get('bookingReference')));    
            if(!$payment){
                return ResponseHelper::jsonResponseMessage("Payment could not be done", Response::HTTP_BAD_REQUEST);
            }
            
            $booking = FlightConfirmationResource::collection($this->IBookingService->finalizeFlightReservation($request->get('bookingReference'))); 
            $passengers = PassengerResource::collection($this->IBookingService->getFlightPassengersByPNR($request->get('bookingReference')));           
          
            $bookingComplete = [
              "flight" => $booking,
              "passenger" => $passengers,
               "payment"   => $payment
            ];

            $generatedBooking = $this->IBookingService->generateBookingConfirmationPDF($bookingComplete);
            
            $passengerEmail = $this->IBookingService->getPassengerEmail($request->get('bookingReference'));
            
            $isSend = $this->ISendEmailService->sendEmailWithAttachments($passengerEmail, $passengerEmail, "Thank you for the booking! We are sending your electronic e-tickets", "Please see attached", $generatedBooking);
            if($isSend){
                return ResponseHelper::jsonResponseMessage($bookingComplete, Response::HTTP_OK);
            }

        } catch (Exception $e) {
            $errorMessage = "An error occurred: " . $e->getMessage();
            $stackTrace = $e->getTraceAsString();
            
            return ResponseHelper::jsonResponseMessage($errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);        
        }

        return ResponseHelper::jsonResponseMessage($bookingComplete, Response::HTTP_BAD_REQUEST);
    }

}


