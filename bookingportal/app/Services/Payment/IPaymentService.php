<?php

declare(strict_types=1);

namespace App\Services\Payment;

use Stripe\BalanceTransaction;

/**
 * Interface IPaymentService
 * This interface defines the methods that a payment service must implement.
 */
interface IPaymentService
{
    /**
     * Create a charge for a payment.
     *
     * @param int $amount The amount to charge (in cents or smallest currency unit).
     * @param string $currency The currency code for the payment (e.g., "USD").
     * @param string $cardNumber The credit card number for the payment.
     * @param string $expYear The expiration year of the credit card.
     * @param string $expMonth The expiration month of the credit card.
     * @param string $cvc The card verification code (CVC) of the credit card.
     * @param string $description A description for the payment.
     * @return mixed The result of the charge creation operation.
     */
    public function createCharge(int $amount, string $currency, string $cardNumber, string $expYear, string $expMonth, string $cvc, string $description);

    /**
     * Retrieve a specific balance transaction by transaction ID.
     *
     * @param string $transactionId The ID of the balance transaction to retrieve.
     * @return BalanceTransaction The retrieved balance transaction.
     */
    public function retrieveSpecificBalanceTransaction(string $transactionId): BalanceTransaction;

    /**
     * Retrieve all payment transactions.
     *
     * @return mixed A collection or array of all payment transactions.
     */
    public function retrieveAllTransactions();
}
