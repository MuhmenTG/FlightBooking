<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ManageBookingController extends Controller
{
        public function retrieveBookingInformation(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'bookingReference' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json('Validation Failed', Response::HTTP_BAD_REQUEST);
            }

            $bookingReference = $request->input('bookingReference');

            $bookedFlightSegments = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();
            $bookedFlightPassenger = PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->get();

            $bookedHotel = HotelBooking::byHotelBookingReference($bookingReference)->first();

            if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'PAX' => $bookedFlightPassenger,
                    'flight' => $bookedFlightSegments,
                ], Response::HTTP_OK);
            }

            if ($bookedHotel) {
                return response()->json([
                    'success' => true,
                    'hotelVoucher' => $bookedHotel,
                ], Response::HTTP_OK);
            }

            return response()->json('Invalid booking', Response::HTTP_NOT_FOUND);
        }

}
