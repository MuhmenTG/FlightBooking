<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Faq;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use App\Models\UserRole;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    //

    public function createAgent(Request $request){

        
        $validator = Validator::make($request->all(), [
            'firstName'               => 'required|string',
            'lastName'                => 'required|string',
            'email'                   => 'required|string',
            'status'                  => 'required|int',
            'isAdmin'                 => 'nullable|int',
            'isAgent'                 => 'nullable|int'

        ]);


        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $status = $request->input('status');
        $isAdmin = $request->input('isAdmin');
        $isAgent = $request->input('isAgent');

        $newAgent = AdminService::createOrEditAgent(
            $firstName,
            $lastName,
            $email,
            $status,
            intval($isAdmin),
            intval($isAgent)
        );

        if($newAgent){
            return response()->json($newAgent, Response::HTTP_OK);
        }
        
        return response()->json("User already registered", Response::HTTP_IM_USED);

    }

    public function getSpecificAgentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $userId = intval($request->input('userId'));

        $agent = AdminService::getSpecificAgentDetails($userId);

        if($agent){
            return response()->json($agent, Response::HTTP_OK);
        }

        return response()->json("Agent could not be found", Response::HTTP_NOT_FOUND);
    }

    public function setAgentAccountToDeactive(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->input('userId');
        
        $user = UserAccount::ById($userId)->first();

        $user->setStatus(0);
        $user->getDeactivatedAt(time());
        return $user->save();
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
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
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

    public function showListOfAgent(){

        $agents = Useraccount::all();
        return [
            "agents" => $agents
        ];
    }

    public function uploadAndEmail(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'mimes:pdf|max:2048'
        ]);
    
        $attachments = $request->allFiles('files');
    
        $email = "muhmen@live.ca";
        $name = "MUHMEN";
    
        SendEmail::sendEmailWithAttachments($name, $email, "Booking", $attachments);
    
        return response()->json("Booking confirmation has been sent", 200);
    }

    public function cancelFlightBooking(Request $request){
        $validator = Validator::make($request->all(), [
            'bookingReference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json('Validation Failed', Response::HTTP_BAD_REQUEST);
        }

        $bookingReference = $request->input('bookingReference');

        $bookedFlightSegments = FlightBooking ::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();
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
        
        return response()->json('Invalid booking', Response::HTTP_NOT_FOUND);        

    }

    public function cancelHotelBooking(Request $request){
        $validator = Validator::make($request->all(), [
            'bookingReference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json('Validation Failed', Response::HTTP_BAD_REQUEST);
        }

        $bookingReference = $request->input('bookingReference');

        $bookedHotel = HotelBooking::byHotelBookingReference($bookingReference)->first();

        if($bookedHotel){
            $bookedHotel->setIsCancelled(1);
            $bookedHotel->save();
            return $bookedHotel;
        }

        return response()->json('Invalid booking', Response::HTTP_NOT_FOUND);        

    }
    
    public function getAllUserEnquiries(): JsonResponse
    {
        $userEnquiries = UserEnquiry::all();
    
        if($userEnquiries->isEmpty()) {
            return response()->json(['message' => 'No user enquiries found'], Response::HTTP_NOT_FOUND);
        }
    
        return response()->json($userEnquiries, Response::HTTP_OK);
    }

    public function getSpecificUserEnquiry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $id = $request->input('id');
    
        $specificUserEnquiry = UserEnquiry::ById($id);
    
        if (!$specificUserEnquiry) {
            return response()->json(['message' => 'User enquiry not found'], Response::HTTP_NOT_FOUND);
        }
    
        return response()->json($specificUserEnquiry, Response::HTTP_OK);
    }

    public function setUserEnquiryStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $id = $request->input('id');
    
        $specificUserEnquiry = UserEnquiry::byId($id);
    
        if (!$specificUserEnquiry) {
            return response()->json(['message' => 'User enquiry not found'], Response::HTTP_NOT_FOUND);
        }
    
        $specificUserEnquiry->setIsSolved(UserEnquiry::CASE_SOLVED);
    
        if ($specificUserEnquiry->save()) {
            return response()->json($specificUserEnquiry, Response::HTTP_OK);
        }
    
        return response()->json(['message' => 'User enquiry could not be marked'], Response::HTTP_BAD_REQUEST);    
    }

    public function answerUserEnquiry(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'responseMessageToUser' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $id = $request->input('id');
        $responseMessageToUser = $request->input('responseMessageToUser');
    
        $specificUserEnquiry = UserEnquiry::byId($id);
        
        if(!$specificUserEnquiry){
            return response()->json('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        $emailSent = SendEmail::sendEmailWithAttachments(
            $specificUserEnquiry->getName(),
            $specificUserEnquiry->getEmail(),
            "Reply regarding " . $specificUserEnquiry->getSubject(),
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
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
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
    
    public function createNewFaq(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer',
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $id = $request->input('id');
        $question = $request->input('question');
        $answer = $request->input('answer');

        $faq = $id ? Faq::byId($id)->first() : new Faq();

        $faq->setQuestion($question);
        $faq->setAnswer($answer);

        if ($faq->save()) {
            return response()->json('New FAQ successfully created', Response::HTTP_OK);
        }

        return response()->json('Failed to create new FAQ', Response::HTTP_INTERNAL_SERVER_ERROR);
    }


    public function getSpecificFaq(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        
        $id = intval($request->input('id'));

        $specificFaq = Faq::byId($id)->first();

        if(!$specificFaq){
            response()->json("Faq not found", Response::HTTP_NOT_FOUND);
        }
        
        response()->json($specificFaq, Response::HTTP_OK);
    }

    public function removeFaq(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }

        $id = intval($request->input('id'));

        $specificFaq = Faq::byId($id)->first();
        if(!$specificFaq){
            return response()->json('Faq to delete not found', Response::HTTP_NOT_FOUND);    
        }
        $specificFaq->delete();

        if($specificFaq){
            response()->json('Faq successfully deleted', Response::HTTP_OK);
        }
    }

    public function createOrEditUserRole(Request $request){
        $validator = Validator::make($request->all(), [
            'id'                => 'nullable|integer',
            'roleName'          => 'required|string',
            'roleCode'          => 'required|string',
            'roleDescription'   => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $id = $request->input('id');
        $roleName = $request->input('roleName');
        $roleCode = $request->input('roleCode');
        $roleDescription = $request->input('roleDescription');

        $userRole = $id ? UserRole::byId($id) : new UserRole();
        $userRole->setRoleName($roleName);
        $userRole->setRoleCode($roleCode);
        $userRole->setRoleDescription($roleDescription);
       
        if ($userRole->save()) {
            return response()->json('New user role successfully created', Response::HTTP_OK);
        }

        return response()->json('Failed to create new user role', Response::HTTP_INTERNAL_SERVER_ERROR);

    }

    public function removeUserRole(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $id = intval($request->input('id'));
    
        $userRole = UserRole::byId($id)->first();
        if (!$userRole) {
            return response()->json('User enquiry not found', Response::HTTP_NOT_FOUND);    
        }
        
        if ($userRole->delete()) {
            return response()->json('User enquiry deleted successfully', Response::HTTP_OK);
        }
    
        return response()->json('UserEnquiry could not be deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function showSpecificOrAllUserRoles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $id = intval($request->input('id'));

        if ($id) {
            $userRole = UserRole::byId($id);

            if (!$userRole) {
                return response()->json(['error' => 'User role not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($userRole, 200);
        }

        $userRoles = UserRole::all();

        if ($userRoles->isEmpty()) {
            return response()->json(['error' => 'No user roles found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($userRoles, 200);
    }

    public function resetAgentPassword(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $userId = $request->input('userId');
        $password = "systemAgentUser";

        $user = UserAccount::ById($userId)->first();
        $user->setPassword(Hash::make($password));
        $user->getFirstTimeLoggedIn(0);
        $user->save();
    }
}

