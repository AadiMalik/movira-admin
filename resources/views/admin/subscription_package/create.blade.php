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
                        <a href="{{ url('subscription-package') }}">
                            <button class="btn btn-danger btn-sm pull-right" type="submit">
                                <i class="mdi mdi-keyboard-backspace mr-2"></i>
                                @lang('view_pages.back')
                            </button>
                        </a>
                    </div>

                    <div class="col-sm-12">

                        <form method="post" class="form-horizontal" action="{{ url('subscription-package/store') }}">
                            @csrf

                            <div class="row">
                                <input type="hidden" name="id" value="{{ isset($subscription_package)?$subscription_package->id:'' }}">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="title">Title <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="title" name="title"
                                            value="{{ isset($subscription_package)?$subscription_package->title:old('title') }}" required
                                            placeholder="@lang('view_pages.enter') Title">
                                        <span class="text-danger">{{ $errors->first('title') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="description">@lang('view_pages.description') <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="description" name="description"
                                            value="{{ isset($subscription_package)?$subscription_package->description:old('description') }}" required
                                            placeholder="@lang('view_pages.enter') @lang('view_pages.description')">
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="price">@lang('view_pages.price') <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" id="price" name="price"
                                            value="{{ isset($subscription_package)?$subscription_package->price:old('price') }}" required
                                            placeholder="@lang('view_pages.enter') @lang('view_pages.price')">
                                        <span class="text-danger">{{ $errors->first('price') }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="duration_type">Package Type <span class="text-danger">*</span></label>
                                        <select name="duration_type" id="duration_type" class="form-control" required>
                                            <option value="week" {{ (isset($subscription_package)? ($subscription_package->duration_type == 'week' ? 'selected' : ''): '') }}>Weekly</option>
                                            <option value="month" {{ isset($subscription_package)? ($subscription_package->duration_type == 'month' ? 'selected' : ''):'' }}>Monthly</option>
                                            <option value="year" {{ isset($subscription_package)?($subscription_package->duration_type == 'year' ? 'selected' : ''):'' }}>Yearly</option>
                                        </select>
                                        <span class="text-danger">{{ $errors->first('duration_type') }}</span>
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