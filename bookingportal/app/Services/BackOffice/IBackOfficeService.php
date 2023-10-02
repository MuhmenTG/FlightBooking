<?php

declare(strict_types=1);
namespace App\Services\BackOffice;

use App\Models\Faq;
use App\Models\UserAccount;
use App\Models\UserEnquiry;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IBackOfficeService
 * This interface defines the methods that a back office service must implement.
 */
interface IBackOfficeService
{
    /**
     * Create a new agent account.
     *
     * @param string $firstName The first name of the agent.
     * @param string $lastName The last name of the agent.
     * @param string $email The email address of the agent.
     * @param string $status The status of the agent.
     * @param int $isAdmin Whether the agent is an admin (0 or 1).
     * @param int $isAgent Whether the agent is an agent (0 or 1).
     * @return UserAccount The created agent account.
     */
    public function createAgent(
        string $firstName,
        string $lastName,
        string $email,
        string $status,
        int $isAdmin,
        int $isAgent
    ): UserAccount;

    /**
     * Edit an existing agent account.
     *
     * @param int $agentId The ID of the agent to edit.
     * @param string $firstName The updated first name of the agent.
     * @param string $lastName The updated last name of the agent.
     * @param string $email The updated email address of the agent.
     * @param string $status The updated status of the agent.
     * @param int $isAdmin Whether the agent is an admin (0 or 1).
     * @param int $isAgent Whether the agent is an agent (0 or 1).
     * @return UserAccount The updated agent account.
     */
    public function editAgent(
        int $agentId,
        string $firstName,
        string $lastName,
        string $email,
        string $status,
        int $isAdmin,
        int $isAgent
    ): UserAccount;

    /**
     * Get an agent account by ID.
     *
     * @param int $agentId The ID of the agent to retrieve.
     * @return UserAccount|null The agent account if found, or null if not found.
     */
    public function getAgentById(int $agentId): ?UserAccount;

    /**
     * Remove an agent account by ID.
     *
     * @param int $agentId The ID of the agent to remove.
     * @return UserAccount|null The removed agent account if successful, or null if not found.
     */
    public function removeAgentAccount(int $agentId): ?UserAccount;

    /**
     * Get all agent accounts.
     *
     * @return Collection A collection of agent accounts.
     */
    public function getAllAgents(): Collection;

    /**
     * Get an FAQ by ID.
     *
     * @param int $faqId The ID of the FAQ to retrieve.
     * @return Faq|null The FAQ if found, or null if not found.
     */
    public function getFaqById(int $faqId): ?Faq;

    /**
     * Get all FAQs.
     *
     * @return Collection|null A collection of FAQs, or null if none are found.
     */
    public function getAllFaqs(): ?Collection;

    /**
     * Create or update an FAQ.
     *
     * @param string $question The question of the FAQ.
     * @param string $answer The answer to the FAQ.
     * @param int|null $id The ID of the FAQ to update (optional).
     * @return Faq The created or updated FAQ.
     */
    public function createOrUpdateFaq(
        string $question,
        string $answer,
        ?int $id = null
    ): Faq;

    /**
     * Find a user enquiry by ID.
     *
     * @param int $id The ID of the user enquiry to find.
     * @return UserEnquiry|null The user enquiry if found, or null if not found.
     */
    public function findUserEnquiryById(int $id): ?UserEnquiry;

    /**
     * Get all payments.
     *
     * @return mixed A collection or array of all payments.
     */
    public function getPayments();

    /**
     * Get specific payment transactions by booking reference and transaction ID.
     *
     * @param string $bookingReference The booking reference associated with the payment.
     * @param string $transactionId The transaction ID to retrieve.
     * @return array|null The specific payment transactions if found, or null if not found.
     */
    public function getSpecificPayments(string $bookingreference, string $transactionId): ?array;
}
