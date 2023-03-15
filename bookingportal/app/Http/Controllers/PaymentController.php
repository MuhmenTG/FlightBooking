<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;
use Stripe;

class PaymentController extends Controller
{
  //
  public function createPayment(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'number'      => 'required|string',
      'exp_month'   => 'required|string',
      'exp_year'    => 'required|string',
      'cvc'         => 'required|string',
      'amount'      => 'required|string',
      'description' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json("Validation Failed", 400);
    }


    $cardNumber = $request->input('number');
    $expMonth = $request->input('exp_month');
    $expYear = $request->input('exp_year');
    $cvc = $request->input('cvc');
    $amount = $request->input('amount');
    $description = $request->input('description');
   

    try {
      $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
      
      $stripe->tokens->create([
        'card' => [
          'number' => $cardNumber,
          'exp_month' => $expMonth,
          'exp_year' => $expYear,
          'cvc' => $cvc,
        ],
      ]);
      
      $charge =  $stripe->charges->create([
        'amount' => $amount,
        'currency' => 'dkk',
        'source' => 'tok_mastercard',
        'description' => $description,
      ]);
      
      return response()->json([$charge->status], 200);
    } catch (Exception $ex) {
    
      return response()->json([['response' => 'error']], 500);
    }
  }
}
