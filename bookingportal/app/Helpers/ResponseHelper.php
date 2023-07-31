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
    const ENQUIRY_SENT = 'Your enquiry has been sent';
    const ENQUIRY_NOT_SENT = 'Your enquiry could not be sent';
    const COSTUMER_ENQUIRY_NOT_FOUND = 'No Costumer enquiries found';
    const AGENT_NOT_FOUND = "Agent could not be found";
    const FAQ_CREATED_SUCCESS = 'New FAQ successfully created';
    const FAQ_CREATION_FAILED = 'Failed to create new FAQ';
    const FAQ_NOT_FOUND = 'Faq not found';
    const BOOKING_REFERENCE_NOT_PROVIDED = 'Booking reference not provided';
    const AGENT_REMOVED_SUCCESFULLY = 'Agent removed succesfully.';
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
    public static function jsonResponseMessage($message, int $statusCode, $key = null): JsonResponse
    {
        if (is_string($message)) {
            $responseData = $key ? [$key => $message] : ['message' => $message];
        } else {
            $responseData = $key ? [$key => $message] : $message;
        }

        return response()->json($responseData, $statusCode);
    }
}

