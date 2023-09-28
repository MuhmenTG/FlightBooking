<?php

declare(strict_types=1);
namespace App\Services\BackOffice;

use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use Illuminate\Database\Eloquent\Collection;

interface IBackOfficeService
{
    public function createAgent(
        string $firstName,
        string $lastName,
        string $email,
        string $status,
        int $isAdmin,
        int $isAgent
    ): UserAccount;

    public function editAgent(
        int $agentId,
        string $firstName,
        string $lastName,
        string $email,
        string $status,
        int $isAdmin,
        int $isAgent
    ): UserAccount;

    public function getAgentById(int $agentId): ?UserAccount;

    public function removeAgentAccount(int $agentId): ?UserAccount;

    public function getAllAgents(): Collection;

    public function getFaqById(int $faqId): ?Faq;

    public function getAllFaqs(): ?Collection;

    public function createOrUpdateFaq(
        string $question,
        string $answer,
        // changed 'faqId' to 'id'
        ?int $id = null
    ): Faq;

    public function findUserEnquiryById(int $id): ?UserEnquiry;

    public function getPayments();

    public function getSpecificPayments(string $bookingreference, string $transactionId): array;
}