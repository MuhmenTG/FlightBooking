<?php

declare(strict_types=1);
namespace App\Services\BackOffice;

use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use App\Repositories\BackOfficeRepository;
use Exception;
use Illuminate\Support\Facades\Hash;

class BackOfficeService implements IBackOfficeService {
    protected $backOfficeRepository;

    public function __construct(BackOfficeRepository $backOfficeRepository)
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
    
    public function editAgent(int $agentId, string $firstName, string $password, string $lastName, string $email, string $status, int $isAdmin, int $isAgent): UserAccount
    {
        $existingUserAccount = $this->backOfficeRepository->findAgentById($agentId);
        if (!$existingUserAccount) {
            throw new Exception("Agent not found.");
        }
    
        $userAccount = $this->backOfficeRepository->updateAgent($agentId, Hash::make($password), $firstName, $lastName, $email, $isAdmin, $isAgent, $status);
    
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

    public function getAllAgents(): array|false
    {
        $agents = [
            'activatedAgents' => $this->backOfficeRepository->getActivatedAgents(),
            'deactivatedAgents' => $this->backOfficeRepository->getDeactivatedAgents()
        ]; 

        if (empty($agents['activated_agents']) && empty($agents['deactivated_agents'])) {
            return false;
        }
    
        return $agents;
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

    public  function findUserEnquiryById(int $id): ?UserEnquiry
    {
        $userEnquiry = UserEnquiry::ById($id);
        if($userEnquiry){
            return $userEnquiry;
        }
        return false;
    }
}