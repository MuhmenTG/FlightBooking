<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Faq;
use App\Models\FlightBooking;
use App\Models\HotelBooking;
use App\Models\PassengerInfo;
use App\Models\Payment;
use App\Models\UserEnquiry;
use App\Models\UserEnqury;
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

            $paymentDetails = Payment::ByNote($bookingReference)->first();

            if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'PAX' => $bookedFlightPassenger,
                    'flight' => $bookedFlightSegments,
                    'payment' => $paymentDetails
                ], Response::HTTP_OK);
            }

            if ($bookedHotel) {
                return response()->json([
                    'success' => true,
                    'hotelVoucher' => $bookedHotel,
                    'payment' => $paymentDetails
                ], Response::HTTP_OK);
            }

            return response()->json('Invalid booking', Response::HTTP_NOT_FOUND);
        }

        public function getAllFaqs(){
            $faqs = Faq::all();
            if($faqs->isEmpty()){
                return response()->json("Faqs could not be found", Response::HTTP_NOT_FOUND);
            }
            return response()->json($faqs, Response::HTTP_OK);
        }

        public function sendEnquirySupport(Request $request){
            $validator = Validator::make($request->all(), [
                'name'            =>  'required|string',
                'email'           =>  'required|string',
                'subject'         =>  'required|string',
                'message'         =>  'required|string',
                'bookingReference' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
            }

            $name = $request->input('name');
            $email = $request->input('email');
            $subject = $request->input('subject');
            $message = $request->input('message');
            $bookingReference = $request->input('bookingReference');

            $enquiry = new UserEnquiry();
            $enquiry->setName($name);
            $enquiry->setEmail($email);
            $enquiry->setSubject($subject);
            $enquiry->setBookingreference($bookingReference);
            $enquiry->setMessage($message);
            $enquiry->setTime(time());
            $enquiry->save();
            
            $userCopy = SendEmail::sendEmailWithAttachments($name, $email, $subject, $message);
            if($userCopy){
                return response()->json('Enquiry sent', Response::HTTP_OK);
            }
            
        }

}
