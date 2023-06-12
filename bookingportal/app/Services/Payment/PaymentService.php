<?php

declare(strict_types=1);

namespace App\Services\Payment;
use App\Models\Payment;
use Stripe\BalanceTransaction;


class PaymentService implements IPaymentService {
    
    public function createCharge(int $amount, string $currency, string $cardNumber, string $expYear, string $expMonth, string $cvc, string $description) 
    {
        
        $stripe = $this->createCardRecord($cardNumber, $expYear, $expMonth, $cvc);
        
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $charge = $stripe->charges->create([
            'amount' => $amount * 100,
            'currency' => 'dkk',
            'source' => 'tok_mastercard',
            'description' => $description,
        ]);
        
        
        if($charge){    
            $payment = New Payment();
            $payment->setPaymentAmount($amount);       
            $payment->setPaymentCurrency($currency);
            $payment->setPaymentType(Payment::PAYMENT_TYPE);
            $payment->setPaymentStatus(Payment::PAYMENT_STATUS_COMPLETED);
            $payment->setPaymentInfoId($charge->id);
            $payment->setPaymentMethod("MasterCard");
            $payment->setPaymentGatewayProcessor("Stripe Api");
            $payment->setNoteComments($description);
            $payment->save();
        }
        
        $payment = Payment::ByPaymentInfoId($charge->id)->get();;
        return $payment;
    }

    public function retrieveSpecificBalanceTransaction(string $transactionId): BalanceTransaction
    {
        \Stripe\Stripe::setApiKey('your_api_key');
        return BalanceTransaction::retrieve($transactionId);
    }

    public function retrieveAllTransactions()
    {
        \Stripe\Stripe::setApiKey('your_api_key');
        return BalanceTransaction::all();
    }

    private function createCardRecord(string $cardNumber, string $expYear, string $expMonth, string $cvc){

        if (!ctype_digit($cardNumber) || strlen($cardNumber) < 12 || strlen($cardNumber) > 19) {
            throw new \InvalidArgumentException('Invalid card numer given Should be 12 digits long.');
        }

        if (!ctype_digit($expMonth) || $expMonth < 1 || $expMonth > 12) {

            throw new \InvalidArgumentException('Invalid expiry Date of card.');
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
