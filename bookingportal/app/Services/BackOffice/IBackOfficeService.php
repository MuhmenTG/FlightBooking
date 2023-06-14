<?php

declare(strict_types=1);
namespace App\Services\BackOffice;
use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserEnquiry;

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
        string $password,
        string $lastName,
        string $email,
        string $status,
        int $isAdmin,
        int $isAgent
    ): UserAccount;

    public function getAgentById(int $agentId): ?UserAccount;

    public function removeAgentAccount(int $agentId): ?UserAccount;

    public function getAllAgents(): array|false;

    public static function createOrUpdateFaq(
        string $question,
        string $answer,
        ?int $faqId = null
    ): Faq;

    public function findUserEnquiryById(int $id): ?UserEnquiry;
}
