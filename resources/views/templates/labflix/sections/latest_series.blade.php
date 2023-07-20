<section class="section pb-80" data-section="single2">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-header">
                    <h2 class="section-title">@lang('Latest Series')</h2>
                </div>
            </div>
        </div><!-- row end -->
        <div class="row mb-none-30">
            @foreach ($latestSerieses as $latestSeries)
                <div class="col-xxl-2 col-md-3 col-4 col-xs-6 mb-30">
                    <div class="movie-card @if (($latestSeries->item_type == 1 && $latestSeries->version == 1) || $latestSeries->item_type == 2) paid @endif" @if ($latestSeries->item_type == 1 && $latestSeries->version == 0) data-text="@lang('Free')" @elseif($latestSeries->item_type == 3) data-text="@lang('Trailer')" @endif>
                        <div class="movie-card__thumb thumb__2">
                            <img class="lazy-loading-img" data-src="{{ getImage(getFilePath('item_portrait') . '/' . $latestSeries->image->portrait) }}" src="{{ asset('assets/global/images/lazy.png') }}" alt="image">
                            <a class="icon" href="{{ route('watch', $latestSeries->id) }}"><i class="fas fa-play"></i></a>
                        </div>
                    </div><!-- movie-card end -->
                </div>
            @endforeach
        </div>
    </div>
</section>
<div class="ad-section pb-80">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @php echo showAd(); @endphp
            </div>
        </div>
    </div>
</div>
