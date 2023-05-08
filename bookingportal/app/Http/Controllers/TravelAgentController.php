<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Mail\SendEmail;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use App\Services\BackOfficeService;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TravelAgentController extends Controller
{
    //
    
    public function cancelHotelBooking(Request $request){
        $validator = Validator::make($request->all(), [
            'bookingReference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $bookingReference = $request->input('bookingReference');

        $isBookingExist = BookingService::gethotelBookingByBookingReference($bookingReference);
        if(!$isBookingExist){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_NOT_FOUND);
      
        }

        $hotelBooking = BookingService::cancelHotelBooking($bookingReference);
        if(!$hotelBooking){
            return ResponseHelper::jsonResponseMessage(ResponseHelper::NOT_CANCELLABLE, Response::HTTP_NOT_FOUND);
        }

        $response = [
            'cancellation' => true,
            'hotel' => $hotelBooking,
        ];

        return ResponseHelper::jsonResponseMessage($response, Response::HTTP_OK);

    }
 
    public function cancelFlightBooking(Request $request){
        $validator = Validator::make($request->all(), [
            'bookingReference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $bookingReference = $request->input('bookingReference');

        $bookedFlightSegments = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();
        $bookedFlightPassenger = PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->get();
        
        
        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            $isflightSegmentsCancelled = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->update([FlightBooking::COL_ISCANCELLED => 1]);
            $isPassengersCancelled = PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->update([PassengerInfo::COL_ISCANCELLED => 1]);

            if($isflightSegmentsCancelled && $isPassengersCancelled){
                $cancelledBooking = FlightBooking::ByBookingReference($bookingReference)->ByIsCancelled(1)->get();
                $cancelledBookingPassengers = PassengerInfo::ByBookingReference($bookingReference)->ByIsCancelled(1)->get();
            }   

            return response()->json([
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
    
        $isSend = SendEmail::sendEmailWithAttachments($name, $email, $subject, $text, $attachments);
        
        if($isSend){
            $response = [
                "success" => true,
                "Booking confirmation has been sent",
            ];
            return ResponseHelper::jsonResponseMessage($response, Response::HTTP_OK);
        }

        return response()->json('Something went wrong while sending confirmation', Response::HTTP_BAD_REQUEST);        
    }

    public function getAllUserEnquiries()
    {
        $userEnquiries = UserEnquiry::all();
    
        if($userEnquiries->isEmpty()) {
            return response()->json(['message' => 'No user enquiries found'], Response::HTTP_NOT_FOUND);
        }
    
        return response()->json($userEnquiries, Response::HTTP_OK);
    }
    
    public function getSpecificUserEnquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $id = $request->input('id');

        $specificUserEnquiry = BackOfficeService::findUserEnquiryById($id);

        if (!$specificUserEnquiry) {
            return response()->json(['message' => 'User enquiry not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($specificUserEnquiry, Response::HTTP_OK);
    }

    public function answerUserEnquiry(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'responseMessageToUser' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
    
        $id = $request->input('id');
        $responseMessageToUser = $request->input('responseMessageToUser');
    
        $specificUserEnquiry = BackOfficeService::findUserEnquiryById($id);
        
        if(!$specificUserEnquiry){
            return response()->json('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        $emailSent = SendEmail::sendEmailWithAttachments(
            $specificUserEnquiry->getName(),
            $specificUserEnquiry->getEmail(),
            $specificUserEnquiry->getSubject(),
            $responseMessageToUser
        );
    
        if($emailSent){
            return response()->json("Email replied", Response::HTTP_OK);
        }
        
        return response()->json('Email could not be sent', Response::HTTP_BAD_REQUEST);
    }
    
    
    public function removeUserEnquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }
    
        $id = intval($request->input('id'));
    
        $specificUserEnquiry = UserEnquiry::byId($id)->first();
        if (!$specificUserEnquiry) {
            return response()->json('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        if ($specificUserEnquiry->delete()) {
            return response()->json('User enquiry deleted successfully', Response::HTTP_OK);
        }
    
        return response()->json('UserEnquiry could not be deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
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
                return response()->json("User account not found", 404);
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

        return response()->json($userAccount, 400);
    }

    public function changeGuestDetails(Request $request){
        
        $validator = Validator::make($request->all(), [
            'bookingReference'     => 'required|string',
            'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'email'                => 'required|email',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $bookingReference = $request->input('bookingReference');
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        
        $bookedHotel = HotelBooking::ByHotelBookingReference($bookingReference)->first();
        $bookedHotel->setMainGuestFirstName($firstName);
        $bookedHotel->setMainGuestLasName($lastName);
        $bookedHotel->setMainGuestEmail($email);
      
        return response()->json($bookedHotel, 200);
    }

}
