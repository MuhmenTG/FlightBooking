<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\EnquirySupportRequest;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FlightConfirmationResource;
use App\Http\Resources\PassengerResource;
use App\Http\Resources\PaymentResource;
use App\Models\AirportInfo;
use App\Services\BackOffice\IBackOfficeService;
use App\Services\Booking\IBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PublicSiteController extends Controller
{
    protected $IBookingService;
    protected $IBackOfficeService;
    /**
    * PublicSiteController constructor.
    * @param IBookingService $IbookingService
    */
    public function __construct(IBookingService $IbookingService, IBackOfficeService $IBackOfficeService)
    {
        $this->IBookingService = $IbookingService;
        $this->IBackOfficeService = $IBackOfficeService;
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
            $bookedFlightPassenger = PassengerResource::collection($bookingInfo['passengers']);
            $paymentDetails = new PaymentResource($bookingInfo['payment']);
    
            $responseData = [
                'passengers' => $bookedFlightPassenger,
                'flight' => $bookedFlightSegments,
                'payment' => $paymentDetails,
            ];
    
            return ResponseHelper::jsonResponseMessage($responseData, Response::HTTP_OK);
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
      $validated = $request->validated();
        
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

    public function searchCity(string $cityIcao){
        $cityName = AirportInfo::ByAirportIcao($cityIcao)->first();

        if(!$cityName){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::CITY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($cityName, Response::HTTP_OK, "city");
    }
}
