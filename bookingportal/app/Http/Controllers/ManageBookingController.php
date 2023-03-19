}<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
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
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $bookingReference = $request->input('bookingReference');
        
        $passengers = PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->get();
        
        if($passengers == null){
            return response()->json("Booking Could not be found", 404);
        }
        
        $flights = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();   

        $booking = [
            'success' => true,
            'PAX'  => $passengers,
            'flight' => $flights,    
        ];

        return response()->json($booking, 200);
    }
}
