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
                    <div class="movie-single-video video-show {{ !$watch ? 'subscribe-alert' : '' }}" id="video_container">
                        <video class="video-js" id="my-video" data-setup="{}" controls preload="auto" height="264" poster="{{ getImage(getFilePath('item_landscape') . '/' . $item->image->landscape) }}" controlsList="nodownload">
                            @if ($watch)
                                <source src="{{ $videoFile }}" type="video/mp4" />
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
                        <div class="video-hide movie-single-video" id="ads_video_container">
                            <video class="video-js" id="ad-video" data-setup="{}" controls preload="auto" height="264" controlsList="nodownload">
                                <source class="main-video" src="{{ $videoFile }}" type="video/mp4" />
                                <p class="vjs-no-js">
                                    @lang('To view this video please enable JavaScript, and consider upgrading to a  web browser that')
                                    <a href="https://videojs.com/html5-video-support/" target="_blank">@lang('supports HTML5 video')</a>
                                </p>
                            </video>
                            <button class="skipButton video-hide" id="skip-button" data-skip-time="0">@lang('Skip Ad')</button>
                            <span class="advertise-text">@lang('Advertisement') - <span class="remains-ads-time">00:52</span></span>
                        </div>
                    @endif
                    <div class="movie-content">
                        <div class="movie-content-inner d-sm-flex justify-content-between align-items-center flex-wrap">
                            <div class="movie-content-left">
                                <h3 class="title">{{ __($item->title) }}</h3>
                                <span class="sub-title">@lang('Category') : <span class="cat">{{ @$item->category->name }}</span>
                                    @if ($item->sub_category)
                                        @lang('Sub Category'): {{ @$item->sub_category->name }}
                                    @endif
                                </span>
                            </div>
                            <div class="movie-content-right mt-sm-0 mt-3">
                                <div class="movie-widget-area align-items-center">
                                    <span class="movie-widget">
                                        <i class="lar la-star base--color"></i>
                                        <span>{{ getAmount($item->ratings) }}</span>
                                    </span>
                                    <span class="movie-widget">
                                        <i class="lar la-eye color--danger"></i>
                                        <span>{{ getAmount($item->view) }} @lang('views')</span>
                                    </span>

                                    @php
                                        $wishlist = $item->wishlists->where('user_id', auth()->id())->count();
                                    @endphp

                                    <span class="movie-widget addWishlist {{ $wishlist ? 'd-none' : '' }}" data-id="{{ $item->id }}" data-type="item"><i class="las la-plus-circle"></i></span>
                                    <span class="movie-widget removeWishlist {{ $wishlist ? '' : 'd-none' }}" data-id="{{ $item->id }}" data-type="item"><i class="las la-minus-circle"></i></span>
                                </div>

                                <ul class="post-share d-flex align-items-center justify-content-sm-end justify-content-start flex-wrap">
                                    <li class="caption">@lang('Share') : </li>

                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Facebook')">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="lab la-facebook-f"></i></a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Linkedin')">
                                        <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$item->title) }}&amp;summary=@php echo strLimit(strip_tags($item->description), 130); @endphp"><i class="lab la-linkedin-in"></i></a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Twitter')">
                                        <a href="https://twitter.com/intent/tweet?text={{ __(@$item->title) }}%0A{{ url()->current() }}"><i class="lab la-twitter"></i></a>
                                    </li>
                                    <li data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('Pinterest')">
                                        <a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __(@$item->title) }}&media={{ getImage(getFilePath('item_landscape') . '/' . @$item->image->landscape) }}"><i class="lab la-pinterest"></i></a>
                                    </li>
                                </ul>

                            </div>
                        </div>
                        <p class="mt-3">{{ __($item->preview_text) }}</p>
                    </div>

                    <div class="movie-details-content">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <h4 class="mb-3">@lang('Details')</h4>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="movie-section pb-80">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="section-header">
                        <h2 class="section-title">@lang('Related Items')</h2>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-30-none">
                @foreach ($relatedItems as $related)
                    <div class="col-xxl-3 col-md-3 col-4 col-xs-6 mb-30">
                        <div class="movie-card @if (($related->item_type == 1 && $related->version == 1) || $related->item_type == 2) paid @endif" @if ($related->item_type == 1 && $related->version == 0) data-text="@lang('Free')" @elseif($related->item_type == 3) data-text="@lang('Trailer')" @endif>
                            <div class="movie-card__thumb thumb__2">
                                <img src="{{ getImage(getFilePath('item_portrait') . '/' . $related->image->portrait) }}" alt="@lang('image')">
                                <a class="icon" href="{{ route('watch', $related->id) }}"><i class="fas fa-play"></i></a>
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
        (function($) {
            "use strict";

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
{{-- 
@push('context')
    oncontextmenu="return false"
@endpush --}}
