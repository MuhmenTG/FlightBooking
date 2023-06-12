<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Payment;
use Stripe\BalanceTransaction;

interface IPaymentService
{
    public function createCharge(int $amount, string $currency, string $cardNumber, string $expYear, string $expMonth, string $cvc, string $description);
    
    public function retrieveSpecificBalanceTransaction(string $transactionId): BalanceTransaction;

    public function retrieveAllTransactions();
}