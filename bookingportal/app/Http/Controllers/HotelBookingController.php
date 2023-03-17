<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;
use Termwind\Components\Raw;

class HotelBookingController extends Controller
{
    //
    const SPECIFICHOTELAVALIABILITY = '';
    const SPECIFICHOTELOFFER = '';
    const CONFIRMHOTELOFFER = 'https://test.api.amadeus.com/v1/booking/hotel-bookings';

    public function searchHotelByCity(Request $request)
    {
        $listOfHotelByCityUrl = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city";
        $token = 'RgSFMiL6G6S5VBwWCPPS6Q00TCLY';
        $validator = Validator::make($request->all(), [
            'cityCode'         => 'required|string',
            'adults'           => 'required|string',
            'checkInDate'      => 'required|string',
            'checkOutDate'     => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $cityCode = $request->input('cityCode');
        $adults = $request->input('adults');
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');


        $data = [
            'cityCode'      => $cityCode
        ];

        $searchData = Arr::query($data);
        $listOfHotelByCityUrl .= '?' . $searchData;

        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->get($listOfHotelByCityUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],

            ]);
            $hotelResponse = $response->getBody();

            $data = json_decode($hotelResponse, true);

            $hotelIds = implode(',', array_map(function ($item) {
                return $item['hotelId'];
            }, $data['data']));
            $finalHotelList = $this->getSpecificHotelsRoomAvaliability($hotelIds, $adults, $checkInDate, $checkOutDate);
            return $finalHotelList;
        } catch (GuzzleException $exception) {
            dd($exception);
        }
    }

    private function getSpecificHotelsRoomAvaliability($hotelIds, string $adults, string $checkInDate, string $checkOutDate)
    {
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

        try {

            $client = new \GuzzleHttp\Client();
            $response = $client->get($specificHotelOfferUrl, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],

            ]);
            return $response->getBody();
        } catch (GuzzleException $exception) {
            dd($exception);
        }
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
