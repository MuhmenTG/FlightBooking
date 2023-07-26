<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\Faq;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Collection;

class BackOfficeRepository implements IBackOfficeRepository{

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

    public function updateAgent(int $agentId, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status):  UserAccount
    {
        $userAccount = UserAccount::ById($agentId)->first();
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
    
    public function getActivatedAgents(): Collection
    {
        $agents = UserAccount::all();
        return $agents;
    }

    public function getSpecificFaq(int $faqId) : ?Faq {
        $specificFaq = Faq::byId($faqId)->first();
        if($specificFaq){
            return $specificFaq;
        }
        return null;
    }

    public function getDeactivatedAgents(): array
    {
        $agents = UserAccount::where(UserAccount::COL_STATUS, 0)->get()->ToArray();
        return $agents;
    }
}