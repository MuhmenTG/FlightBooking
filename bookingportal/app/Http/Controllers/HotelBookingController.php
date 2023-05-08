<?php

namespace App\Http\Controllers;
use App\DTO\HotelSelectionDTO;
use App\Helpers\ResponseHelper;
use App\Helpers\ValidationHelper;
use App\Services\AmadeusService;
use App\Services\BookingService;
use App\Services\PaymentService;
use Exception;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class HotelBookingController extends Controller
{

    public function searchHote1l(Request $request)
    {
        $validator = ValidationHelper::validateHotelSearchRequest($request);

        if ($validator->fails()) {
            return ResponseHelper ::validationErrorResponse($validator->errors());
        }

        $cityCode = $request->input('cityCode');
        $adults = $request->input('adults');
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');
        $roomQuantity = $request->input('roomQuantity');
        $priceRange = $request->input('priceRange');
        $paymentPolicy = $request->input('paymentPolicy');
        $boardType = $request->input('boardType');
        $accessToken = 'E3Xt5A7nKnOgayc2u5CcskXpNEKK';

        $hotelIds = AmadeusService::AmadeusGetHotelList($cityCode, $accessToken);
        echo $hotelIds;exit;
        try {
            $finalHotelList = AmadeusService::AmadeusGetSpecificHotelsRoomAvailability($hotelIds, $adults, $checkInDate, $checkOutDate, $roomQuantity, $priceRange, $paymentPolicy, $boardType, $accessToken);
        } 
        catch (InvalidArgumentException $e) 
        {
            ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } 

        return $finalHotelList;
    }

    public function searchHotel(Request $request)
    {
        $validator = ValidationHelper::validateHotelSearchRequest($request);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $cityCode = $request->input('cityCode');
        $adults = $request->input('adults');
        $checkInDate = $request->input('checkInDate');
        $checkOutDate = $request->input('checkOutDate');
        $roomQuantity = $request->input('roomQuantity');
        $priceRange = $request->input('priceRange');
        $paymentPolicy = $request->input('paymentPolicy');
        $boardType = $request->input('boardType');
        $accessToken = 'E3Xt5A7nKnOgayc2u5CcskXpNEKK';

        $hotelIds = AmadeusService::AmadeusGetHotelList($cityCode, $accessToken);

        $hotelIdsArray = explode(',', $hotelIds);
        $batchSize = 10;
        $batches = array_chunk($hotelIdsArray, $batchSize);
        $finalHotelList = [];

        foreach ($batches as $batch) {
            try {
                $hotelList = AmadeusService::AmadeusGetSpecificHotelsRoomAvailability(
                    implode(',', $batch),
                    $adults,
                    $checkInDate,
                    $checkOutDate,
                    $roomQuantity,
                    $priceRange,
                    $paymentPolicy,
                    $boardType,
                    $accessToken
                );
        
                $jsonString = $hotelList->getContents();

        
                // Decode the JSON string into an associative array
                $hotelIds = json_decode($jsonString, true);
        
                // Merge the batch hotels with the final hotel list
                $finalHotelList = array_merge($finalHotelList, $hotelIds);
            } catch (InvalidArgumentException $e) {
                return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }
        
        return response()->json($finalHotelList);
        
    }


    public function bookHotel(Request $request)
    {
        $validator = ValidationHelper::validateBookHotelRequest($request);
    
        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }
    
        $hotelOfferId = $request->input('hotelOfferId');
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');
        $cardNumber = $request->input('cardNumber');
        $expireMonth = $request->input('expireMonth');
        $expireYear = $request->input('expireYear');
        $cvcDigits = $request->input('cvcDigits');
        $accessToken = $request->bearerToken();

        try {
            $selectedHotelOfferResponse = AmadeusService::reviewSelectedHotelOfferInfo($hotelOfferId, $accessToken);
            if(!$selectedHotelOfferResponse){
                return ResponseHelper::jsonResponseMessage(ResponseHelper::HOTEL_NOT_FOUND, Response::HTTP_BAD_REQUEST);
            }
            
            $data = json_decode($selectedHotelOfferResponse, true);
            $hotelOfferDTO = new HotelSelectionDTO($data);
    
            $bookingReferenceNumber = BookingService::generateBookingReference();
    
            $transaction = PaymentService::createCharge($hotelOfferDTO->priceTotal, "DKK", $cardNumber, $expireYear, $expireMonth, $cvcDigits, $bookingReferenceNumber);
            if(!$transaction){
                return ResponseHelper::jsonResponseMessage(ResponseHelper::TRANSACTION_COULD_NOT_FINISH, Response::HTTP_BAD_REQUEST);
            }
            
            $hotelBooking = BookingService::createHotelRecord($hotelOfferDTO, $bookingReferenceNumber, $firstName, $lastName, $email, $transaction->getPaymentInfoId());
            
            $response = [
                'success' => true,
                'hotelBooking'  => $hotelBooking,
                'transaction' => $transaction,    
            ];

            return ResponseHelper::jsonResponseMessage($response, Response::HTTP_OK);
        } catch(Exception $e){
            return ResponseHelper::jsonResponseMessage($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    

}
