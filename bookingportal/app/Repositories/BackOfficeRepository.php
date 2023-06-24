<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\UserAccount;
use App\Models\UserEnquiry;

class BackOfficeRepository {

    public function createAgent(string $password, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status): UserAccount
    {
        $userAccount = new UserAccount();
        $userAccount->setPassword($password);
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setIsAgent($isAgent);
        $userAccount->setIsAdmin($isAdmin);
        $userAccount->setStatus($status);
        $userAccount->save();
        return $userAccount;
    }

    public function findAgentById(int $id): ?UserAccount
    {
        return UserAccount::find($id);
    }

    public function findAgentByEmail(string $email): ?UserAccount
    {
        return UserAccount::where('email', $email)->first();
    }

    public function updateAgent(int $userId, string $password, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status):  UserAccount
    {
        $userAccount = UserAccount::ById($userId)->first();
        $userAccount->setPassword($password);
        $userAccount->setFirstName($firstName);
        $userAccount->setLastName($lastName);
        $userAccount->setEmail($email);
        $userAccount->setIsAgent($isAgent);
        $userAccount->setIsAdmin($isAdmin);
        $userAccount->setStatus($status);
        $userAccount->save();
        return $userAccount;
    }

    public function deandReactivateAccount(UserAccount $userAccount): UserAccount
    {
        if ($userAccount->getStatus() == 1) {
            $userAccount->setStatus(0);
        } else {
            $userAccount->setStatus(1);
        }
    
        $userAccount->setDeactivatedAt(time());
        $userAccount->save();
        return $userAccount;
    }
    
    public function getActivatedAgents(): array
    {
        $agents = UserAccount::where(UserAccount::COL_STATUS, 1)->get()->ToArray();
        return $agents;
    }

    public function getDeactivatedAgents(): array
    {
        $agents = UserAccount::where(UserAccount::COL_STATUS, 0)->get()->ToArray();
        return $agents;
    }
}