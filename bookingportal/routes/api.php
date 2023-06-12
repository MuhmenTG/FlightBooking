<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\FlightBookingController;
use App\Http\Controllers\HotelBookingController;
use App\Http\Controllers\ManageBookingController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\TravelAgentController;
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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('logout', [AuthenticationController::class, 'logout']);
});

Route::prefix('flight')->group(function () {
    Route::post('searchFlights', [FlightBookingController::class, 'searchFlights']);
    Route::post('chooseFlightOffer', [FlightBookingController::class, 'chooseFlightOffer']);
    Route::post('confirmFlight', [FlightBookingController::class, 'flightConfirmation']);
    Route::post('payConfirmFlight', [FlightBookingController::class, 'payFlightConfirmation']);
});

Route::prefix('hotel')->group(function () {
    
    Route::post('searchSelectHotel', [HotelBookingController::class, 'searchHotel']);
    Route::post('availiabilityOfASpecificHotel', [HotelBookingController::class, 'availiabilityOfASpecificHotel']);

    Route::get('reviewSelectedHotelOfferInfo/{hotelOfferId}', [HotelBookingController::class, 'reviewSelectedHotelOfferInfo']);
    Route::post('bookHotel', [HotelBookingController::class, 'bookHotel']);
});

Route::prefix('public')->group(function () {
    Route::get('retriveBooking/{bookingReference}', [PublicSiteController::class, 'retrieveBookingInformation']);
    Route::put('updateHotelGuestInfo/{hotelBookingReference}', [TravelAgentController::class, 'changeGuestDetails']);
    Route::post('sendEnquirySupport', [PublicSiteController::class, 'sendEnquirySupport']);
    Route::get('getAllFaqs', [PublicSiteController::class, 'getAllFaqs']);
});


Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // Admin-only routes here

    Route::post('/admin/createAgent', [AdminController::class, 'createAgent']);
    Route::put('/admin/editAgentDetails/{agentId}', [AdminController::class, 'editAgent']);
    Route::get('/admin/getSpecificAgentDetails/{agentId}', [AdminController::class, 'getSpecificAgentDetails']);
    Route::post('/admin/setAgentAccountToDeactive/{agentId}', [AdminController::class, 'setAgentAccountToDeactive']);
    Route::get('/admin/showListOfAgent', [AdminController::class, 'showListOfTravlAgent']);
    
    Route::post('/admin/createNewFaq', [AdminController::class, 'createNewFaq']);
    Route::put('/admin/editFaq/{faqId}', [AdminController::class, 'editFaq']);
    Route::delete('/admin/removeFaq/{faqId}', [AdminController::class, 'removeFaq']);
    Route::get('/admin/getSpecificFaq/{faqId}', [AdminController::class, 'getSpecificFaq']);
    Route::get('/admin/getAllfaq/{faqId}', [AdminController::class, 'getSpecificFaq']);

    Route::post('/admin/createNewUserRole', [AdminController::class, 'createOrEditUserRole']);
    Route::put('/admin/editUserRole/{roleId}', [AdminController::class, 'createOrEditUserRole']);
    Route::delete('/admin/removeUserRole/{roleId}', [AdminController::class, 'removeUserRole']);
    Route::get('/admin/getUserRole/{roleId}', [AdminController::class, 'showSpecificOrAllUserRoles']);
    Route::get('/admin/showUserRoles', [AdminController::class, 'showSpecificOrAllUserRoles']);

    Route::post('/admin/resetAgentPassword', [AdminController::class, 'resetAgentPassword']);
});

Route::middleware(['auth:sanctum', 'isAgent'])->group(function () {
    // Agent-only routes here

    Route::get('/travelAgent/getAllFlightBookings', [TravelAgentController::class, 'getAllFlightBookings']);
    Route::get('/travelAgent/getAllHotelBookings', [TravelAgentController::class, 'getAllHotelBookings']);
    Route::get('/travelAgent/getBooking/{bookingReference}', [PublicSiteController::class, 'retrieveBookingInformation']);

    Route::put('/travelAgent/cancelHotel/{hotelBookingReference}', [TravelAgentController::class, 'cancelHotelBooking']);
    Route::put('/travelAgent/cancelFlight/{flightBookingReference}', [TravelAgentController::class, 'cancelFlightBooking']);    
    Route::post('/travelAgent/sendBooking', [TravelAgentController::class, 'resendBookingConfirmationPDF']);

    Route::post('/travelAgent/answerUserEnquiry', [TravelAgentController::class, 'answerUserEnquiry']);
    Route::put('/travelAgent/setUserEnquiryStatus/{enquiryId}', [TravelAgentController::class, 'setUserEnquiryStatus']);
    Route::delete('/travelAgent/removeUserEnquiry/{enquiryId}', [TravelAgentController::class, 'removeUserEnquiry']);
    Route::get('/travelAgent/getAllUserEnquries', [TravelAgentController::class, 'getAllUserEnquiries']);
    Route::get('/travelAgent/getSpecificUserEnquiry/{enquiryId}', [TravelAgentController::class, 'getSpecificUserEnquiry']);

    Route::post('/travelAgent/editAgentDetails', [AdminController::class, 'editAgentDetails']);
});


// Tested




