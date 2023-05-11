<?php

declare(strict_types=1);
namespace App\Services;

use App\Models\Faq;
use App\Models\FlightBooking;
use App\Models\PassengerInfo;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use Illuminate\Support\Facades\Hash;

class BackOfficeService {

    public static function createOrEditAgent(string $firstName, string $lastName, string $email, string $status, int $isAdmin, int $isAgent, $userId = null) : UserAccount
    {
        if ($userId) {
            $userAccount = UserAccount::ById($userId)->first();
            if (!$userAccount) {
                return false;
            }
        } else {
            $userAccount = UserAccount::ByEmail($email)->first();
            if ($userAccount) {
                return false;
            }
            $password = "systemAgentUser";
            $userAccount = new UserAccount();
            $userAccount->setPassword(Hash::make($password));
        }

        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setIsAgent($isAgent);
        $userAccount->setIsAdmin($isAdmin);
        $userAccount->setStatus($status);
        $userAccount->save();

        return $userAccount;
    }

    public static function getSpecificAgentDetails(int $userId) : UserAccount
    {
        $user = UserAccount::ById($userId)->first();
        if ($user) {
            return $user;
        }

        return false;
    }

    public static function removeAgentAccount(int $userId) : bool
    {
        $user = UserAccount::ById($userId)->first();
        if ($user) {
            $user->setStatus(0);
            $user->getDeactivatedAt(time());
            $user->save();
            return true;
        }
        return false;
    }

    public static function createOrUpdateFaq(string $question, string $answer, int $faqId = null) : Faq
    {
        if($faqId && $faqId !== null){
            $faq = Faq::byId($faqId)->first();
            if (!$faq) {
                return false;
            }
        }
        $faq = new Faq();

        $faq->setQuestion($question);
        $faq->setAnswer($answer);
        $faq->save();
        return $faq;
    }

    public static function findBookingByReference(string $bookingReference): ?FlightBooking
    {
        $bookedFlightSegments = FlightBooking::where(FlightBooking::COL_BOOKINGREFERENCE, $bookingReference)->get();
        $bookedFlightPassenger = PassengerInfo::where(PassengerInfo::COL_PNR, $bookingReference)->get();
        
        if (!$bookedFlightSegments->isEmpty() && !$bookedFlightPassenger->isEmpty()) {
            return FlightBooking::ByBookingReference($bookingReference)->first();
        }
        
        return null;
    }

    public static function cancelBooking(FlightBooking $booking): ?FlightBooking
    {
        $booking->setAttribute(FlightBooking::COL_ISCANCELLED, 1);
        $isflightSegmentsCancelled = $booking->save();
        
        if (!$isflightSegmentsCancelled) {
            return null;
        }
        
        return FlightBooking::ByBookingReference($booking->getAttribute(FlightBooking::COL_BOOKINGREFERENCE))->ByIsCancelled(1)->get();
    }

    public static function cancelBookingPassengers(FlightBooking $booking): ?PassengerInfo
    {
        $isPassengersCancelled = PassengerInfo::where(PassengerInfo::COL_PNR, $booking->getAttribute(FlightBooking::COL_BOOKINGREFERENCE))
            ->update([PassengerInfo::COL_ISCANCELLED => 1]);
        
        if (!$isPassengersCancelled) {
            return null;
        }
        
        return PassengerInfo::ByBookingReference($booking->getAttribute(FlightBooking::COL_BOOKINGREFERENCE))->ByIsCancelled(1)->get();
    }

    public static function findUserEnquiryById(int $id): ?UserEnquiry
    {
        $userEnquiry = UserEnquiry::ById($id);
        if($userEnquiry){
            return $userEnquiry;
        }
        return false;
    }

    public static function deactivateEmployee(int $userId){
        
        $user = UserAccount::ById($userId)->first();
        if(!$user){
            return false;
        }

        $user->setStatus(0);
        $user->getDeactivatedAt(time());
        $user->save();
        return $user;
    }
}