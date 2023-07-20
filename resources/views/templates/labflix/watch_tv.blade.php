@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                <div class="col-lg-8 pe-lg-5">
                    <div class="tv-detials">
                        <video class="tv-section__video video-js vjs-default-skin" id="my_video_1" data-setup='{ "aspectRatio":"1366:620", "playbackRates": [1, 1.5, 2] }' controls preload="none" poster="{{ getImage(getFilePath('television') . '/' . $tv->image, getFileSize('television')) }}" autoplay>
                            <source src="{{ $tv->url }}" type='application/x-mpegURL' autoplay />
                        </video>

                        <div class="tv-details-wrapper">
                            <div class="tv-details__content">
                                <div class="tv-details-channel">
                                    <div class="tv-details-channel__thumb">
                                        <img src="{{ getImage(getFilePath('television') . '/' . $tv->image, getFileSize('television')) }}" alt="">
                                    </div>
                                    <div class="tv-details-channel__content">
                                        <h5 class="tv-details-channel__title">{{ __($tv->title) }}</h5>
                                    </div>
                                </div>
                                <div class="tv-details__social-share">
                                    <ul class="post-share d-flex align-items-center justify-content-sm-end justify-content-start flex-wrap">
                                        <li class="caption">@lang('Share') : </li>

                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook">
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="lab la-facebook-f"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="Linkedin">
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __($tv->title) }}&amp;summary={{ __($tv->description) }}"><i class="fab fa-linkedin-in"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter">
                                            <a href="https://twitter.com/intent/tweet?text={{ __(@$tv->title) }}%0A{{ url()->current() }}"><i class="lab la-twitter"></i></a>
                                        </li>
                                        <li data-bs-toggle="tooltip" data-bs-placement="top" title="Pinterest">
                                            <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __(@$tv->title) }}&media={{ getImage(getFilePath('television') . '/' . $tv->image, getFileSize('television')) }}"><i class="lab la-pinterest"></i></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p class="tv-details__desc mt-4">{{ __($tv->description) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="tv-details__sidebar">
                        <h3 class="tv-details__sidebar-title m-3">@lang('Other Tv Channels')</h3>
                        <ul class="tv-sidebar-list">
                            @foreach ($otherTv as $tv)
                                <li class="tv-sidebar-list__item">
                                    <a class="tv-sidebar-list__link" href="{{ route('watch.tv', $tv->id) }}">
                                        <div class="tv-details-channel">
                                            <div class="tv-details-channel__thumb">
                                                <img src="{{ getImage(getFilePath('television') . '/' . $tv->image, getFileSize('television')) }}" alt="">
                                            </div>
                                            <div class="tv-details-channel__content">
                                                <h5 class="tv-details-channel__title">{{ __($tv->title) }}</h5>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/video-js.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/video.js') }}"></script>
@endpush
