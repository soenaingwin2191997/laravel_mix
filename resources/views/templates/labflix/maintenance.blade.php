@extends($activeTemplate.'layouts.app')
@section('app')
<div class="maintanance-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="maintanance-icon mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                @php echo $maintenance->data_values->description @endphp
            </div>
        </div>
    </div>
</div>
@endsection

@push('style')
    <style>
     body{
        min-height: calc(100vh + 0px) !important;
    }
    .maintanance-page {
        display: grid;
        place-content: center;
        width: 100%;
        height: 100vh;
    }
    .maintanance-icon {
        width: 60px;
        height: 60px;
        display: grid;
        place-items: center;
        aspect-ratio: 1;
        border-radius: 50%;
        background: #fff;
        font-size: 26px;
        color: #e73d3e;
    }
    </style>
@endpush