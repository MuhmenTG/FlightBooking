<?php
declare(strict_types=1);

namespace App\Services;
use App\Models\Payment;
use Stripe\BalanceTransaction;


class PaymentService {
    
    public static function createCharge(int $amount, string $currency, string $cardNumber, string $expYear, string $expMonth, string $cvc, string $description) 
    {
        $stripe = PaymentService::createCardRecord($cardNumber, $expYear, $expMonth, $cvc);
        
        if ($amount < 0) {
            throw new \InvalidArgumentException('Invalid amount.');
        }

        if (!in_array($currency, ['usd', 'eur', 'dkk'])) {
            throw new \InvalidArgumentException('Invalid currency.');
        }
        
        $charge =  $stripe->charges->create([
            'amount' => $amount,
            'currency' => $currency,
            'source' => 'tok_mastercard',
        ]);

        if($charge){    
            $payment = New Payment();
            $payment->setPaymentAmount($charge->amount);       
            $payment->setPaymentCurrency($currency);
            $payment->setPaymentType(Payment::PAYMENT_TYPE);
            $payment->setPaymentStatus(Payment::PAYMENT_STATUS_COMPLETED);
            $payment->setPaymentInfoId($charge->id);
            $payment->setPaymentMethod("MasterCard");
            $payment->setPaymentGatewayProcessor("Stripe Api");
            $payment->setNoteComments($description);
            $payment->save();
        }

        return $payment;
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
      
        $stripe->tokens->create([
          'card' => [
            'number' => $cardNumber,
            'exp_month' => $expMonth,
            'exp_year' => $expYear,
            'cvc' => $cvc,
          ],
        ]);

        return $stripe;
    }
}
