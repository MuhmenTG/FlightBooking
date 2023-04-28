<?php

namespace App\Http\Controllers;
use App\DTO\HotelSelectionDTO;
use App\Models\HotelBooking;
use App\Services\AmadeusService;
use App\Services\BookingService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class HotelBookingController extends Controller
{

    public function searchHotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cityCode'      => 'required|string',
            'adults'        => 'required|integer|min:1',
            'checkInDate'   => 'required|date|date_format:Y-m-d',
            'checkOutDate'  => 'required|date|date_format:Y-m-d',
            'roomQuantity'  => 'required|string',
            'priceRange'    => 'nullable|string',
            'paymentPolicy' => 'nullable|string',
            'boardType'     => 'nullable|string',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $cityCode = $request->input('cityCode');
        $adults = $request->input('adults');
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');
        $roomQuantity = $request->input('roomQuantity');
        $priceRange = $request->input('priceRange');
        $paymentPolicy = $request->input('paymentPolicy');
        $boardType = $request->input('boardType');
      
        $accessToken = $request->bearerToken();

        $hotelIds = AmadeusService::AmadeusGetHotelList($cityCode, $accessToken);

        try {
            $finalHotelList = AmadeusService::AmadeusGetSpecificHotelsRoomAvailability($hotelIds, $adults, $checkInDate, $checkOutDate, $roomQuantity, $priceRange, $paymentPolicy, $boardType, $accessToken);
        } 
        catch (InvalidArgumentException $e) 
        {
            return response()->json(['message' => $e->getMessage()], 400);
        } 

        return $finalHotelList;
    }

    public function bookHotel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotelOfferId'         => 'required|string',
            'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'email'                => 'required|email',
            'cardNumber'           => 'required|string',
            'expireMonth'          => 'required|string',
            'expireYear'           => 'required|string',
            'cvcDigits'            => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], 400);
        }
    
        $hotelOfferId = $request->input('hotelOfferId');
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $cardNumber = $request->input('cardNumber');
        $expireMonth = $request->input('expireMonth');
        $expireYear = $request->input('expireYear');
        $cvcDigits = $request->input('cvcDigits');
        $accessToken = $request->bearerToken();

        try {
            $selectedHotelOfferResponse = AmadeusService::reviewSelectedHotelOfferInfo($hotelOfferId, $accessToken);
            if(!$selectedHotelOfferResponse){
                return response()->json('Could not find booking', Response::HTTP_BAD_REQUEST);
            }
            
            $data = json_decode($selectedHotelOfferResponse, true);
            $hotelOfferDTO = new HotelSelectionDTO($data);
    
            $bookingReferenceNumber = BookingService::generateBookingReference();
    
            $transaction = PaymentService::createCharge($hotelOfferDTO->priceTotal, "dkk", $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReferenceNumber);
            if(!$transaction){
                return response()->json('Could not create transaction', Response::HTTP_BAD_REQUEST);
            }
            
            $hotelBooking = BookingService::createHotelRecord($hotelOfferDTO, $bookingReferenceNumber, $firstName, $lastName, $email, $transaction->getPaymentInfoId());
            
            $response = [
                'success' => true,
                'hotelBooking'  => $hotelBooking,
                'transaction' => $transaction,    
            ];

            return response()->json($response, 200);
        } catch(Exception $e){
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    public function changeGuestDetails(Request $request){
        
        $validator = Validator::make($request->all(), [
            'bookingReference'     => 'required|string',
            'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'email'                => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
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
