<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Mail\ISendEmailService;
use App\Mail\SendEmail;;
use App\Models\HotelBooking;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use App\Services\BackOfficeService;
use App\Services\Booking\IBookingService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TravelAgentController extends Controller
{
    //
    protected $IBookingService;
    protected $IEmailSendService;

    

    public function __construct(IBookingService $IBookingService, ISendEmailService $IEmailSendService)
    {
        $this->IBookingService = $IBookingService;
        $this->IEmailSendService = $IEmailSendService;
    }

    public function cancelFlightBooking(string $bookingReference)
    {
        $bookedFlightSegments = $this->IBookingService->getFlightSegmentsByBookingReference($bookingReference);
        $bookedFlightPassenger = $this->IBookingService->getFlightPassengersByPNR($bookingReference);

        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            $this->IBookingService->cancelFlightBooking($bookingReference);

            $cancelledBooking = $this->IBookingService->getFlightSegmentsByBookingReference($bookingReference);
            $cancelledBookingPassengers = $this->IBookingService->getFlightPassengersByPNR($bookingReference);

            return ResponseHelper::jsonResponseMessage([
                'cancellation' => true,
                'PAX' => $cancelledBooking,
                'flight' => $cancelledBookingPassengers
            ], Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }
     
    public function resendBookingConfirmationPDF(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'name'      => 'required|string',
            'text'      => 'required|string',
            'subject'   => 'required|string',
            'files'     => 'required',
            'files.*'   => 'mimes:pdf|max:2048',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
    
        $attachments = $request->allFiles('files');
        $email = $request->input('email');
        $name = $request->input('name');
        $text = $request->input('text');
        $subject = $request->input('subject');
    
        $isSend = $this->IEmailSendService->sendEmailWithAttachments($name, $email, $subject, $text, $attachments);
        
        if($isSend){
            $response = [
                "success" => true,
                "Booking confirmation has been sent",
            ];
            return ResponseHelper::jsonResponseMessage($response, Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage('Something went wrong while sending confirmation', Response::HTTP_BAD_REQUEST);        
    }

    public function getAllUserEnquiries()
    {
        $userEnquiries = UserEnquiry::all();
    
        if($userEnquiries->isEmpty()) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::COSTUMER_ENQUIRY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
    
        return ResponseHelper::jsonResponseMessage($userEnquiries, Response::HTTP_OK);
    }
    
    public function getSpecificUserEnquiry(int $enquiryId)
    {
        $specificUserEnquiry = $this->IBookingService->getUserEnquiryById($enquiryId);

        if (!$specificUserEnquiry) {
            return ResponseHelper::jsonResponseMessage(['message' => 'User enquiry not found'], Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($specificUserEnquiry, Response::HTTP_OK);
    }

    public function answerUserEnquiry(Request $request){

        $validator = Validator::make($request->all(), [
            'id'                    => 'required|integer',
            'responseMessageToUser' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
    
        $enquiryId = $request->input('id');
        $responseMessageToUser = $request->input('responseMessageToUser');

        $specificUserEnquiry = $this->IBookingService->getUserEnquiryById($enquiryId);
        
        if(!$specificUserEnquiry){
            return ResponseHelper::jsonResponseMessage('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        $emailSent = $this->IEmailSendService->sendEmailWithAttachments($specificUserEnquiry->getName(), $specificUserEnquiry->getEmail(),
            $specificUserEnquiry->getSubject(), $responseMessageToUser
        );
    
        if($emailSent){
            return ResponseHelper::jsonResponseMessage("Email replied", Response::HTTP_OK);
        }
        
        return ResponseHelper::jsonResponseMessage('Email could not be sent', Response::HTTP_BAD_REQUEST);
    }
    
    
    public function removeUserEnquiry(int $enquiryId)
    {    
        $specificUserEnquiry = UserEnquiry::byId($enquiryId)->first();
        if (!$specificUserEnquiry) {
            return ResponseHelper::jsonResponseMessage('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        if ($specificUserEnquiry->delete()) {
            return ResponseHelper::jsonResponseMessage('User enquiry deleted successfully', Response::HTTP_OK);
        }
    
        return ResponseHelper::jsonResponseMessage('UserEnquiry could not be deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    
    public function editAgentDetails(Request $request){
        
        $validator = Validator::make($request->all(), [
            'firstName'               => 'required|string',
            'lastName'                => 'required|string',
            'email'                   => 'required|string',
            'status'                  => 'required|int',
            'isAdmin'                 => 'nullable|int',
            'isAgent'                 => 'nullable|int',

            'userId'                  => 'nullable|int',
        ]);


        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $status = $request->input('status');
        $isAdmin = $request->input('isAdmin');
        $isAgent = $request->input('isAgent');

        $userId = $request->input('userId');

        
        $loggedInUserId = $request->user()->id;

        if ($loggedInUserId->role === 'admin' || ($loggedInUserId === $userId)) {
            $userAccount = UserAccount::byId($userId ?? $loggedInUserId)->first();
            if (!$userAccount) {
                return ResponseHelper::jsonResponseMessage("User account not found", 404);
            }
        }

        $userAccount = UserAccount::ById($userId)->first();
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setIsAgent($isAgent);
        $userAccount->setIsAdmin($isAdmin);
        $userAccount->setStatus($status);
        $userAccount->save();

        return ResponseHelper::jsonResponseMessage($userAccount, 400);
    }


}
