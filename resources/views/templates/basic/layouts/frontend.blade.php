<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ $general->siteName(__($pageTitle)) }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.seo')
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/fontawesome-all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/global.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/line-awesome.min.css') }}" rel="stylesheet" />

    <link href="{{ asset($activeTemplateTrue . 'css/swiper.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/videojs/video.min.css') }}" rel="stylesheet">

    <link href="{{ asset($activeTemplateTrue . 'css/style.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/bootstrap-fileinput.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/color.php?color=' . $general->base_color) }}" rel="stylesheet">
    @stack('style-lib')
    @stack('style')

    <link href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ $general->base_color }}&secondColor={{ $general->secondary_color }}" rel="stylesheet">
</head>

<body @stack('context')>

    @stack('fbComment')
    @php
        $categories = App\Models\Category::where('status', 1)
            ->with([
                'subcategories' => function ($subcategory) {
                    $subcategory->where('status', 1);
                },
            ])
            ->get(['name', 'id']);
    @endphp
    @include($activeTemplate . 'partials.preloader')
    @include($activeTemplate . 'partials.frontend_header')

    @if (!request()->routeIs('home'))
        @include($activeTemplate . 'partials.breadcrumb')
    @endif

    <a class="scrollToTop" href="#"><i class="las la-angle-double-up"></i></a>

    @yield('content')

    @include($activeTemplate . 'partials.footer')

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp
    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <div class="cookies-card hide text-center">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="cookies-card__content mt-4">{{ $cookie->data_values->short_desc }} <a class="text--base" href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a class="btn btn--base w-100 policy" href="javascript:void(0)">@lang('Allow')</a>
            </div>
        </div>
    @endif

    <script src="{{ asset('assets/global/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/global.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/swiper.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>
    <script src="{{ asset('assets/global/js/videojs/video.min.js') }}"></script>

    @stack('script-lib')
    @stack('script')
    @include('partials.plugins')
    @include('partials.notify')

    <script>
        (function($) {
            "use strict";
            $(".langSel").on("change", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).val();
            });

            var inputElements = $('input,select');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],select,textarea');
            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input, select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });

            $(document).on('click', '.acceptPolicy', function() {
                $.ajax({
                    url: "{{ route('cookie.accept') }}",
                    method: 'GET',
                    success: function(data) {
                        if (data.success) {
                            $('.cookie__wrapper').addClass('d-none');
                            notify('success', data.success)
                        }
                    },
                });
            });

            $(document).on('click', '.subscribe-alert', function() {
                var modal = $('#alertModal');
                modal.modal('show');
            });

            $(document).on('submit', '.subscribe-form', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var email = $('input[name=email]').val();
                $.post('{{ route('subscribe') }}', {
                    email: email
                }, function(response) {
                    if (response.errors) {
                        for (var i = 0; i < response.errors.length; i++) {
                            iziToast.error({
                                message: response.errors[i],
                                position: "topRight"
                            });
                        }
                    } else {
                        $('input[name=email]').val('');
                        iziToast.success({
                            message: response.success,
                            position: "topRight"
                        });
                    }
                });
            });

            $(document).on("click", ".advertise", function() {
                var id = $(this).data('id');
                var url = "{{ route('add.click') }}";

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    method: 'POST',
                    data: {
                        'id': id
                    },
                    success: function(data) {

                    },
                });
            });

            $(document).on('click', '.addWishlist', function() {
                let id = $(this).data('id');
                let type = $(this).data('type');
                let contentType = ['item', 'episode'];
                if (!contentType.includes(type) || !Number.isInteger(id)) {
                    notify('error', 'Invalid Request');
                    return;
                }
                let data = {};
                data.id = id;
                data.type = type;

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    type: "POST",
                    url: `{{ route('wishlist.add') }}`,
                    data: data,
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            $('.addWishlist').addClass('d-none');
                            $('.removeWishlist').removeClass('d-none');
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

            $(document).on('click', '.removeWishlist', function() {
                let id = $(this).data('id');
                let type = $(this).data('type');
                let contentType = ['item', 'episode'];
                if (!contentType.includes(type) || !Number.isInteger(id)) {
                    notify('error', 'Invalid Request');
                    return;
                }
                let data = {};
                data.id = id;
                data.type = type;

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    type: "POST",
                    url: `{{ route('wishlist.remove') }}`,
                    data: data,
                    success: function(response) {
                        if (response.status == 'success') {
                            notify('success', response.message);
                            $('.addWishlist').removeClass('d-none');
                            $('.removeWishlist').addClass('d-none');
                        } else {
                            notify('error', response.message);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
    @include('partials.push_config')
</body>

</html>
