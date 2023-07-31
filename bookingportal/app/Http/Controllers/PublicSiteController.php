<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\FaqResource;
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
            return ResponseHelper::jsonResponseMessage($bookingInfo, Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }

    /**
    * Send an enquiry or support request.
    *
    * @param Request $request The request object.
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function sendEnquirySupport(Request $request){
        $validator = Validator::make($request->all(), [
            'name'            =>  'required|string',
            'email'           =>  'required|string',
            'subject'         =>  'required|string',
            'message'         =>  'required|string',
            'bookingReference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $name = $request->input('name');
        $email = $request->input('email');
        $subject = $request->input('subject');
        $message = $request->input('message');
        $bookingReference = $request->input('bookingReference');

        $response = $this->IBookingService->sendRquestContactForm(
            $name, $email, $subject, $message, $bookingReference
        );

        if($response){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::ENQUIRY_SENT, Response::HTTP_OK);
        }
        return ResponseHelper::jsonResponseMessage(ResponseHelper::ENQUIRY_NOT_SENT, Response::HTTP_BAD_REQUEST);
    }
    
    /**
    * Get all FAQs.
    *
    * @return \Illuminate\Http\JsonResponse The JSON response.
    */
    public function getAllFaqs(){
        $faqs = $this->IBackOfficeService->getAllFaqs();

        if($faqs->isEmpty()){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::FAQ_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        $faqs = FaqResource::collection($faqs);
        
        return ResponseHelper::jsonResponseMessage($faqs, Response::HTTP_OK, "FAQS");
    }
}