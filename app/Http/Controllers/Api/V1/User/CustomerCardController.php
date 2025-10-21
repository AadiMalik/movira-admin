<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use App\Base\Constants\Auth\Role;
use App\Base\Constants\Setting\Settings;
use App\Http\Controllers\ApiController;
use App\Models\CustomerCard;
use App\Models\Request\Request;
use App\Transformers\User\UserTransformer;
use App\Transformers\Driver\DriverProfileTransformer;
use App\Transformers\Owner\OwnerProfileTransformer;
use App\Transformers\User\DispatcherTransformer;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class CustomerCardController extends ApiController
{
      public function list()
      {
            $user_id = auth()->user()->id;
            $customer_card = CustomerCard::where('user_id', $user_id)->get();
            return $this->respondOk($customer_card);
      }

      public function save(Request $request)
      {
            $user = auth()->user();

            $stripe_enabled = get_settings(Settings::ENABLE_STRIPE);
            $stripe_environment = get_settings(Settings::STRIPE_ENVIRONMENT);
            if ($stripe_enabled == 1 && $stripe_environment == 'live') {
                  Stripe::setApiKey(get_settings(Settings::STRIPE_LIVE_SECRET_KEY));
            } elseif ($stripe_enabled == 1 && $stripe_environment == 'test') {
                  Stripe::setApiKey(get_settings(Settings::STRIPE_TEST_SECRET_KEY));
            } else {
                  return $this->respondFailed('Stripe is not enabled');
            }
            $stripe_customer_id = $user->stripe_customer_id;
            if($stripe_enabled == 1 && $user->stripe_customer_id == null){
                  $user_update = User::find($user->id);
                  $customer = Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                  ]);
                  $user_update->stripe_customer_id = $customer->id;
                  $user_update->update();
                  $stripe_customer_id = $customer->id;
            }
            $paymentMethod = PaymentMethod::retrieve($request->payment_method_id);
            $paymentMethod->attach(['customer' => $stripe_customer_id]);

            Customer::update($stripe_customer_id, [
                  'invoice_settings' => ['default_payment_method' => $paymentMethod->id],
            ]);

            $customer_card = CustomerCard::create([
                  'user_id' => $user->id,
                  'payment_method_id' => $paymentMethod->id,
                  'brand' => $paymentMethod->card->brand,
                  'last4' => $paymentMethod->card->last4,
                  'exp_month' => $paymentMethod->card->exp_month,
                  'exp_year' => $paymentMethod->card->exp_year,
            ]);

            return $this->respondOk($customer_card);
      }

      public function delete($id)
      {
            $customer_card = CustomerCard::find($id);
            $customer_card->delete();
            return $this->respondOk($customer_card);
      }
}
