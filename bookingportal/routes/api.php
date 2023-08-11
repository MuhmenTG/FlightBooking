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

Route::prefix('public')->group(function () {
    Route::get('retriveBooking/{bookingReference}', [PublicSiteController::class, 'retrieveBookingInformation']);
    Route::post('contactform', [PublicSiteController::class, 'sendEnquirySupport']);
    Route::get('getAllFaqs', [PublicSiteController::class, 'getAllFaqs']);
    Route::get('/getSpecificFaq/{faqId}', [AdminController::class, 'getSpecificFaq']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post('/admin/createAgent', [AdminController::class, 'createAgent']);
    Route::post('/admin/editAgentDetails', [AdminController::class, 'editAgent']);
    Route::get('/admin/getSpecificAgentDetails/{agentId}', [AdminController::class, 'getSpecificAgentDetails']);
    Route::put('/admin/setAgentAccountToDeactiveOrActive/{agentId}', [AdminController::class, 'deOrReactivateAgentAccount']);
    Route::get('/admin/showListOfTravelAgents', [AdminController::class, 'showListOfTravelAgents']);
    Route::post('/admin/createNewFaq', [AdminController::class, 'createFaq']);
    Route::post('/admin/editFaq', [AdminController::class, 'editFaq']);
    Route::get('/admin/getSpecificFaq/{faqId}', [AdminController::class, 'getSpecificFaq']);
    Route::get('/admin/getAllFaqs', [PublicSiteController::class, 'getAllFaqs']);
    Route::delete('/admin/removeFaq/{faqId}', [AdminController::class, 'removeFaq']);
    Route::post('/admin/resetAgentPassword', [AdminController::class, 'resetAgentPassword']);
});

Route::middleware(['auth:sanctum', 'isAgent'])->group(function () {
    Route::get('/travelAgent/getAllFlightBookings', [TravelAgentController::class, 'getAllFlightBookings']);
    Route::get('/travelAgent/getBooking/{bookingReference}', [PublicSiteController::class, 'retrieveBookingInformation']);
    Route::get('/travelAgent/getAllPaymentTransactions', [TravelAgentController::class, 'getAllPaymentTransactions']);
    Route::post('/travelAgent/resendBookingConfirmationPDF', [TravelAgentController::class, 'resendBookingConfirmationPDF']);
    Route::get('/travelAgent/getSpecificPaymentTransactions/{bookingReference}/{paymentId}', [TravelAgentController::class , 'getSpecificPaymentTransactions']);
    Route::post('/travelAgent/editPassengerInformation', [TravelAgentController::class, 'editPassengerInformation']);
    Route::get('/travelAgent/cancelFlight/{flightBookingReference}', [TravelAgentController::class, 'cancelFlightBooking']);    
   Route::post('/travelAgent/answerUserEnquiry', [TravelAgentController::class, 'answerUserEnquiry']);
    Route::put('/travelAgent/setUserEnquiryStatus/{enquiryId}', [TravelAgentController::class, 'setUserEnquiryStatus']);
    Route::delete('/travelAgent/removeUserEnquiry/{enquiryId}', [TravelAgentController::class, 'removeUserEnquiry']);
    Route::get('/travelAgent/getAllUserEnquries', [TravelAgentController::class, 'getAllUserEnquiries']);
    Route::get('/travelAgent/getSpecificUserEnquiry/{enquiryId}', [TravelAgentController::class, 'getSpecificUserEnquiry']);
    Route::post('/travelAgent/editOwnAgentDetails', [TravelAgentController::class, 'editAgentDetails']);
});





