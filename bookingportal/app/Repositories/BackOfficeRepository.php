<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\Faq;
use App\Models\Payment;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Collection;

class BackOfficeRepository implements IBackOfficeRepository{

    /**
    * {@inheritDoc}
    */
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

    /**
    * {@inheritDoc}
    */
    public function findAgentById(int $id): ?UserAccount
    {
        return UserAccount::find($id);
    }

    /**
    * {@inheritDoc}
    */
    public function getUserByEmail(string $email) : ?UserAccount
    {
        $user = UserAccount::ByEmail($email)->first();
        return $user;
    }

    /**
    * {@inheritDoc}
    */
    public function findAgentByEmail(string $email): ?UserAccount
    {
        return UserAccount::where('email', $email)->where(UserAccount::COL_DEACTIVATEDAT, 0)->first();
    }

    /**
    * {@inheritDoc}
    */
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

    /**
    * {@inheritDoc}
    */
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

    /**
    * {@inheritDoc}
    */
    public function getActivatedAgents(): Collection
    {
        $agents = UserAccount::all();
        return $agents;
    }

    /**
    * {@inheritDoc}
    */
    public function getSpecificFaq(int $faqId) : ?Faq {
        $specificFaq = Faq::byId($faqId)->first();
        if($specificFaq){
            return $specificFaq;
        }
        return null;
    }

    /**
    * {@inheritDoc}
    */
    public function getAllFaq() : Collection {
        $faqs = Faq::all();
        return $faqs;
    }
    
    /**
    * {@inheritDoc}
    */
    public function createOrUpdateFaq(string $question, string $answer, int $faqId = null): Faq
    {
        if ($faqId !== null) {
            $faq = Faq::ById($faqId)->first();
            if (!$faq) {
                throw new \InvalidArgumentException("FAQ with ID $faqId not found.");
            }
        } else {
            $faq = new Faq();
        }

        $faq->setQuestion($question);
        $faq->setAnswer($answer);
        $faq->save();

        return $faq;
    }

    /**
    * {@inheritDoc}
    */
    public function getDeactivatedAgents(): array
    {
        $agents = UserAccount::where(UserAccount::COL_STATUS, 0)->get()->ToArray();
        return $agents;
    }

    /**
    * {@inheritDoc}
    */
    public function getAllPayments() : Collection{
        $payments = Payment::all();
        return $payments;
    }

    /**
    * {@inheritDoc}
    */
    public function getSpecificPayments(string $bookingreference, string $transactionId) : ?array{
        $payment = Payment::where(Payment::COL_CONNECTEDBOOKINGREFERENCE, $bookingreference)->first();
        var_dump($payment);exit;
        return $payment;
    }
    
}
