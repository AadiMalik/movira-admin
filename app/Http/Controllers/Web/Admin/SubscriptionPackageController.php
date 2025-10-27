<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use App\Base\Filters\Master\CommonMasterFilter;
use App\Base\Libraries\QueryFilter\QueryFilterContract;
use App\Http\Requests\Admin\CreateSubscriptionPackageRequest;
use Braintree\Subscription;

class SubscriptionPackageController extends Controller
{
    protected $subscription_package;
    public function __construct(SubscriptionPackage $subscription_package)
    {
        $this->subscription_package = $subscription_package;
    }

    public function index()
    {
        return view('admin.subscription_package.index');
    }

    public function fetch(QueryFilterContract $queryFilter)
    {
        $query = $this->subscription_package->companyKey()->active();
        $results = $queryFilter->builder($query)->customFilter(new CommonMasterFilter)->paginate();
        return view('admin.subscription_package._subscription_package', compact('results'));
    }

    public function create()
    {
        $page = trans('pages_names.add_subscription_package');
        // $cities = ServiceLocation::companyKey()->whereActive(true)->get();
        $main_menu = 'subscription_package';
        $sub_menu = '';

        return view('admin.subscription_package.create', compact('page', 'main_menu', 'sub_menu'));
    }

    public function store(CreateSubscriptionPackageRequest $request)
    {
        // dd($request->all());
        $created_params = $request->only(['question', 'answer', 'user_type']);
        $created_params['active'] = 1;

        $this->subscription_package->create($created_params);

        return redirect('subscription-package')->with('success', 'subscription package added succesfully');
    }

    public function getById(SubscriptionPackage $subscription_package)
    {
        $subscription_package = $subscription_package;

        return view('admin.subscription_package.create', compact('subscription_package'));
    }


    public function toggleStatus(SubscriptionPackage $subscription_package)
    {
        $status = $subscription_package->isActive() ? false : true;
        $subscription_package->update(['is_active' => $status]);

        $message = trans('succes_messages.subscription_package_status_changed_succesfully');
        return redirect('subscription_package')->with('success', $message);
    }

    public function delete(SubscriptionPackage $subscription_package)
    {
        $subscription_package->delete();

        $message = trans('succes_messages.subscription_package_deleted_succesfully');
        return redirect('subscription_package')->with('success', $message);
    }
}
