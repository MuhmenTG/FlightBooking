<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\ManageBookingController;
use App\Models\FlightBooking;
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
Route::post('/flight/chooseFlightOffer', [FlightBookingController::class, 'chooseFlightOffer']);
Route::post('/flight/confirmFlight', [FlightBookingController::class, 'flightConfirmation']);
Route::post('/flight/payConfirmFlight', [FlightBookingController::class, 'payFlightConfirmation']);

//Hotel seaching
Route::post('/hotel/searchSelectHotel', [HotelBookingController::class, 'searchHotel']);
Route::get('/hotel/reviewSelectedHotelOfferInfo/{hotelOfferId}', [HotelBookingController::class, 'reviewSelectedHotelOfferInfo']);
Route::post('/hotel/bookHotel', [HotelBookingController::class, 'bookHotel']);

//find booking both hotel and/or flight(costumer-page)
route::post('/booking/retriveBooking', [ManageBookingController::class, 'retrieveBookingInformation']);
route::post('/booking/updateHotelGuestInfo', [HotelBookingController::class, 'changeGuestDetails']);
route::post('/booking/sendEnquirySupport', [ManageBookingController::class, 'sendEnquirySupport']);
route::post('/booking/getAllFaqs', [ManageBookingController::class, 'getAllFaqs']);

//AgentPanel
Route::post('/admin/cancelHotel', [AdminController::class, 'cancelHotelBooking']);
Route::post('/admin/cancelFlight', [AdminController::class, 'cancelFlightBooking']);
Route::post('/admin/sendBooking', [AdminController::class, 'uploadAndEmail']);

Route::post('/admin/getAllUserEnquries', [AdminController::class, 'getAllUserEnquiries']);
Route::post('/admin/getSpecificUserEnquiry', [AdminController::class, 'getSpecificUserEnquiry']);
Route::post('/admin/setUserEnquiryStatus', [AdminController::class, 'setUserEnquiryStatus']);
Route::post('/admin/answerUserEnquiry', [AdminController::class, 'answerUserEnquiry']);
Route::post('/admin/removeUserEnquiry', [AdminController::class, 'removeUserEnquiry']);

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

    Route::post('/admin/createNewFaq', [AdminController::class, 'createNewFaq']);
    Route::post('/admin/editFaq', [AdminController::class, 'createNewFaq']);
    Route::post('/admin/removeFaq', [AdminController::class, 'removeFaq']);
    Route::post('/admin/getSpecificFaq', [AdminController::class, 'getSpecificFaq']);
    
    
//});







