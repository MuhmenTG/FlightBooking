<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\ManageBookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/getAccess', [FlightBookingController::class, 'getAccessToken']);

//Flight searching
Route::post('/flight/searchFlights', [FlightBookingController::class, 'searchFlights']);
Route::get('/flight/selectFlight', [FlightBookingController::class, 'selectFlightOffer']);
Route::post('/flight/confirmFlight', [FlightBookingController::class, 'flightConfirmation']);

//Hotel seaching
Route::get('/hotel/searchSelectHotel', [HotelBookingController::class, 'searchHotel']);
Route::get('/hotel/reviewSpecificHotelOffer/{hotelOfferId}', [HotelBookingController::class, 'reviewSelectedHotelOfferInfo']);
Route::post('/hotel/confirmHotel', [HotelBookingController::class, 'hotelConfirmation']);

//find booking both hotel and/or flight(costumer-page)
route::post('/booking/retriveBooking', [ManageBookingController::class, 'retrieveBookingInformation']);
route::post('/booking/updateHotelGuestInfo', [HotelBookingController::class, 'changeGuestDetails']);

//AgentPanel


//auth
Route::post('/auth/login', [AuthenticationController::class, 'loginUser']);
Route::post('/auth/logout', [AuthenticationController::class, 'logout']);


//Route::group(['middleware' => ['auth:sanctum']], function () {
    //Admin Panel
    Route::post('/admin/createAgent', [AdminController::class, 'createAgent']);
    Route::post('/admin/getSpecificAgentDetails', [AdminController::class, 'getSpecificAgentDetails']);
    Route::post('/admin/removeAgentAccount', [AdminController::class, 'removeAgentAccount']);
    Route::post('/admin/editAgentDetails', [AdminController::class, 'editAgentDetails']);
    Route::post('/admin/showListOfAgent', [AdminController::class, 'showListOfAgent']);
//});