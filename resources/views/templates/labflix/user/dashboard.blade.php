@extends($activeTemplate . 'layouts.master')
@section('content')
    @if ($user->exp > now())
        <div class="card-area pt-80 pb-80">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-6">
                        <div class="card custom--card">
                            <div class="card-header text-center">
                                <h3 class="card-title mb-0">
                                    @lang('Current subscription plan is ' . @$user->plan->name)
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="card-body-content mt-3 text-center">
                                    <h4 class="title">@lang('Subscription will be expired')</h4>
                                </div>
                                <div class="draw-countdown mt-3" data-year="{{ \Carbon\Carbon::parse($user->exp)->format('Y') }}" data-month="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('m') }}" data-day="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('d') }}" data-hour="{{ \Carbon\Carbon::parse(auth()->user()->exp)->format('H') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if ($user->deposits->where('status', 2)->count() > 0)
            <div class="card-area section--bg pt-80">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-6">
                            <div class="card custom--card">
                                <div class="card-body">
                                    <div class="card-body-content text-center">
                                        <h3 class="title">@lang('Your payment is now in pending, please wait for admin response')</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <section class="pt-80 pb-80">
            <div class="container">
                <div class="row justify-content-center mb-30-none">
                    @forelse ($plans as $plan)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-30">
                            <div class="package-card text-center">
                                <div class="package-card__icon">
                                    @php echo $plan->icon @endphp
                                </div>
                                <h6 class="package-card__name">{{ __($plan->name) }}</h6>
                                <div class="package-card__price">{{ $general->cur_sym }}{{ getAmount($plan->pricing) }}</div>
                                <p class="mb-3">@lang('Get ' . $plan->duration . ' days subscribtion')</p>
                                @if ($user->deposits->where('status', 2)->count() > 0)
                                    <button class="cmn-btn" disabled>@lang('Subscribe Now')</button>
                                @else
                                    <button class="cmn-btn confirmationBtn" data-question="@lang('Are you sure to subscribe this plan')?" data-action="{{ route('user.subscribe.plan', $plan->id) }}">@lang('Subscribe Now')</button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-lg-6 col-md-8">
                            <div class="package-card text-center">
                                <div class="package-card__icon">
                                    <i class="las la-frown"></i>
                                </div>
                                <h3 class="text--danger">@lang('Plan not found')</h3>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    @endif
    <x-confirmation-modal baseBtn="btn btn--base" />
@endsection
