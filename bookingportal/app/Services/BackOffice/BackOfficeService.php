<?php

declare(strict_types=1);
namespace App\Services\BackOffice;

use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use App\Repositories\IBackOfficeRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class BackOfficeService implements IBackOfficeService {
    protected $backOfficeRepository;

    public function __construct(IBackOfficeRepository $backOfficeRepository)
    {
        $this->backOfficeRepository = $backOfficeRepository;
    }
    
    public function createAgent(string $firstName, string $lastName, string $email, string $status, int $isAdmin, int $isAgent): UserAccount
    {
        $existingUserAccount = $this->backOfficeRepository->findAgentByEmail($email);
        if ($existingUserAccount) {
            throw new Exception("Agent with the same email already exists.");
        }
    
        $password = "systemAgentUser";
        $hashedPassword = Hash::make($password);
    
        $userAccount = $this->backOfficeRepository->createAgent($hashedPassword, $firstName, $lastName, $email, $isAdmin, $isAgent, $status);
    
        return $userAccount;
    }
    
    public function editAgent(int $agentId, string $firstName, string $lastName, string $email, string $status, int $isAdmin, int $isAgent): UserAccount
    {
        $existingUserAccount = $this->backOfficeRepository->findAgentById($agentId);
        if (!$existingUserAccount) {
            throw new Exception("Agent not found.");
        }
    
        $userAccount = $this->backOfficeRepository->updateAgent($agentId, $firstName, $lastName, $email, $isAdmin, $isAgent, $status);
    
        return $userAccount;
    }
    
    public function getAgentById(int $agentId): ?UserAccount
    {
        return $this->backOfficeRepository->findAgentById($agentId);
    }
    
    public function removeAgentAccount(int $agentId): ?UserAccount
    {
        $userAccount = $this->backOfficeRepository->findAgentById($agentId);
        if (!$userAccount) {
            throw new Exception("Agent not found.");
        }
    
        $deactivatedAccount = $this->backOfficeRepository->deandReactivateAccount($userAccount);

        return $deactivatedAccount;
    }    

    public function getAllAgents(): Collection
    {
        $agents = $this->backOfficeRepository->getActivatedAgents();
        
        if ($agents == null) {
            return false;
        }
    
        return $agents;
    }

    public function getFaqById(int $faqId) : ?Faq {
        $faq = $this->backOfficeRepository->getSpecificFaq($faqId);
        return $faq;
    }
     

    public function createOrUpdateFaq(string $question, string $answer, int $faqId = null) : Faq
    {
        if($faqId && $faqId !== null){
            $faq = Faq::byId($faqId)->first();
            if (!$faq) {
                return false;
            }
        }
        else{
            $faq = new Faq();
        }
        $faq->setQuestion($question);
        $faq->setAnswer($answer);
        $faq->save();
        return $faq;
    }

    public  function findUserEnquiryById(int $id): ?UserEnquiry
    {
        $userEnquiry = UserEnquiry::ById($id);
        if($userEnquiry){
            return $userEnquiry;
        }
        return false;
    }
}