<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use App\Models\UserAccount;
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
            'role'                    => 'required|int'
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $password = "systemAgentUser";
        $status = $request->input('status');
        $role = $request->input('role');

        $userAccount = new UserAccount();
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setPassword(Hash::make($password));
        $userAccount->setStatus($status);
        $userAccount->setRole($role);

        return response()->json($userAccount->save(), 200);

    }

    public function getSpecificAgentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $userId = $request->input('userId');

        $user = UserAccount::ById($userId)->first();
        if($user){
            return response()->json($user, 200);
        }

        return response()->json("Agent could not be found", 404);

    }

    public function removeAgentAccount(Request $request){
        
        $validator = Validator::make($request->all(), [
            'userId'                  => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $userId = $request->input('userId');

        
        $user = Useraccount::ById($userId)->first();

        $user->setStatus(0);
        $user->getDeactivatedAt(time());
        return $user->save();

    }

    public function editAgentDetails(Request $request){
        
        $validator = Validator::make($request->all(), [
            'firstName'               => 'nullable|string',
            'lastName'                => 'nullable|string',
            'email'                   => 'nullable|string',
            'status'                  => 'nullable|int',
            'role'                    => 'nullable|int',
            'userId'                  => 'nullable|int',

        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $status = $request->input('status');
        $role = $request->input('role');
        
        $userId = $request->input('userId');

        $userAccount = UserAccount::ById($userId)->first();
        

        $userAccount->setFirstName($firstName);
    
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setStatus($status);
        $userAccount->setRole($role);
        $userAccount->save();

        return response()->json($userAccount, 400);
    }

    public function showListOfAgent(){

        $agents = Useraccount::all();
        return [
            "agents" => $agents
        ];
    }

    public function showAllBookings(Request $request){

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

}
