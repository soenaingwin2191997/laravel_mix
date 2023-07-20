@php
    $footerContent = getContent('basic_footer.content', true);
    $footerElement = getContent('basic_footer.element', false, null, true);
    $socialIcons = getContent('social_icon.element', false, null, true);
    $policyPages = getContent('policy_pages.element');
    $short_links = getContent('short_links.element');
@endphp
<footer class="footer-section footer bg-overlay-black bg_img @if (request()->routeIs('home') || request()->routeIs('category') || request()->routeIs('subCategory') || request()->routeIs('search')) d-none @endif pt-80" data-background="{{ getImage('assets/images/frontend/basic_footer/' . @$footerContent->data_values->background_image, '1920x789') }}">
    <div class="container">
        <div class="footer-top-area d-flex align-items-center justify-content-between flex-wrap">
            <div class="footer-logo">
                <a class="site-logo" href="{{ route('home') }}"><img src="{{ asset('assets/images/logoIcon/logo.png') }}" alt="logo"></a>
            </div>
            <div class="social-area">
                <ul class="footer-social">
                    @foreach ($socialIcons as $item)
                        <li><a href="{{ @$item->data_values->url }}" target="_blank">@php echo @$item->data_values->social_icon @endphp</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="footer-bottom-area">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-30">
                    <div class="footer-widget">
                        <h3 class="widget-title">@lang('About Us')</h3>
                        <p>{{ __(@$footerContent->data_values->about_us) }}</p>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 mb-30">
                    <div class="footer-widget">
                        <h3 class="widget-title">@lang('Categories')</h3>
                        <ul class="footer-links">
                            @foreach ($categories as $category)
                                <li><a href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-6 col-sm-4 mb-30">
                    <div class="footer-widget">
                        <h3 class="widget-title">@lang('Short Links')</h3>
                        <ul class="footer-links">

                            @forelse($short_links as $link)
                                <li><a href="{{ route('links', [$link->id, slug($link->data_values->title)]) }}">{{ __($link->data_values->title) }}</a></li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-8 mb-30">
                    <div class="footer-widget">
                        <h3 class="widget-title">@lang('Subscribe News Letter')</h3>
                        <p>{{ __(@$footerContent->data_values->subscribe_title) }}</p>
                        <form class="subscribe-form" method="post">
                            @csrf
                            <input name="email" type="email" placeholder="@lang('Email Address')" required>
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                        <div class="download-links">
                            @foreach ($footerElement as $footer)
                                <a class="download-links__item" href="{{ @$footer->data_values->link }}" target="_blank">
                                    <img src="{{ getImage('assets/images/frontend/basic_footer/' . @$footer->data_values->store_image, '150x45') }}" alt="@lang('image')">
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-12 text-center">
                    <div class="copyright-wrapper d-flex align-items-center justify-content-between flex-wrap">
                        <div class="copyright">
                            <p>@lang('Copyright') &copy; <a class="text--base" href="{{ route('home') }}">{{ $general->sitename }}</a> {{ date('Y') }} @lang('All Rights Reserved')
                            </p>
                        </div>
                        <div class="copyright-link-area">
                            <ul class="copyright-link">
                                @foreach ($policyPages as $item)
                                    <li><a href="{{ route('policies', [$item->id, slug($item->data_values->title)]) }}">{{ __($item->data_values->title) }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="alertModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"> @lang('Subscription Alert')!</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <strong>@lang('Please subscribe a plan to view our paid items')</strong>
            </div>
            <div class="modal-footer">
                <a class="btn btn--default btn-sm w-100" href="{{ route('user.home') }}">@lang('Subscribe Now')</a>
            </div>
        </div>
    </div>
</div>
