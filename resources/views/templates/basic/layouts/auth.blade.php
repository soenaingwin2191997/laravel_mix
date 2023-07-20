<!doctype html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ $general->sitename(__($pageTitle)) }}</title>
    @include('partials.seo')

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/fontawesome-all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/swiper.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/line-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/style.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/bootstrap-fileinput.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . "/css/color.php?color=$general->base_color") }}" rel="stylesheet">

    @stack('style-lib')

    @stack('style')
</head>

<body @stack('context')>

    <div class="preloader">
        <div class="loader">
            <div class="camera__wrap">
                <div class="camera__body">
                    <div class="camera__body-k7">
                        <div class="tape">
                            <div class="roll"></div>
                            <div class="roll"></div>
                            <div class="roll"></div>
                            <div class="roll"></div>
                            <div class="center"></div>
                        </div>
                        <div class="tape">
                            <div class="roll"></div>
                            <div class="roll"></div>
                            <div class="roll"></div>
                            <div class="roll"></div>
                            <div class="center"></div>
                        </div>
                    </div>
                    <div class="camera__body__stuff">
                        <div class="camera__body__stuff-bat"></div>
                        <div class="camera__body__stuff-pointer first"></div>
                        <div class="camera__body__stuff-pointer"></div>
                    </div>
                </div>
                <div class="camera__body-optic"></div>
                <div class="camera__body-light"></div>
            </div>
        </div>
    </div>

    @yield('content')

    <script src="{{ asset($activeTemplateTrue . 'js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/bootstrap.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/swiper.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/wow.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/main.js') }}"></script>

    @stack('script-lib')

    @stack('script')

    @include('partials.plugins')

    @include('partials.notify')

    <script>
        (function($) {
            "use strict";
            //Cookie
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
        })(jQuery);
    </script>

</body>

</html>
