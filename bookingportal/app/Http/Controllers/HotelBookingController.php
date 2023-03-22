<?php

namespace App\Http\Controllers;

use App\DTO\HotelOfferDTO;
use App\DTO\HotelSelectionDTO;
use App\Factories\BookingFactory;
use App\Factories\PaymentFactory;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class HotelBookingController extends Controller
{

    public function searchHotel(Request $request)
    {
        $listOfHotelByCityUrl = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city";
        $token = 'TizVBB9VEFAR4hDip9nR9nYjrwAg';
    
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
    
        $data = [
            'cityCode' => $cityCode
        ];
        $searchData = Arr::query($data);
        $listOfHotelByCityUrl .= '?' . $searchData;
    
        $hotelResponse = $this->httpRequest($listOfHotelByCityUrl, $token);
        $hotelResponse = json_decode($hotelResponse, true);
    
        $hotelIds = implode(',', array_map(function ($item) {
            return $item['hotelId'];
        }, $hotelResponse['data']));
    
        $finalHotelList = $this->getSpecificHotelsRoomAvailability($hotelIds, $adults, $checkInDate, $checkOutDate);
    
        return $finalHotelList;
    }
    
    private function getSpecificHotelsRoomAvailability($hotelIds, string $adults, string $checkInDate, string $checkOutDate)
    {
       
        $isCommaSeparated = implode(",", explode(",", $hotelIds)) === $hotelIds;

        if (!$isCommaSeparated || empty($hotelIds)) {
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
        $token = 'TizVBB9VEFAR4hDip9nR9nYjrwAg';

        $data = [
            'hotelIds'      => $hotelIds,
            'adults'        => $adults,
            'checkInDate'   => $checkInDate,
            'checkOutDate'  => $checkOutDate
        ];

        $searchData = Arr::query($data);
        $specificHotelOfferUrl .= '?' . $searchData;

        $response = $this->httpRequest($specificHotelOfferUrl, $token);
        return $response;
    }


    public function reviewSelectedHotelOfferInfo(string $hotelOfferId)
    {

        $url = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        $token = 'WrgaicAUlie5AYs8AAy1FsHKyrhL';

        if($hotelOfferId == null){
            throw new InvalidArgumentException("Invalid hotelOfferId found");
        }

        $url .= '/' . $hotelOfferId;

        $response = $this->httpRequest($url, $token);
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

        $response = $this->reviewSelectedHotelOfferInfo($hotelOfferId);
      
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

    
}
