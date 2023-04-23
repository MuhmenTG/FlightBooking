<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use App\Models\Faq;
use App\Services\BookingService;
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
                return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
            }

            $bookingReference = $request->input('bookingReference');

            $bookingInfo = BookingService::retrieveBookingInformation($bookingReference);
    
            if ($bookingInfo) {
                return response()->json($bookingInfo, Response::HTTP_OK);
            }
    
            return response()->json('Invalid booking', Response::HTTP_NOT_FOUND);    
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

            $response = BookingService::sendRquestContactForm(
                $name, $email, $subject, $message, $bookingReference
            );

            if($response){
                return response()->json("Your enquiry has been sent", Response::HTTP_OK);
            }
            return response()->json("Something went wrong", Response::HTTP_BAD_REQUEST);

        }
        
        public function getAllFaqs(){
            $faqs = Faq::all();
            if($faqs->isEmpty()){
                return response()->json("Faqs could not be found", Response::HTTP_NOT_FOUND);
            }
            return response()->json($faqs, Response::HTTP_OK);
        }
}