<?php

namespace App\Http\Controllers;

use App\DTO\HotelOfferDTO;
use App\DTO\HotelSelectionDTO;
use App\Factories\BookingFactory;
use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;
use InvalidArgumentException;

class HotelBookingController extends Controller
{
    //
    const SPECIFICHOTELAVALIABILITY = '';
    const SPECIFICHOTELOFFER = '';
    const CONFIRMHOTELOFFER = 'https://test.api.amadeus.com/v1/booking/hotel-bookings';

    public function searchHotel(Request $request)
    {
        $listOfHotelByCityUrl = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city";
        $token = 'GMvn8enAg3EWsaAhhDAJ8SZImiEa';
    
        $validator = Validator::make($request->all(), [
            'cityCode'      => 'required|string',
            'adults'        => 'required|string',
            'checkInDate'   => 'required|string',
            'checkOutDate'  => 'required|string',
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
        $token = 'GMvn8enAg3EWsaAhhDAJ8SZImiEa';

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


    public function getFinalHotelOfferInfo(Request $request)
    {

        $url = "https://test.api.amadeus.com/v3/shopping/hotel-offers";

        $token = '6CfuAxAE2xc1wA8O7bhGT3whv32M';

        $validator = Validator::make($request->all(), [
            'hotelOfferId'         => 'required|string',
       /*     'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'gender'               => 'required|string',
            'dateOfBirth'          => 'required|string',
            'email'                => 'required|string',*/
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $hotelOfferId = $request->input('hotelOfferId');
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $gender = $request->input('gender');
        $dateOfBirth = $request->input('dateOfBirth');
        $email = $request->input('email');


        $url .= '/' . $hotelOfferId;

        
        $response = $this->httpRequest($url, $token);
        $data = json_decode($response, true);

        $hotelOfferDTO = new HotelSelectionDTO($data);

        $bookingReferenceNumber = BookingFactory::generateBookingReference();
    
        $hotelBoooking = BookingFactory::createHotelRecord($hotelOfferDTO, $bookingReferenceNumber);
     
        echo $hotelBoooking;exit;
    }

    
}
