<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\ManageBookingController;
use App\Http\Controllers\TravelAgentController;
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

//auth
Route::post('/auth/login', [AuthenticationController::class, 'loginUser']);
Route::post('/auth/logout', [AuthenticationController::class, 'logout']);

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


Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // Admin-only routes here
    Route::post('/admin/createAgent', [AdminController::class, 'createAgent']);
    Route::post('/admin/getSpecificAgentDetails', [AdminController::class, 'getSpecificAgentDetails']);
    Route::post('/admin/removeAgentAccount', [AdminController::class, 'removeAgentAccount']);
    Route::post('/admin/editAgentDetails', [AdminController::class, 'editAgentDetails']);
    Route::post('/admin/showListOfAgent', [AdminController::class, 'showListOfAgent']);
    Route::post('/admin/createNewFaq', [AdminController::class, 'createNewFaq']);
    Route::post('/admin/editFaq', [AdminController::class, 'createNewFaq']);
    Route::post('/admin/removeFaq', [AdminController::class, 'removeFaq']);
    Route::post('/admin/getSpecificFaq', [AdminController::class, 'getSpecificFaq']);
    Route::post('/admin/createNewUserRole', [AdminController::class, 'createOrEditUserRole']);
    Route::post('/admin/editUserRole', [AdminController::class, 'createOrEditUserRole']);
    Route::post('/admin/removeUserRole', [AdminController::class, 'removeUserRole']);
    Route::post('/admin/getUserRole', [AdminController::class, 'showSpecificOrAllUserRoles']);
    Route::post('/admin/showUserRoles', [AdminController::class, 'showSpecificOrAllUserRoles']);
    Route::post('/admin/resetAgentPassword', [AdminController::class, 'resetAgentPassword']);
});

Route::middleware(['auth:sanctum', 'isAgent'])->group(function () {
    // Agent-only routes here
    Route::post('/travelAgent/cancelHotel', [TravelAgentController::class, 'cancelHotelBooking']);
    Route::post('/travelAgent/cancelFlight', [TravelAgentController::class, 'cancelFlightBooking']);
    Route::post('/travelAgent/sendBooking', [TravelAgentController::class, 'resendBookingConfirmationPDF']);
    Route::post('/travelAgent/getAllUserEnquries', [TravelAgentController::class, 'getAllUserEnquiries']);
    Route::post('/travelAgent/getSpecificUserEnquiry', [TravelAgentController::class, 'getSpecificUserEnquiry']);
    Route::post('/travelAgent/setUserEnquiryStatus', [TravelAgentController::class, 'setUserEnquiryStatus']);
    Route::post('/travelAgent/answerUserEnquiry', [TravelAgentController::class, 'answerUserEnquiry']);
    Route::post('/travelAgent/removeUserEnquiry', [TravelAgentController::class, 'removeUserEnquiry']);
    Route::post('/travelAgent/editAgentDetails', [AdminController::class, 'editAgentDetails']);
});








