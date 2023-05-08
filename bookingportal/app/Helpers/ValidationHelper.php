<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationHelper
{
    public static function validateFlightSearchRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'originLocationCode'        => 'required|string',
            'destinationLocationCode'   => 'required|string',
            'departureDate'             => 'required|string',
            'adults'                    => 'required|integer',
            'returnDate'                => 'nullable|string',
            'children'                  => 'nullable|integer',
            'infants'                   => 'nullable|integer',
            'travelClass'               => 'nullable|string',
            'includedAirlineCodes'      => 'nullable|string',
            'excludedAirlineCodes'      => 'nullable|string',
            'nonStop'                   => 'nullable',
        ]);
    }

    public static function validateFlightConfirmationRequest(Request $request){
        
        return Validator::make($request->all(), [    
            'itineraries' => 'required|array',
            'itineraries.*.segments.*.duration' => 'required|string',
            'itineraries.*.segments' => 'required|array',
            'passengers' => 'required|array',
            'passengers.*.title' => 'required|string',
            'passengers.*.firstName' => 'required|string',
            'passengers.*.lastName' => 'required|string',
            'passengers.*.dateOfBirth' => 'required|string',
            'passengers.*.email' => 'required|email',
            'passengers.*.passengerType' => 'required|string',
        ]);
    }

    public static function validateFlightPayRequest(Request $request){

        return Validator::make($request->all(), [
            'bookingReference'     => 'required|string',
            'grandTotal'           => 'required|string',
            'cardNumber'           => 'required|string',
            'expireMonth'          => 'required|string',
            'expireYear'           => 'required|string',
            'cvcDigits'            => 'required|string',
            'supportPackage'       => 'nullable|boolean',
            'changableTicket'      => 'nullable|boolean',
            'cancellationableTicket' => 'nullable|boolean'
        ]);
    }  

    public static function validateHotelSearchRequest(Request $request){
        
        return Validator::make($request->all(), [
            'cityCode'      => 'required|string',
            'adults'        => 'required|integer|min:1',
            'checkInDate'   => 'required|date|date_format:Y-m-d',
            'checkOutDate'  => 'required|date|date_format:Y-m-d',
            'roomQuantity'  => 'required|string',
            'priceRange'    => 'nullable|string',
            'paymentPolicy' => 'nullable|string',
            'boardType'     => 'nullable|string',

        ]);
    }

    public static function validateBookHotelRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'hotelOfferId'         => 'required|string',
            'firstName'            => 'required|string',
            'lastName'             => 'required|string',
            'email'                => 'required|email',
            'cardNumber'           => 'required|string',
            'expireMonth'          => 'required|string',
            'expireYear'           => 'required|string',
            'cvcDigits'            => 'required|string',
        ]);
    }
}
