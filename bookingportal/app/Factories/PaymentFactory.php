<?php
declare(strict_types=1);

namespace App\Factories;

use Stripe\Charge;
use Stripe\BalanceTransaction;
use Stripe\BalanceTransactionList;
use Stripe\Card;

class PaymentFactory {
    public static function createCharge(float $amount, string $currency, string $cardNumber, string $expYear, string $expMonth, string $cvc): Charge
    {
        $cardToken = PaymentFactory::createCardRecord($cardNumber, $expYear, $expMonth, $cvc);

        if (!is_int($amount) || $amount < 0) {
            throw new \InvalidArgumentException('Invalid amount.');
        }

        if (!in_array($currency, ['usd', 'eur', 'dkk'])) {
            throw new \InvalidArgumentException('Invalid currency.');
        }

        \Stripe\Stripe::setApiKey('your_api_key');
        return Charge::create([
            'amount' => $amount,
            'currency' => $currency,
            'source' => $cardToken,
        ]);
    }

    public static function retrieveSpecificBalanceTransaction(string $transactionId): BalanceTransaction
    {
        \Stripe\Stripe::setApiKey('your_api_key');
        return BalanceTransaction::retrieve($transactionId);
    }

    public static function retrieveAllTransactions()
    {
        \Stripe\Stripe::setApiKey('your_api_key');
        return BalanceTransaction::all();
    }

    private static function createCardRecord(string $cardNumber, string $expYear, string $expMonth, string $cvc){

        if (!ctype_digit($cardNumber) || strlen($cardNumber) < 12 || strlen($cardNumber) > 19) {
            throw new \InvalidArgumentException('Invalid card.');
        }

        if (!ctype_digit($expMonth) || $expMonth < 1 || $expMonth > 12) {

            throw new \InvalidArgumentException('Invalid expiry Date.');
        }
        
        if (!ctype_digit($expYear) || strlen($expYear) != 4 || $expYear < date('Y')) {
    
        }
        
        if (!ctype_digit($cvc) || strlen($cvc) < 3 || strlen($cvc) > 4) {
        
        }


        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
      
        $stripeCardToken =$stripe->tokens->create([
          'card' => [
            'number' => $cardNumber,
            'exp_month' => $expMonth,
            'exp_year' => $expYear,
            'cvc' => $cvc,
          ],
        ]);

        return $stripeCardToken;
    }
}
