@extends('admin.layouts.app')
@section('title', 'Main page')

@section('content')
{{-- {{session()->get('errors')}} --}}

<!-- Start Page content -->
<div class="content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">
                <div class="box">

                    <div class="box-header with-border">
                        <a href="{{ url('subscription-package-feature') }}">
                            <button class="btn btn-danger btn-sm pull-right" type="submit">
                                <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                @lang('view_pages.back')
                            </button>
                        </a>
                    </div>

                    <div class="col-sm-12">

                        <form method="post" class="form-horizontal" action="{{ url('subscription-package-feature/store') }}">
                            @csrf

                            <div class="row">
                                <input type="hidden" name="id" value="{{ isset($subscription_package_feature)?$subscription_package_feature->id:'' }}">
                                <input type="hidden" name="subscription_package_id" value="{{ isset($subscription_package)?$subscription_package->id:'' }}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="title" name="title"
                                            value="{{ isset($subscription_package_feature)?$subscription_package_feature->title:old('title') }}" required
                                            placeholder="@lang('view_pages.enter') title">
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="value">Value <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="value" name="value"
                                            value="{{ isset($subscription_package_feature)?$subscription_package_feature->value:old('value') }}" required
                                            placeholder="@lang('view_pages.enter') value">
                                        <span class="text-danger">{{ $errors->first('value') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="sorting">Sorting <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="sorting" name="sorting"
                                            value="{{ isset($subscription_package_feature)?$subscription_package_feature->sorting:old('sorting') }}" required
                                            placeholder="@lang('view_pages.enter') sorting">
                                        <span class="text-danger">{{ $errors->first('sorting') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-12">
                                    <button class="btn btn-primary btn-sm pull-right m-5" type="submit">
                                        @lang('view_pages.save')
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- container -->
</div>
<!-- content -->
@endsection