<?php

namespace App\Http\Controllers;
use App\DTO\HotelSelectionDTO;
use App\Factories\BookingFactory;
use App\Factories\PaymentFactory;
use App\Models\HotelBooking;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

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
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 400);
        }

        $cityCode = $request->input('cityCode');
        $adults = $request->input('adults');
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');
        $accessToken = $request->bearerToken();

        $data = ['cityCode' => $cityCode];
        $searchData = Arr::query($data);
        $listOfHotelByCityUrl .= '?' . $searchData;

        $hotelResponse = $this->httpRequest($listOfHotelByCityUrl, $accessToken);

        if (empty($hotelResponse)) {
            return response()->json(['message' => 'Error retrieving hotel data'], 500);
        }

        $hotelResponse = json_decode($hotelResponse, true);

        if (!isset($hotelResponse['data'])) {
            return response()->json(['message' => 'No hotels found in the specified city'], 404);
        }

        $hotelIds = implode(',', array_map(function ($item) {
            return $item['hotelId'];
        }, $hotelResponse['data']));

        try {
            $finalHotelList = $this->getSpecificHotelsRoomAvailability($hotelIds, $adults, $checkInDate, $checkOutDate, $accessToken);
        } 
        catch (InvalidArgumentException $e) 
        {
            return response()->json(['message' => $e->getMessage()], 400);
        } 
        

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
            'cvcDigits'             => 'required|string',
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
        $cvcDigits = $request->input('cvcDigts');
        $accessToken = $request->bearerToken();
    
        try {
            $selectedHotelOfferResponse = $this->reviewSelectedHotelOfferInfo($hotelOfferId, $accessToken);
            if(!$selectedHotelOfferResponse){
                return response()->json('Could not find booking', Response::HTTP_BAD_REQUEST);
            }
            
            $data = json_decode($selectedHotelOfferResponse, true);
            $hotelOfferDTO = new HotelSelectionDTO($data);
    
            $bookingReferenceNumber = BookingFactory::generateBookingReference();
    
            $transaction = PaymentFactory::createCharge($hotelOfferDTO->priceTotal, "dkk", $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReferenceNumber);
            if(!$transaction){
                return response()->json('Could not create transaction', Response::HTTP_BAD_REQUEST);
            }
    
            $hotelBooking = BookingFactory::createHotelRecord($hotelOfferDTO, $bookingReferenceNumber, $firstName, $lastName, $email, $transaction->getPaymentInfoId());
            if(!$hotelBooking){
                return response()->json('Could not create hotel record', Response::HTTP_BAD_REQUEST);
            }
    
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
