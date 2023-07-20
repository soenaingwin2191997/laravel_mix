@extends($activeTemplate . 'layouts.frontend')

@push('style')
    @if ($watch)
        <style>
            .video-show {
                display: block;
            }

            .video-hide {
                display: none;
            }
        </style>
    @endif
@endpush

@section('content')
    <section class="movie-details-section section--bg ptb-80">
        <div class="container">
            <div class="row justify-content-center mb-30-none">
                <div class="col-xl-8 col-lg-8 mb-30">
                    <div class="movie-item">
                        <div class="video-show movie-video {{ !$watch ? 'subscribe-alert' : '' }}" id="video_container">
                            <video class="video-js vjs-tech" id="my-video" data-setup="{}" controls preload="auto" height="264" poster="{{ getImage(getFilePath('episode') . '/' . @$firstVideoImg) }}" controlsList="nodownload">
                                @if ($watch)
                                    <source src="{{ $firstVideoFile }}" type="video/mp4" />
                                    <p class="vjs-no-js">
                                        @lang('To view this video please enable JavaScript, and consider upgrading to a  web browser that')
                                        <a href="https://videojs.com/html5-video-support/" target="_blank">@lang('supports HTML5 video')</a>
                                    </p>
                                @endif
                                @if (@$subtitles)
                                    @foreach ($subtitles ?? [] as $subtitle)
                                        <track srclang="{{ __($subtitle->code) }}" src="{{ getImage(getFilePath('subtitle') . '/' . $subtitle->file) }}" label="{{ __($subtitle->language) }}" kind="subtitles" />
                                    @endforeach
                                @endif
                            </video>
                        </div>

                        @if ($watch)
                            <div class="video-hide movie-video" id="ads_video_container">
                                <video class="video-js" id="ad-video" data-setup="{}" preload="auto" height="264" controlsList="nodownload">
                                    <source class="main-video" src="{{ $firstVideoFile }}" type="video/mp4" />
                                    <p class="vjs-no-js">
                                        @lang('To view this video please enable JavaScript, and consider upgrading to a  web browser that')
                                        <a href="https://videojs.com/html5-video-support/" target="_blank">@lang('supports HTML5 video')</a>
                                    </p>
                                </video>
                                <button class="skipButton video-hide" id="skip-button" data-skip-time="0">@lang('Skip Ad')</button>
                                <span class="advertise-text">@lang('Advertisement') - <span class="remains-ads-time"></span></span>
                            </div>
                        @endif

                        <div class="movie-content">
                            <div class="movie-content-inner d-flex justify-content-between align-items-center flex-wrap">
                                <div class="movie-content-left">
                                    <h3 class="title">{{ __($item->title) }}</h3>
                                </div>
                                <div class="movie-content-right">
                                    <div class="movie-widget-area">
                                        <span class="movie-widget"><i class="lar la-star text--warning"></i>
                                            {{ getAmount($item->ratings) }}</span>
                                        <span class="movie-widget"><i class="lar la-eye text--danger"></i>
                                            {{ getAmount($item->view) }} @lang('views')</span>

                                        @php
                                            $wishlist = $activeEpisode->wishlists->where('user_id', auth()->id())->count();
                                        @endphp

                                        <span class="movie-widget addWishlist {{ $wishlist ? 'd-none' : '' }}" data-id="{{ $activeEpisode->id }}" data-type="episode"><i class="las la-plus-circle"></i></span>
                                        <span class="movie-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}" data-id="{{ $activeEpisode->id }}" data-type="episode"><i class="las la-minus-circle"></i></span>

                                    </div>
                                </div>
                            </div>
                            <div class="movie-widget-area">
                            </div>
                            <p>{{ __($item->preview_text) }}</p>
                        </div>
                    </div>
                    <div class="product-tab mt-40">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="product-tab-desc" data-bs-toggle="tab" href="#product-desc-content" role="tab" aria-controls="product-desc-content" aria-selected="true">@lang('Description')</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="product-tab-team" data-bs-toggle="tab" href="#product-team-content" role="tab" aria-controls="product-team-content" aria-selected="false">@lang('Team')</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="product-desc-content" role="tabpanel" aria-labelledby="product-tab-desc">
                                <div class="product-desc-content">
                                    {{ __($item->description) }}
                                </div>
                            </div>
                            <div class="tab-pane fade fade" id="product-team-content" role="tabpanel" aria-labelledby="product-tab-team">
                                <div class="product-desc-content">
                                    <ul class="team-list">
                                        <li><span>@lang('Director'):</span> {{ __($item->team->director) }}</li>
                                        <li><span>@lang('Producer'):</span> {{ __($item->team->producer) }}</li>
                                        <li><span>@lang('Cast'):</span> {{ __($item->team->casts) }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 mb-30">
                    <div class="widget-box">
                        <div class="widget-wrapper movie-small-list pt-0">
                            @forelse($episodes as $episode)
                                @php
                                    $videoSrc = getVideoFile($episode->video);
                                @endphp
                                <div class="widget-item widget-item__overlay d-flex align-items-center justify-content-between" data-src="{{ $videoSrc }}" data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}">
                                    <div class="widget-item__content d-flex align-items-center movie-small flex-wrap">
                                        <div class="widget-thumb">

                                            <a href="{{ route('watch', [$item->id, $episode->id]) }}">
                                                <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" alt="movie">
                                                @if ($episode->version == 0)
                                                    <span class="movie-badge">@lang('Free')</span>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="widget-content">
                                            <h4 class="title">{{ __($episode->title) }}</h4>
                                            <div class="widget-btn">
                                                @if ($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                    <a class="custom-btn" href="{{ route('watch', [$item->id, $episode->id]) }}">@lang('Play Now')</a>
                                                @else
                                                    <a class="custom-btn" href="{{ route('watch', [$item->id, $episode->id]) }}">@lang('Subscribe to watch')</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="widget-item__lock">
                                        <span class="widget-item__lock-icon">
                                            @if ($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                <i class="fas fa-unlock"></i>
                                            @else
                                                <i class="fas fa-lock"></i>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @empty
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="movie-section ptb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Related Episode')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach ($relatedEpisodes ?? [] as $related)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 mb-30">
                        <div class="movie-item">
                            <div class="movie-thumb">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="movie">
                                @if ($related->item_type == 1 && $related->version == 0)
                                    <span class="movie-badge">@lang('Free')</span>
                                @elseif($related->item_type == 3)
                                    <span class="movie-badge">@lang('Trailer')</span>
                                @endif
                                <div class="movie-thumb-overlay">
                                    <a class="video-icon" href="{{ route('watch', $related->id) }}"><i class="fas fa-play"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        'use strict';
        (function($) {

            document.onkeydown = function(e) {
                if (e.keyCode == 123) {
                    return false;
                }
                if (e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)) {
                    return false;
                }
                if (e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)) {
                    return false;
                }
                if (e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) {
                    return false;
                }

                if (e.ctrlKey && e.shiftKey && e.keyCode == 'C'.charCodeAt(0)) {
                    return false;
                }
            }

            var ads = JSON.parse(`@json($adsTime)`);
            var skipTime = "{{ @$general->skip_time }}"
            var myVideo = videojs('my-video', {
                controlBar: {
                    skipButtons: {
                        forward: 5,
                        backward: 5
                    }
                },
                playbackRates: [0.5, 1, 1.5, 2],
            });

            myVideo.ready(function() {
                myVideo.play();
            });

            var currentTime = null;

            myVideo.on('firstplay', function() {
                var ad = ads[0];
                if (ad) playAds(ad);
            })
            myVideo.on('timeupdate', function(e) {
                var updatedTime = Math.floor(myVideo.currentTime());
                if (updatedTime > 0) {
                    var ad = null;
                    if ((updatedTime - currentTime) > 1) {
                        let timeIndex = currentTime;
                        currentTime = updatedTime
                        while (timeIndex <= updatedTime) {
                            ad = ads[timeIndex];
                            if (ad) break;
                            timeIndex++
                        }
                    } else if (currentTime !== updatedTime) {
                        currentTime = updatedTime
                        ad = ads[currentTime];
                    } else {
                        ad = null;
                    }
                    if (ad) playAds(ad);
                }
            });

            var updatedAdTime = 0;

            function playAds(ad) {
                myVideo.pause();
                var mainVideo = document.getElementById("video_container");
                var adsVideo = document.getElementById("ads_video_container");

                mainVideo.classList.add('video-hide');
                mainVideo.classList.remove('video-show');
                adsVideo.classList.add('video-show');
                adsVideo.classList.remove('video-hide');
                adVideo.src({
                    src: ad,
                    type: 'video/mp4'
                })
                adVideo.play();


                $('.skipButton').data('skip-time', `${skipTime}`);
                adVideo.on('ended', function() {
                    adsVideo.classList.add("video-hide");
                    adsVideo.classList.remove("video-show");
                    mainVideo.classList.remove("video-hide");
                    mainVideo.classList.add("video-show");
                    adVideo.pause();
                    myVideo.play();
                    myVideo.setAttribute('autoplay', true)
                    document.getElementById('skip-button').classList.add('video-hide')
                })
            }
            var adVideo = videojs('ad-video');

            adVideo.on('timeupdate', function(e) {
                var currentAdTime = Math.floor(adVideo.currentTime());
                updatedAdTime = currentAdTime;
                var remainAdTime = toTime(adVideo.remainingTime());
                $('.remains-ads-time').text(remainAdTime);
                var skipTime = $('.skipButton').data('skip-time');
                if (skipTime > 0 && skipTime == updatedAdTime) {
                    $('.skipButton').removeClass('video-hide');
                }
            });

            function toTime(second) {
                var sec_num = parseInt(second, 10); // don't forget the second param
                var hours = Math.floor(sec_num / 3600);
                var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
                var seconds = sec_num - (hours * 3600) - (minutes * 60);

                if (hours < 10) {
                    hours = "0" + hours;
                }
                if (minutes < 10) {
                    minutes = "0" + minutes;
                }
                if (seconds < 10) {
                    seconds = "0" + seconds;
                }
                if (hours > 0) {
                    return hours + ':' + minutes + ':' + seconds;
                }
                return minutes + ':' + seconds;
            }

            $('.skipButton').on('click', function() {
                var mainVideo = document.getElementById("video_container");
                var adsVideo = document.getElementById("ads_video_container");
                adsVideo.classList.add("video-hide");
                adsVideo.classList.remove("video-show");
                mainVideo.classList.remove("video-hide");
                mainVideo.classList.add("video-show");
                adVideo.pause();
                myVideo.play();
                myVideo.setAttribute('autoplay', true)
                document.getElementById('skip-button').classList.add('video-hide')
            })

        })(jQuery);
    </script>
@endpush

@push('context')
    oncontextmenu="return false"
@endpush
