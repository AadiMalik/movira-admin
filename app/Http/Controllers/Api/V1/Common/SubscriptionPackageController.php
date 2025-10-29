<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Models\SubscriptionPackage;
use App\Base\Constants\Auth\Role;
use App\Base\Constants\Setting\Settings;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use App\Http\Controllers\Api\V1\BaseController;
use App\Http\Requests\SubscribeRequest;
use App\Models\CustomerCard;
use App\Models\SubscriptionInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\Stripe;
use Stripe\Subscription;

/**
 * @group FAQ
 *
 * APIs for faq lists for user & driver
 */
class SubscriptionPackageController extends BaseController
{
      protected $subscription_package;

      public function __construct(SubscriptionPackage $subscription_package)
      {
            $this->subscription_package = $subscription_package;
      }

      /**
       * List subscription_package
       * @urlParam lat required double  latitude provided by user
       * @urlParam lng required double  longitude provided by user
       * @responseFile responses/common/subscription_package.json
       */
      public function index()
      {
            $result = $this->subscription_package->with('features')->where('is_active', 1)->get();

            return $this->respondSuccess($result);
      }

      public function subscribe(SubscribeRequest $request)
      {
            $user = auth()->user();
            $package = SubscriptionPackage::findOrFail($request->subscription_package_id);
            $customerCard = CustomerCard::findOrFail($request->customer_card_id);

            // ✅ STEP 1: Set Stripe keys
            $stripe_enabled = get_settings(Settings::ENABLE_STRIPE);
            $stripe_environment = get_settings(Settings::STRIPE_ENVIRONMENT);

            if ($stripe_enabled == 1 && $stripe_environment == 'live') {
                  Stripe::setApiKey(get_settings(Settings::STRIPE_LIVE_SECRET_KEY));
            } elseif ($stripe_enabled == 1 && $stripe_environment == 'test') {
                  Stripe::setApiKey(get_settings(Settings::STRIPE_TEST_SECRET_KEY));
            } else {
                  return $this->respondFailed('Stripe is not enabled');
            }

            // ✅ STEP 2: Ensure Stripe Customer exists
            if (!$user->stripe_customer_id) {
                  $customer = Customer::create([
                        'email' => $user->email,
                        'name'  => $user->name,
                  ]);
                  $user->update(['stripe_customer_id' => $customer->id]);
            }

            // ✅ STEP 3: Attach Payment Method (if not already attached)
            try {
                  PaymentMethod::retrieve($customerCard->payment_method_id)
                        ->attach(['customer' => $user->stripe_customer_id]);
            } catch (\Exception $e) {
                  // already attached or something else — continue safely
            }

            // ✅ STEP 4: Set as default payment method
            Customer::update($user->stripe_customer_id, [
                  'invoice_settings' => ['default_payment_method' => $customerCard->payment_method_id],
            ]);

            // ✅ STEP 5: Create Subscription
            $subscription = Subscription::create([
                  'customer' => $user->stripe_customer_id,
                  'items' => [['price' => $package->stripe_price_id]],
                  'default_payment_method' => $customerCard->payment_method_id,
                  'expand' => ['latest_invoice.payment_intent'],
            ]);

            $subscription_current = Subscription::retrieve($subscription->id);
            $periodEnd = $subscription_current->current_period_end
                  ?? $subscription_current->trial_end;
            // ✅ STEP 6: Save subscription info in users table
            $user->update([
                  'stripe_subscription_id'   => $subscription->id,
                  'subscription_package_id'  => $package->id,
                  'customer_card_id'         => $customerCard->id,
                  'subscription_ends_at'     => Carbon::createFromTimestamp($periodEnd),
            ]);

            // ✅ STEP 7: Save invoice details
            if (!empty($subscription->latest_invoice)) {
                  $invoice = $subscription->latest_invoice;
                  $paymentIntent = $invoice->payment_intent ?? null;

                  SubscriptionInvoice::create([
                        'user_id'                 => $user->id,
                        'subscription_package_id' => $package->id,
                        'customer_card_id'        => $customerCard->id,
                        'stripe_invoice_id'       => $invoice->id,
                        'stripe_payment_intent_id' => $paymentIntent ? $paymentIntent->id : null,
                        'amount'                  => $invoice->amount_paid / 100, // convert cents to dollars
                        'currency'                => strtoupper($invoice->currency),
                        'status'                  => $invoice->status,
                        'raw_payload'             => json_encode($invoice),
                  ]);
            }

            return response()->json([
                  'success' => true,
                  'subscription' => $subscription,
                  'message' => 'Subscription created successfully',
            ]);
      }

      public function subscriptionCancel()
      {
            $user = auth()->user();
            if ($user->stripe_subscription_id) {
                  $subscription = Subscription::retrieve($user->stripe_subscription_id);
                  $subscription->cancel();
            }
            return $this->respondSuccess('Subscription cancelled successfully');
      }
}
