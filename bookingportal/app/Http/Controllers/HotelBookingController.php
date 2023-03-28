<?php

namespace App\Http\Controllers;
use App\DTO\HotelSelectionDTO;
use App\Factories\BookingFactory;
use App\Factories\PaymentFactory;
use App\Models\HotelBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class HotelBookingController extends Controller
{

    public function searchHotel(Request $request)
    {
        $listOfHotelByCityUrl = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city";
    
        $validator = Validator::make($request->all(), [
            'cityCode'      => 'required|string',
            'adults'        => 'required|integer|min:1',
            'checkInDate'   => 'required|date|date_format:Y-m-d',
            'checkOutDate'  => 'required|date|date_format:Y-m-d',
        ]);

        
        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }
    
        $cityCode = $request->input('cityCode');
        $adults = $request->input('adults');
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');
        $accessToken = $request->bearerToken();

        $data = [
            'cityCode' => $cityCode
        ];
        $searchData = Arr::query($data);
        $listOfHotelByCityUrl .= '?' . $searchData;
    
        $hotelResponse = $this->httpRequest($listOfHotelByCityUrl, $accessToken);
        $hotelResponse = json_decode($hotelResponse, true);
    
        $hotelIds = implode(',', array_map(function ($item) {
            return $item['hotelId'];
        }, $hotelResponse['data']));
    
        $finalHotelList = $this->getSpecificHotelsRoomAvailability($hotelIds, $adults, $checkInDate, $checkOutDate, $accessToken);
    
        return $finalHotelList;
    }
    
    public function getSpecificHotelsRoomAvailability($hotelIds, string $adults, string $checkInDate, string $checkOutDate, string $accessToken)
    {
       
        $isCommaSeparatedArray = implode(",", explode(",", $hotelIds)) === $hotelIds;

        if (!$isCommaSeparatedArray || empty($hotelIds)) {
            throw new InvalidArgumentException("Invalid hotelIds parameter. Expecting a non-empty array.");
        }

        if (!is_numeric($adults) || $adults < 1) {
            throw new InvalidArgumentException("Invalid adults parameter. Expecting a positive integer.");
        }

        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $checkInDate)) {
            throw new InvalidArgumentException("Invalid checkInDate parameter. Expecting date format yyyy-mm-dd.");
        }
    
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $checkOutDate)) {
            throw new InvalidArgumentException("Invalid checkOutDate parameter. Expecting date format yyyy-mm-dd.");
        }
    
        $specificHotelOfferUrl = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        $data = [
            'hotelIds'      => $hotelIds,
            'adults'        => $adults,
            'checkInDate'   => $checkInDate,
            'checkOutDate'  => $checkOutDate
        ];

        $searchData = Arr::query($data);
        $specificHotelOfferUrl .= '?' . $searchData;


        $response = $this->httpRequest($specificHotelOfferUrl, $accessToken);
        return $response;
    }


    public function reviewSelectedHotelOfferInfo(string $hotelOfferId, string $accessToken)
    {

        $url = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        if($hotelOfferId == null){
            throw new InvalidArgumentException("Invalid hotelOfferId found");
        }

        $url .= '/' . $hotelOfferId;

        $response = $this->httpRequest($url, $accessToken);
        return $response;
    }

    publiC function hotelConfirmation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotelOfferId'         => 'required|string',
            'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'email'                => 'required|email',
            'cardNumber'           => 'required|string',
            'expireMonth'          => 'required|string',
            'expireYear'           => 'required|string',
            'cvcDigts'             => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $hotelOfferId = $request->input('hotelOfferId');
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $cardNumber = $request->input('cardNumber');
        $expireMonth = $request->input('expireMonth');
        $expireYear = $request->input('expireYear');
        $cvcDigts = $request->input('cvcDigts');
        $accessToken = $request->bearerToken();

        $response = $this->reviewSelectedHotelOfferInfo($hotelOfferId, $accessToken);
      
        $data = json_decode($response, true);
        
        $hotelOfferDTO = new HotelSelectionDTO($data);

        $bookingReferenceNumber = BookingFactory::generateBookingReference();
        
        $transaction = PaymentFactory::createCharge($hotelOfferDTO->priceTotal, "dkk", $cardNumber, $expireYear, $expireMonth, $cvcDigts, $bookingReferenceNumber);
        
        if($transaction){
            $hotelBoooking = BookingFactory::createHotelRecord($hotelOfferDTO, $bookingReferenceNumber, $firstName, $lastName, $email, $transaction->getPaymentInfoId());
        }
        
        $booking = [
            'success' => true,
            'hotelBoooking'  => $hotelBoooking,
            'transaction' => $transaction,    
        ];

        return response()->json($booking, 200);
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
