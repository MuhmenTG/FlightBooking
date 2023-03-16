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

    public function searchHotelByCity(Request $request){  
        $listOfHotelByCityUrl = "https://test.api.amadeus.com/v1/reference-data/locations/hotels/by-city";
        $token = 'VjoVY0vJ9UCJhMGOyZQRcuJyvkqN';
        $validator = Validator::make($request->all(), [
            'cityCode'      => 'required|string',
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
            
            $hotelIds = implode(',', array_map(function($item) {
                return $item['hotelId'];
            }, $data['data']));
            $finalHotelList = $this->selectSpecificHotelOffer($hotelIds, $adults, $checkInDate, $checkOutDate);
            return $finalHotelList;


        } catch (GuzzleException $exception) {
            dd($exception);
        }       
    }

    public function selectSpecificHotelOffer($hotelIds, $adults, $checkInDate, $checkOutDate){
        $specificHotelOfferUrl = "https://test.api.amadeus.com/v3/shopping/hotel-offers";
            $token = 'VjoVY0vJ9UCJhMGOyZQRcuJyvkqN';


        $data = [
            'hotelIds'      => $hotelIds,
            'adults'        => $adults,
            'checkInDate'   => $checkInDate,
            'checkOutDate'  => $checkOutDate
        ];

        $searchData = Arr::query($data);
        $specificHotelOfferUrl .= '?' . $searchData;
     //   echo $specificHotelOfferUrl;exit;
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
    
    public function selectSpecificHotelRoomOffer(Request $request){
       
    }

   /* public function confirmHotelBooking(){

        $validator = Validator::make($request->all(), [
            'cityCode'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json("Validation Failed", 400);
        }

        $cityCode = $request->input('cityCode');

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
                    'Authorization' => 'Bearer ' . ''
                ],
                    
            ]);
            return $response->getBody();

        } catch (GuzzleException $exception) {
            dd($exception);
        }       
    }*/
    
}
