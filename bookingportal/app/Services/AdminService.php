<?php
namespace App\Services;

use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;

class AdminService {

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


    
}