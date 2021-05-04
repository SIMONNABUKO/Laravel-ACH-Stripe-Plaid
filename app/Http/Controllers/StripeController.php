<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use AlexVargash\LaravelStripePlaid\StripePlaid;
use Stripe;
use App\Models\Customer;
use App\Models\Charge;
use Illuminate\Support\Facades\Storage;

class StripeController extends Controller
{
    public function create_token()
    {
        $client_user_id = 'user_good';
        $stripePlaid = new StripePlaid();
        $linkToken   = $stripePlaid->createLinkToken($client_user_id);
        return response()->json($linkToken);
    }
   
    public function exchange_tokens(Request $request)
    {
        $accountId   = $request->client_id;
        $amount = $request->amount;
        $publicToken = $request->public_token;
        $stripePlaid = new StripePlaid();
        $stripeToken = $stripePlaid->getStripeToken($publicToken, $accountId);
        $this->createCustomer($stripeToken, $amount);
    }

    public function createCustomer($bankToken, $amount)
    {
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $customer = $stripe->customers->create([
            'source' => $bankToken,
            'description' => 'First Customer to create with stripe',
      
        ]);
        $jsonstring = \json_encode($customer);
        $details = str_replace('Stripe\Customer JSON:', '', $jsonstring);
        $cust = new Customer;
        $cust->details = $details;
        $cust->save();
        $json = \json_decode($details);
        $customer_id= $json->id;

        $this->chargeCustomer($customer_id, $amount);
    }

    public function chargeCustomer($customer_id, $amount)
    {
        $total_charge = $amount*100;
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $charge = $stripe->charges->create([
            'amount' => (int)$total_charge,
            'currency' => 'usd',
            'customer'=>$customer_id,
            'description' => 'another charge',
          ]);

        $charg = new Charge;
        $charg->details = \json_encode($charge);
        $charg->save();
        $data = ['status'=>'success', 'message'=>"Payment Successful"];
        return response()->json($data);
    }
}
