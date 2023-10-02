<?php

declare(strict_types=1);
namespace App\Repositories;

use App\Models\Faq;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IBackOfficeRepository
 * This interface defines the methods that a back office repository must implement.
 */
interface IBackOfficeRepository {

    /**
     * Create a new agent user account.
     *
     * @param string $password The password for the new user account.
     * @param string $firstName The first name of the new user.
     * @param string $lastName The last name of the new user.
     * @param string $email The email address of the new user.
     * @param int $isAdmin Flag indicating if the user is an admin (1 for admin, 0 for non-admin).
     * @param int $isAgent Flag indicating if the user is an agent (1 for agent, 0 for non-agent).
     * @param string $status The status of the new user account.
     * @return UserAccount The created user account.
     */
    public function createAgent(string $password, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status): UserAccount;

    /**
     * Find an agent user account by ID.
     *
     * @param int $id The ID of the agent user account to retrieve.
     * @return UserAccount|null The agent user account if found, or null if not found.
     */
    public function findAgentById(int $id): ?UserAccount;

    /**
     * Find an agent user account by email.
     *
     * @param string $email The email address of the agent user account to retrieve.
     * @return UserAccount|null The agent user account if found, or null if not found.
     */
    public function findAgentByEmail(string $email): ?UserAccount;

    /**
     * Update an agent user account.
     *
     * @param int $agentId The ID of the agent user account to update.
     * @param string $firstName The updated first name.
     * @param string $lastName The updated last name.
     * @param string $email The updated email address.
     * @param int $isAdmin Flag indicating if the user is an admin (1 for admin, 0 for non-admin).
     * @param int $isAgent Flag indicating if the user is an agent (1 for agent, 0 for non-agent).
     * @param string $status The updated status of the user account.
     * @return UserAccount The updated user account.
     */
    public function updateAgent(int $agentId, string $firstName, string $lastName, string $email, int $isAdmin, int $isAgent, string $status): UserAccount;

    /**
     * Deactivate or reactivate a user account.
     *
     * @param UserAccount $userAccount The user account to deactivate or reactivate.
     * @return UserAccount The deactivated or reactivated user account.
     */
    public function deandReactivateAccount(UserAccount $userAccount): UserAccount;

    /**
     * Get a collection of activated agents.
     *
     * @return Collection A collection of activated agents.
     */
    public function getActivatedAgents(): Collection;

    /**
     * Get a specific FAQ by FAQ ID.
     *
     * @param int $faqId The ID of the FAQ to retrieve.
     * @return Faq|null The FAQ if found, or null if not found.
     */
    public function getSpecificFaq(int $faqId): ?Faq;

    /**
     * Get a collection of all FAQs.
     *
     * @return Collection A collection of all FAQs.
     */
    public function getAllFaq(): Collection;

    /**
     * Create or update an FAQ.
     *
     * @param string $question The question for the FAQ.
     * @param string $answer The answer for the FAQ.
     * @param int|null $faqId The ID of the FAQ to update (optional, null for create).
     * @return Faq The created or updated FAQ.
     */
    public function createOrUpdateFaq(string $question, string $answer, int $faqId = null): Faq;

    /**
     * Get an array of deactivated agents.
     *
     * @return array An array of deactivated agents.
     */
    public function getDeactivatedAgents(): array;

    /**
     * Get a collection of all payments.
     *
     * @return Collection A collection of all payments.
     */
    public function getAllPayments(): Collection;

    /**
     * Get specific payment information by booking reference and transaction ID.
     *
     * @param string $bookingreference The booking reference associated with the payment.
     * @param string $transactionId The transaction ID associated with the payment.
     * @return array|null The payment information if found, or null if not found.
     */
    public function getSpecificPayments(string $bookingreference, string $transactionId): ?array;

    /**
     * Get a user account by email address.
     *
     * @param string $email The email address of the user account to retrieve.
     * @return UserAccount|null The user account if found, or null if not found.
     */
    public function getUserByEmail(string $email): ?UserAccount;
}
