@extends($activeTemplate.'layouts.frontend')

@section('content')
    <section class="pt-80 pb-80">
        <div class="container">
            <div class="row gy-4">
                @foreach ($tvs as $tv)
                <div class="col-lg-2 col-sm-3 col-6">
                    <div class="tv-card">
                        <div class="tv-card__thumb">
                            <a href="{{ route('watch.tv', $tv->id) }}"><img src="{{ getImage(getFilePath('television').'/'.$tv->image, getFileSize('television')) }}" alt=""></a>
                        </div>
                    </div>
                </div>
                @endforeach 
            </div>
        </div>
    </section>
@endsection