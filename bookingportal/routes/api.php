<?php

use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\PaymentController;
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
Route::post('/search', [FlightBookingController::class, 'searchFlights']);
Route::get('/selectFlightOffer', [FlightBookingController::class, 'selectFlightOffer']);


Route::get('/searchHotelByCity', [HotelBookingController::class, 'searchHotelByCity']);
Route::get('/selectSpecificHotelOffer', [HotelBookingController::class, 'selectSpecificHotelOffer']);
Route::post('/search', [FlightBookingController::class, 'searchFlights']);
Route::post('/search', [FlightBookingController::class, 'searchFlights']);

Route::post('/makePayment', [PaymentController::class, 'createPayment']);