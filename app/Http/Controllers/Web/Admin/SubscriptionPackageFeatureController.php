<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Models\SubscriptionPackageFeature;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Requests\Admin\CreateSubscriptionPackageFeatureRequest;
use Braintree\Subscription;

class SubscriptionPackageFeatureController extends Controller
{
    protected $subscription_package;
    protected $subscription_package_feature;
    public function __construct(SubscriptionPackage $subscription_package, SubscriptionPackageFeature $subscription_package_feature)
    {
        $this->subscription_package = $subscription_package;
        $this->subscription_package_feature = $subscription_package_feature;
    }

    public function index($subscription_package_id)
    {
        $page = 'Subscription Package Features';

        $main_menu = 'subscription-package-feature';
        $sub_menu = '';
        $subscription_package = $this->subscription_package->find($subscription_package_id);
        return view('admin.subscription_package_feature.index', compact('subscription_package','page', 'main_menu', 'sub_menu'));
    }

    public function fetch(QueryFilterContract $queryFilter, $subscription_package_id)
    {
        $query = SubscriptionPackageFeature::query();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();
        $subscription_package = $this->subscription_package->find($subscription_package_id);
        return view('admin.subscription_package_feature._subscription_package_feature', compact('subscription_package','results'));
    }

    public function create($subscription_package_id)
    {
        $page = trans('pages_names.add_subscription_package');
        // $cities = ServiceLocation::companyKey()->whereActive(true)->get();
        $main_menu = 'subscription-package';
        $sub_menu = '';
        
        $subscription_package = $this->subscription_package->find($subscription_package_id);

        return view('admin.subscription_package_feature.create', compact('subscription_package','page', 'main_menu', 'sub_menu'));
    }

    public function store(CreateSubscriptionPackageFeatureRequest $request)
    {
      //   dd($request->all());
        $created_params = $request->only(['title', 'value', 'sorting', 'subscription_package_id']);

        if ($request->id == '') {
            $this->subscription_package_feature->create($created_params);
        } else {
            $this->subscription_package_feature->find($request->id)->update($created_params);
        }

        return redirect('subscription-package-feature/'.$request->subscription_package_id.'')->with('success', 'subscription package Feature added succesfully');
    }

    public function getById($id)
    {
        $page = trans('pages_names.add_subscription_package_feature');
        $main_menu = 'subscription-package-feature';
        $sub_menu = '';
        $subscription_package_feature = $this->subscription_package_feature->find($id);
        $subscription_package = $this->subscription_package->find($subscription_package_feature->subscription_package_id);
        return view('admin.subscription_package_feature.create', compact('subscription_package','subscription_package_feature', 'page', 'main_menu', 'sub_menu'));
    }

    public function delete(SubscriptionPackageFeature $subscription_package_feature)
    {
        $subscription_package_feature->delete();

        $message = 'subscription package Feature deleted successfully';
        return redirect('subscription-package-feature/'.$subscription_package_feature->subscription_package_id.'')->with('success', $message);
    }
}
