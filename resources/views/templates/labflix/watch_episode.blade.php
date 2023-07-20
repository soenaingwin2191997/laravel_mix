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
    <div class="pt-80 pb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="movie-single-video video-show" id="video_container">
                        <video class="video-js vjs-tech" id="my-video" data-setup='{ "playbackRates": [0.5, 1, 1.5, 2] }' controls preload="auto" height="264" poster="{{ getImage(getFilePath('episode') . '/' . @$firstVideoImg) }}" controlsList="nodownload">
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
                        <div class="movie-single-video video-hide" id="ads_video_container">
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
                    </div>

                    <div class="movie-details-content">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="season1" role="tabpanel" aria-labelledby="season1-tab">
                                <div class="d-flex flex-wrap">
                                    <div class="card mb-sm-3 col-12 order-sm-1 order-2 mt-3 p-0">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <h4 class="mb-3">@lang('Description')</h4>
                                                    <p>{{ __($item->description) }}</p>
                                                </div>
                                                <div class="col-lg-6 mt-lg-0 mt-4">
                                                    <h4 class="mb-3">@lang('Team')</h4>
                                                    <ul class="movie-details-list">
                                                        <li>
                                                            <span class="caption">@lang('Director:')</span>
                                                            <span class="value">{{ __($item->team->director) }}</span>
                                                        </li>
                                                        <li>
                                                            <span class="caption">@lang('Producer:')</span>
                                                            <span class="value">{{ __($item->team->producer) }}</span>
                                                        </li>
                                                        <li>
                                                            <span class="caption">@lang('Cast:')</span>
                                                            <span class="value">{{ __($item->team->casts) }}</span>
                                                        </li>
                                                        <li>
                                                            <span class="caption">@lang('Genres:')</span>
                                                            <span class="value">{{ __(@$item->team->genres) }}</span>
                                                        </li>
                                                        <li>
                                                            <span class="caption">@lang('Language:')</span>
                                                            <span class="value">{{ __(@$item->team->language) }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- card end -->

                                    <div class="card col-12 order-sm-2 order-1 p-0">
                                        <div class="card-body p-0">
                                            <ul class="movie-small-list movie-list-scroll">
                                                @foreach ($episodes as $episode)
                                                    @php
                                                        $className = '';
                                                        $videoSrc = '';
                                                        if ($episode->version == 1) {
                                                            if (auth()->check() && auth()->user()->plan_id != 0 && auth()->user()->exp != null) {
                                                                $className = 'video-item';
                                                                $videoSrc = getVideoFile($episode->video);
                                                            }
                                                        } else {
                                                            $className = 'video-item';
                                                            $videoSrc = getVideoFile($episode->video);
                                                        }
                                                    @endphp

                                                    <li class="movie-small d-flex align-items-center justify-content-between movie-item__overlay {{ $className }} @if ($episode->version == 1) paid @endif flex-wrap" data-src="{{ $videoSrc }}" data-img="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" @if ($episode->version == 0) data-text="Free" @endif>
                                                        <div class="caojtyektj d-flex align-items-center flex-wrap">
                                                            <div class="movie-small__thumb">
                                                                <img src="{{ getImage(getFilePath('episode') . '/' . $episode->image) }}" alt="image">
                                                            </div>
                                                            <div class="movie-small__content">
                                                                <h5>{{ __($episode->title) }}</h5>
                                                                @if ($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                                    <a class="base--color" href="{{ route('watch', [$item->id, $episode->id]) }}">@lang('Play Now')</a>
                                                                @else
                                                                    <a class="base--color" href="{{ route('user.home') }}">@lang('Subscribe to watch')</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="movie-small__lock">
                                                            <span class="movie-small__lock-icon">
                                                                @if ($episode->version == 0 || (auth()->check() && auth()->user()->exp > now()))
                                                                    <i class="fas fa-unlock"></i>
                                                            </span>
                                                        @else
                                                            <i class="fas fa-lock"></i></span>
                                                @endif
                                        </div>
                                        </li>
                                        @endforeach
                                        </ul>
                                    </div>
                                </div><!-- card end -->
                            </div>
                        </div>
                    </div>
                </div><!-- movie-details-content end -->
            </div>
        </div>
    </div>
    </div>

    <section class="movie-section pb-80">
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
                    <div class="col-xxl-3 col-md-3 col-4 col-xs-6 mb-30">
                        <div class="movie-card @if (($related->item_type == 1 && $related->version == 1) || $related->item_type == 2) paid @endif" @if ($related->item_type == 1 && $related->version == 0) data-text="@lang('Free')" @elseif($related->item_type == 3) data-text="@lang('Trailer')" @endif>
                            <div class="movie-card__thumb thumb__2">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="image">
                                <a class="icon" href="{{ route('watch', $related->id) }}"><i class="fas fa-play"></i></a>
                            </div>
                        </div><!-- movie-card end -->
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
            });

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

                if (myVideo.isFullscreen()) {
                    console.log(200)
                    adVideo.requestFullscreen();
                } else {}
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
            adVideo.controls(false)

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
                var sec_num = parseInt(second, 10);
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
            });

        })(jQuery);
    </script>
@endpush

@push('context')
    oncontextmenu="return false"
@endpush
