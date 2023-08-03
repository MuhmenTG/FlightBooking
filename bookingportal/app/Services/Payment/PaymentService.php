<?php

declare(strict_types=1);

namespace App\Services\Payment;
use App\Models\Payment;
use App\Repositories\ITravelAgentRepository;
use Stripe\BalanceTransaction;


class PaymentService implements IPaymentService {

    protected $bookingRepository;

    public function __construct(ITravelAgentRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }
    
    public function createCharge(int $amount, string $currency, string $cardNumber, string $expireYear, string $expireMonth, string $cvc, string $bookingreference) 
    {
        
        $stripe = $this->createCardRecord($cardNumber, $expireYear, $expireMonth, $cvc);
        
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        $charge = $stripe->charges->create([
            'amount' => $amount * 100,
            'currency' => 'dkk',
            'source' => 'tok_mastercard',
            'description' => $bookingreference,
        ]);
        
        
        if($charge){    
           $payment = $this->bookingRepository->createPayment($charge, $amount, $currency, $bookingreference);
        }
        
        $payment = Payment::ByPaymentTransactionId($charge->id)->first();
        return $payment;
    }

    private function createCardRecord(string $cardNumber, string $expYear, string $expMonth, string $cvc){

        if (!ctype_digit($cardNumber) || strlen($cardNumber) < 12 || strlen($cardNumber) > 19) {
            throw new \InvalidArgumentException('Invalid card numer given Should be 12 digits long.');
        }

        if (!ctype_digit($expMonth) || $expMonth < 1 || $expMonth > 12) {

            throw new \InvalidArgumentException('Invalid expiry Date of card.');
        }
        
        if (!ctype_digit($expYear) || strlen($expYear) != 4 || $expYear < date('Y')) {
            throw new \InvalidArgumentException('Invalid expiry Date of card.');
    
        }
        
        if (!ctype_digit($cvc) || strlen($cvc) < 3 || strlen($cvc) > 4) {
            throw new \InvalidArgumentException('Invalid expiry Date of card.');
        
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

}
