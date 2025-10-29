<?php

namespace App\Http\Controllers\Web\Admin;

use App\Base\Constants\Setting\Settings;
use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Requests\Admin\CreateSubscriptionPackageRequest;
use Braintree\Subscription;
use Illuminate\Support\Facades\Log;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class SubscriptionPackageController extends Controller
{
    protected $subscription_package;
    public function __construct(SubscriptionPackage $subscription_package)
    {
        $this->subscription_package = $subscription_package;
    }

    public function index()
    {
        $page = 'Subscription Package';

        $main_menu = 'subscription-package';
        $sub_menu = '';
        return view('admin.subscription_package.index', compact('page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = SubscriptionPackage::query();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();
        return view('admin.subscription_package._subscription_package', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_subscription_package');
        // $cities = ServiceLocation::companyKey()->whereActive(true)->get();
        $main_menu = 'subscription-package';
        $sub_menu = '';

        return view('admin.subscription_package.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(CreateSubscriptionPackageRequest $request)
    {
        // dd($request->all());
        $stripe_enabled = get_settings(Settings::ENABLE_STRIPE);
        $stripe_environment = get_settings(Settings::STRIPE_ENVIRONMENT);
        if ($stripe_enabled == 1 && $stripe_environment == 'live') {
            Stripe::setApiKey(get_settings(Settings::STRIPE_LIVE_SECRET_KEY));
        } elseif ($stripe_enabled == 1 && $stripe_environment == 'test') {
            Stripe::setApiKey(get_settings(Settings::STRIPE_TEST_SECRET_KEY));
        } else {
            return $this->respondFailed('Stripe is not enabled');
        }
        $created_params = $request->only(['title', 'description', 'price', 'duration_type']);
        $created_params['is_active'] = 1;
        $subscription = null;

        if (empty($request->id)) {
            // 1️⃣ Create Product in Stripe
            $product = Product::create([
                'name' => $request->title,
                'description' => $request->description,
                'active' => true,
            ]);

            // 2️⃣ Create Price in Stripe
            $interval = $request->duration_type;
            $price = Price::create([
                'unit_amount' => $request->price * 100,
                'currency' => 'usd',
                'recurring' => ['interval' => $interval],
                'product' => $product->id,
            ]);

            $created_params['stripe_package_id'] = $product->id;
            $created_params['stripe_price_id'] = $price->id;

            $subscription = $this->subscription_package->create($created_params);
        } else {
            $subscription = $this->subscription_package->find($request->id);
            if (!$subscription) {
                return redirect('subscription-package')->with('error', 'Subscription package not found');
            }

            // 1️⃣ Update Product in Stripe
            if ($subscription->stripe_package_id) {
                Product::update($subscription->stripe_package_id, [
                    'name' => $request->title,
                    'description' => $request->description,
                    'active' => true,
                ]);
            }

            // 2️⃣ Update Price (Stripe prices are immutable — must create a new one)
            if ($subscription->stripe_package_id) {
                $interval = $request->duration_type;
                $price = Price::create([
                    'unit_amount' => $request->price * 100,
                    'currency' => 'usd',
                    'recurring' => ['interval' => $interval],
                    'product' => $subscription->stripe_package_id,
                ]);
                $created_params['stripe_price_id'] = $price->id;
            }

            $subscription->update($created_params);
        }

        return redirect('subscription-package')->with('success', 'subscription package added succesfully');
    }

    public function getById($id)
    {
        $page = trans('pages_names.add_subscription_package');
        $main_menu = 'subscription-package';
        $sub_menu = '';
        $subscription_package = $this->subscription_package->find($id);

        return view('admin.subscription_package.create', compact('subscription_package', 'page', 'main_menu', 'sub_menu'));
    }


    public function toggleStatus(SubscriptionPackage $subscription_package)
    {
        $status = $subscription_package->is_active == 1 ? false : true;
        $subscription_package->update(['is_active' => $status]);

        return redirect('subscription-package')->with('success',  'subscription package status changed succesfully');
    }

    public function delete(SubscriptionPackage $subscription_package)
    {
        try {
            $stripe_enabled = get_settings(Settings::ENABLE_STRIPE);
            $stripe_environment = get_settings(Settings::STRIPE_ENVIRONMENT);
            if ($stripe_enabled == 1 && $stripe_environment == 'live') {
                Stripe::setApiKey(get_settings(Settings::STRIPE_LIVE_SECRET_KEY));
            } elseif ($stripe_enabled == 1 && $stripe_environment == 'test') {
                Stripe::setApiKey(get_settings(Settings::STRIPE_TEST_SECRET_KEY));
            } else {
                return $this->respondFailed('Stripe is not enabled');
            }

            if ($subscription_package->stripe_package_id) {
                Product::update($subscription_package->stripe_package_id, ['active' => false]);
            }
        } catch (\Exception $e) {
            Log::error('Stripe delete failed: ' . $e->getMessage());
        }
        $subscription_package->delete();

        $message = 'subscription package deleted successfully';
        return redirect('subscription-package')->with('success', $message);
    }
}
