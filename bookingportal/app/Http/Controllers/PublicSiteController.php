<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EnquirySupportRequest;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FlightConfirmationResource;
use App\Http\Resources\PassengerResource;
use App\Http\Resources\PaymentResource;
use App\Models\AirportInfo;
use App\Services\Amadeus\IAmadeusService;
use App\Services\BackOffice\IBackOfficeService;
use App\Services\Booking\IBookingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicSiteController extends Controller
{
    protected $IBookingService;
    protected $IBackOfficeService;
    protected $IAmadeusService;

    /**
    * PublicSiteController constructor.
    * @param IBookingService $IbookingService
    */
    public function __construct(IBookingService $IbookingService, IBackOfficeService $IBackOfficeService, IAmadeusService $IAmadeusService)
    {
        $this->IBookingService = $IbookingService;
        $this->IBackOfficeService = $IBackOfficeService;
        $this->IAmadeusService = $IAmadeusService;
    }

    /**
    * Retrieve booking information by booking reference.
    *
    * @param string $bookingReference The booking reference.
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function retrieveBookingInformation(string $bookingReference)
    {
        if ($bookingReference === null) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_REFERENCE_NOT_PROVIDED, Response::HTTP_BAD_REQUEST);
        }
    
        $bookingInfo = $this->IBookingService->retrieveBookingInformation($bookingReference);
    
        if ($bookingInfo) {

            $bookedFlightSegments = FlightConfirmationResource::collection($bookingInfo['flight']);
            $bookedFlightPassengers = PassengerResource::collection($bookingInfo['passengers']);
            $paymentDetails = new PaymentResource($bookingInfo['payment']);
    
            $bookingComplete = [
                "passengers" => $bookedFlightPassengers,
                "flights" => $bookedFlightSegments,
                "payment" => $paymentDetails,
            ];
    
            return ResponseHelper::jsonResponseMessage($bookingComplete, Response::HTTP_OK);
        }
    
        return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }
    

    /**
    * Send an enquiry or support request.
    *
    * @param Request $request The request object.
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function sendEnquirySupport(EnquirySupportRequest $request)
    {    
        $request->validated();
        
        $response = $this->IBookingService->sendRquestContactForm(
            $request->get('name'),
            $request->get('email'),
            $request->get('subject'),
            $request->get('message'),
            $request->get('bookingReference')
        );

        $message = $response ? ResponseHelper::ENQUIRY_SENT : ResponseHelper::ENQUIRY_NOT_SENT;
        $statusCode = $response ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST;

        return ResponseHelper::jsonResponseMessage($message, $statusCode);
    }

    
    /**
    * Get all FAQs.
    *
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function getAllFaqs(){
        $faqs = FaqResource::collection($this->IBackOfficeService->getAllFaqs());

        if($faqs->isEmpty()){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($faqs, Response::HTTP_OK, "FAQS");
    }

    public function getSearchCity(string $cityName){
        $cityName = AirportInfo::whereLike(AirportInfo::COL_CITY, $cityName)->get();

        if(!$cityName){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::CITY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($cityName, Response::HTTP_OK, "city");
    }

    /**
    * The City Search API finds cities that match a specific word or string of letters.
    *
    * @param Request $request The request object.
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function searchCity(Request $request){
        $constructedSearchUrl = $this->IAmadeusService->AmadeusCitySearchUrl(
            $request->input('keyWord')
        );
        
        $data = $this->sendhttpRequest($constructedSearchUrl, 'GGOk4m5HyMALt0XGhZEcUNj9BpLT');
        
        return $data;
    }

}
