<?php

declare(strict_types=1);
namespace App\Services\BackOffice;

use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use App\Repositories\IBackOfficeRepository;
use App\Repositories\ITravelAgentRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class BackOfficeService implements IBackOfficeService {
    protected $backOfficeRepository;
    protected $travelAgentRepository;

    public function __construct(IBackOfficeRepository $backOfficeRepository, ITravelAgentRepository $TravelAgentRepository)
    {
        $this->backOfficeRepository = $backOfficeRepository;
        $this->travelAgentRepository = $TravelAgentRepository;
    }
    
    /**
    * {@inheritDoc}
    */
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
    
    
    /**
    * {@inheritDoc}
    */
    public function editAgent(int $agentId, string $firstName, string $lastName, string $email, string $status, int $isAdmin, int $isAgent): UserAccount
    {
        $existingUserAccount = $this->backOfficeRepository->findAgentById($agentId);
        if (!$existingUserAccount) {
            throw new Exception("Agent not found.");
        }
    
        $userAccount = $this->backOfficeRepository->updateAgent($agentId, $firstName, $lastName, $email, $isAdmin, $isAgent, $status);
    
        return $userAccount;
    }
    
    
    /**
    * {@inheritDoc}
    */
    public function getAgentById(int $agentId): ?UserAccount
    {
        return $this->backOfficeRepository->findAgentById($agentId);
    }
    
    
    /**
    * {@inheritDoc}
    */
    public function removeAgentAccount(int $agentId): ?UserAccount
    {
        $userAccount = $this->backOfficeRepository->findAgentById($agentId);
        if (!$userAccount) {
            throw new Exception("Agent not found.");
        }
    
        $deactivatedAccount = $this->backOfficeRepository->deandReactivateAccount($userAccount);

        return $deactivatedAccount;
    }    

    
    /**
    * {@inheritDoc}
    */
    public function getAllAgents(): Collection
    {
        $agents = $this->backOfficeRepository->getActivatedAgents();
        
        if ($agents == null) {
            return false;
        }
    
        return $agents;
    }

    
    /**
    * {@inheritDoc}
    */
    public function getFaqById(int $faqId) : ?Faq {
        $faq = $this->backOfficeRepository->getSpecificFaq($faqId);
        return $faq;
    }
     
    
    /**
    * {@inheritDoc}
    */
    public function getAllFaqs() : ?Collection{
        $faqs = $this->backOfficeRepository->getAllFaq();
        return $faqs;
    }
    
    /**
    * {@inheritDoc}
    */
    public function createOrUpdateFaq(string $question, string $answer, int $faqId = null) : Faq
    {
        $faq = $this->backOfficeRepository->createOrUpdateFaq($question, $answer, $faqId);
        return $faq;
    }
    
    /**
    * {@inheritDoc}
    */
    public function findUserEnquiryById(int $id): ?UserEnquiry
    {
        $userEnquiry = $this->travelAgentRepository->getUserEnquiryById($id);
        if($userEnquiry){
            return $userEnquiry;
        }
        return false;
    }

    /**
    * {@inheritDoc}
    */
    public function getPayments(){        
        $payments = $this->backOfficeRepository->getAllPayments();

        if($payments !== null){
            return $payments;
        }

        return [];
    }

    
    /**
    * {@inheritDoc}
    */
    public function getSpecificPayments(string $bookingreference, string $transactionId) : array{
        $payment = $this->backOfficeRepository->getSpecificPayments($bookingreference, $transactionId);

        if($payment !== null){
            return $payment;
        }

        return [];
    }
  
}
