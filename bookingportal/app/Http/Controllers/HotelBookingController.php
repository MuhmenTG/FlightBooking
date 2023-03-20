<?php

namespace App\Http\Controllers;

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
        $token = 'RgSFMiL6G6S5VBwWCPPS6Q00TCLY';
    
        $validator = Validator::make($request->all(), [
            'cityCode' => 'required|string',
            'adults' => 'required|string',
            'checkInDate' => 'required|string',
            'checkOutDate' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }
    
        $cityCode = $request->input('cityCode');
        $adults = intval($request->input('adults'));
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');
    
        $data = [
            'cityCode' => $cityCode
        ];
        $searchData = Arr::query($data);
        $listOfHotelByCityUrl .= '?' . $searchData;
    
        $hotelResponse = $this->httpRequest($listOfHotelByCityUrl, $token);
        $data = json_decode($hotelResponse, true);
    
        $hotelIds = implode(',', array_map(function ($item) {
            return $item['hotelId'];
        }, $data['data']));
    
        $finalHotelList = $this->getSpecificHotelsRoomAvailability($hotelIds, $adults, $checkInDate, $checkOutDate);
    
        return $finalHotelList;
    }
    
    private function getSpecificHotelsRoomAvailability($hotelIds, int $adults, string $checkInDate, string $checkOutDate)
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
        $token = 'RgSFMiL6G6S5VBwWCPPS6Q00TCLY';

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

        $token = 'RgSFMiL6G6S5VBwWCPPS6Q00TCLY';


        $validator = Validator::make($request->all(), [
            'hotelOfferId'         => 'required|string',
            'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'gender'               => 'required|string',
            'dateOfBirth'          => 'required|string',
            'email'                => 'required|string',
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
        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],

            ]);
            $hotelOfferResponse = $response->getBody();
            
            $data = json_decode($hotelOfferResponse, true);
            $create = $this->createHotelBooking();

        } catch (GuzzleException $exception) {
            dd($exception);
        }
    }

    private function createHotelBooking(){

    }
}
