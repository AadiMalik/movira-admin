@extends('admin.layouts.app')

@section('title', 'Features')

@section('content')

<section class="content">
    <div class="row">
        <div class="col-12">
            <div class="box table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">Features ({{ $subscription_package->title }})</h3>
                    <div class="row text-right">
                        @if (auth()->user()->can('add-faq'))
                            <div class="col-12 text-right">
                                <a href="{{ url('subscription-package-feature/create/' . $subscription_package->id) }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus-circle mr-2"></i>Add Feature
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div id="js-subscription-package-feature-partial-target">
                    <include-fragment src="{{ url('subscription-package-feature/fetch/' . $subscription_package->id) }}">
                        <span style="text-align: center;font-weight: bold;">@lang('view_pages.loading')</span>
                    </include-fragment>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/fetchdata.min.js') }}"></script>
    <script>
        $(function() {
            // Handle pagination clicks
            $('body').on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.get(url, function(data) {
                    $('#js-subscription-package-feature-partial-target').html(data);
                });
            });

            // Search feature (if you enable it later)
            $('#search').on('click', function(e) {
                e.preventDefault();
                var search_keyword = $('#search_keyword').val();
                fetch("{{ url('subscription-package-feature/fetch/' . $subscription_package->id) }}?search=" + search_keyword)
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector('#js-subscription-package-feature-partial-target').innerHTML = html;
                    });
            });
        });
    </script>

@endsection
