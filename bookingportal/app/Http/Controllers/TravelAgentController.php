<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Resources\FlightConfirmationResource;
use App\Http\Resources\PassengerResource;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\SupportRequestResource;
use App\Mail\ISendEmailService;
use App\Models\UserAccount;
use App\Services\BackOffice\IBackOfficeService;
use App\Services\Booking\IBookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TravelAgentController extends Controller
{
    //
    protected $IBookingService;
    protected $IBackOfficeService;
    protected $IEmailSendService;



    public function __construct(IBookingService $IBookingService, ISendEmailService $IEmailSendService, IBackOfficeService $IBackOfficeService)
    {
        $this->IBookingService = $IBookingService;
        $this->IEmailSendService = $IEmailSendService;
        $this->IBackOfficeService = $IBackOfficeService;
    }

    public function cancelFlightBooking(string $bookingReference)
    {
        $bookedFlightSegments = $this->IBookingService->getFlightSegmentsByBookingReference($bookingReference);
        $bookedFlightPassenger = $this->IBookingService->getFlightPassengersByPNR($bookingReference);

        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            $this->IBookingService->cancelFlightBooking($bookingReference);

            $cancelledBooking = $this->IBookingService->getFlightSegmentsByBookingReference($bookingReference);
            $cancelledBookingPassengers = $this->IBookingService->getFlightPassengersByPNR($bookingReference);

            return ResponseHelper::jsonResponseMessage([
                'cancellation' => 'booking is cancelled',
                'PAX' => $cancelledBooking,
                'flight' => $cancelledBookingPassengers
            ], Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage(ResponseHelper::BOOKING_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }

    public function resendBookingConfirmationPDF(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'bookingReference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $email = $request->input('email');
        $bookingReference = $request->input('bookingReference');

        $bookingInfo = $this->IBookingService->retrieveBookingInformation($bookingReference);

        if ($bookingInfo) {
            $bookedFlightSegments = FlightConfirmationResource::collection($bookingInfo['flight']);
            $bookedFlightPassenger = PassengerResource::collection($bookingInfo['passengers']);
            $paymentDetails = new PaymentResource($bookingInfo['payment']);

            $bookingComplete = [
                'passengers' => $bookedFlightPassenger,
                'flight' => $bookedFlightSegments,
                'payment' => $paymentDetails,
            ];

            $pdfContent = $this->IBookingService->generateBookingConfirmationPDF($bookingComplete);

            $isSend = $this->IEmailSendService->sendEmailWithAttachments($email, $email, "We're resending your electronic ticket", "Please see your attached tickets", $pdfContent);

            if ($isSend) {
                return ResponseHelper::jsonResponseMessage("Booking confirmation has been sent", Response::HTTP_OK);
            }

            return ResponseHelper::jsonResponseMessage('Something went wrong while sending confirmation', Response::HTTP_BAD_REQUEST);
        }
    }

    public function getAllUserEnquiries()
    {
        $userEnquiries = $this->IBookingService->getAllUserEnquiries();

        if ($userEnquiries->isEmpty()) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::COSTUMER_ENQUIRY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        $userEnquiries = SupportRequestResource::collection($userEnquiries);

        return ResponseHelper::jsonResponseMessage($userEnquiries, Response::HTTP_OK, "enquiryResponses");
    }

    public function getSpecificUserEnquiry(int $enquiryId)
    {
        $specificUserEnquiry = $this->IBookingService->getUserEnquiryById($enquiryId);

        if (!$specificUserEnquiry) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::COSTUMER_ENQUIRY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        $specificUserEnquiry = new SupportRequestResource($specificUserEnquiry);

        return ResponseHelper::jsonResponseMessage($specificUserEnquiry, Response::HTTP_OK, "enquiryResponse");
    }

    public function answerUserEnquiry(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'responseMessageToUser' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $enquiryId = intval($request->input('id'));
        $responseMessageToUser = $request->input('responseMessageToUser');

        $specificUserEnquiry = $this->IBookingService->getUserEnquiryById($enquiryId);

        if (!$specificUserEnquiry) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::COSTUMER_ENQUIRY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        $emailSent = $this->IEmailSendService->sendEmailWithAttachments(
            $specificUserEnquiry->getName(),
            $specificUserEnquiry->getEmail(),
            $specificUserEnquiry->getSubject(),
            $responseMessageToUser
        );

        if ($emailSent) {
            return ResponseHelper::jsonResponseMessage("Email replied", Response::HTTP_OK);
        }

        return ResponseHelper::jsonResponseMessage('Email could not be sent', Response::HTTP_BAD_REQUEST);
    }

    public function editPassengerInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'                => 'required|integer',
            'bookingReference'  => 'required|string',
            'firstName'         => 'required|string',
            'lastName'          => 'required|string',
            'dateOfBirth'       => 'required|string',
            'email'             => 'required|string',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::validationErrorResponse($validator->errors());
        }

        $id = intval($request->input('id'));
        $bookingReference = $request->input('bookingReference');
        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $dateOfBirth = $request->input('dateOfBirth');
        $email = $request->input('email');


        $passenger = $this->IBookingService->getSpecificPassengerInBooking($id, $bookingReference);

        if (!$passenger) {
            return ResponseHelper::jsonResponseMessage('Passenger not found', Response::HTTP_NOT_FOUND, "UpdatedPassenger");
        }

        $passenger = $this->IBookingService->updatePassenger($passenger, $firstName, $lastName, $dateOfBirth, $email);

        $passenger = new PassengerResource($passenger);

        return ResponseHelper::jsonResponseMessage($passenger, Response::HTTP_OK, 'updatedPassengerInfo');
    }

    public function setUserEnquiryStatus(int $enquiryId)
    {

        $specificUserEnquiry = $this->IBookingService->getUserEnquiryById($enquiryId);

        if (!$specificUserEnquiry) {
            return ResponseHelper::jsonResponseMessage(ResponseHelper::COSTUMER_ENQUIRY_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }

        $specificUserEnquiry->setIsSolved(1);
        $specificUserEnquiry->save();

        $specificUserEnquiry = new SupportRequestResource($specificUserEnquiry);

        return ResponseHelper::jsonResponseMessage($specificUserEnquiry, Response::HTTP_OK, 'solvedUserEnquiry');

    }

    public function getAllFlightBookings()
    {

        $payment = $this->IBookingService->getAllConfirmedBookings();

        if ($payment == null) {
            return ResponseHelper::jsonResponseMessage('There is not any booking info avaliable', Response::HTTP_NOT_FOUND);
        }

        return ResponseHelper::jsonResponseMessage($payment, Response::HTTP_OK, 'bookings');
    }

    public function getAllPaymentTransactions()
    {
        $payment = $this->IBackOfficeService->getPayments();

        if ($payment == null) {
            return ResponseHelper::jsonResponseMessage('There is not any payment info avaliable', Response::HTTP_NOT_FOUND);
        }

        $payment = PaymentResource::collection($payment);

        return ResponseHelper::jsonResponseMessage($payment, Response::HTTP_OK, 'payments');
    }

    public function editAgentDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|string',
        ]);


        if ($validator->fails()) {
            return ResponseHelper::jsonResponseMessage($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $firstName = $request->input('firstName');
        $lastName = $request->input('lastName');
        $email = $request->input('email');


        $loggedInUserId = $request->user()->id;

        $userAccount = UserAccount::ById($loggedInUserId)->first();
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->save();

        return ResponseHelper::jsonResponseMessage($userAccount, 400);
    }


}