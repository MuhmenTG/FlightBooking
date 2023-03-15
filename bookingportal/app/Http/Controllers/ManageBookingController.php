<?php

namespace App\Http\Controllers;

use App\Models\PassengerInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ManageBookingController extends Controller
{
    //
    public function retriveBookingInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bookingReference'      => 'required|string',
            'email'                 => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $bookingReference = $request->input('bookingReference');
        $email = $request->input('email');
        
        $passengers = PassengerInfo::where(PassengerInfo::COL_EMAIL, $email)->where(PassengerInfo::COL_PNR, $bookingReference)->all();
        if($passengers){
            
        }
    }
}
