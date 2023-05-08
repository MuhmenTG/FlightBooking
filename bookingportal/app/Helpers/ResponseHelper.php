<?php

namespace App\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ResponseHelper
{
    
    const EMPTY_FLIGHT_ARRAY = 'Empty flight data';
    const FLIGHTS_NOT_FOUND = 'Could not find any flights';
    const HOTEL_NOT_FOUND = 'Could not find hotel';
    const TRANSACTION_COULD_NOT_FINISH = 'Could not create transaction';
    const CREDENTIALS_WRONG = 'Wrong credentials';  
    const LOGOUT_SUCCESS = 'You have been logged out';
    const BOOKING_NOT_FOUND = 'Could not find any records';
    const NOT_CANCELLABLE = 'Could not cancel booking';

    /**
    * Generate a validation error response.
    *
    * @param  mixed  $errors  The validation errors.
    * @return JsonResponse  The JSON response.
    */
    public static function validationErrorResponse($errors): JsonResponse
    {
        return response()->json(['error' => 'Validation failed', 'details' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
    * Generate a JSON response message.
    *
    * @param  mixed  $message  The message to be included in the response.
    * @param  int  $statusCode  The HTTP status code for the response.
    * @return JsonResponse  The JSON response.
    */
    public static function jsonResponseMessage($message, int $statusCode): JsonResponse
    {
        $responseData = is_array($message) ? $message : ['message' => $message];
        return response()->json($responseData, $statusCode);
    }
}

