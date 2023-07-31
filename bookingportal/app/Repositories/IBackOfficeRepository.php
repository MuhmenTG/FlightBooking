<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\Faq;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Collection;

interface IBackOfficeRepository {

    public function createAgent(string $password, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status): UserAccount;

    public function findAgentById(int $id): ?UserAccount;

    public function findAgentByEmail(string $email): ?UserAccount;

    public function updateAgent(int $agentId, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status):  UserAccount;

    public function deandReactivateAccount(UserAccount $userAccount): UserAccount;
    
    public function getActivatedAgents(): Collection;

    public function getSpecificFaq(int $faqId) : ?Faq;

    public function getDeactivatedAgents(): array;

    public function getAllPayments() : Collection;

    public function getSpecificPayments(string $bookingreference, string $transactionId) : ?array;

    public function getUserByEmail(string $email) : ?UserAccount;
    
}