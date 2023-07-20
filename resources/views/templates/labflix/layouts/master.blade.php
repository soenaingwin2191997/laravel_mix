@extends($activeTemplate.'layouts.app')
@section('app')
@php
    $categories = App\Models\Category::where('status', 1)->with(['subcategories' => function ($subcategory) {
                    $subcategory->where('status', 1);
                }])->get(['name', 'id']);
@endphp
    @include($activeTemplate.'partials.header')

    @if (!request()->routeIs('home'))
    @include($activeTemplate.'partials.breadcrumb')
    @endif

    @yield('content')

    @include($activeTemplate.'partials.footer')
@endsection
