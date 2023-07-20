<!-- header-section start  -->

<header class="header">
    <div class="header__bottom">
        <div class="container-fluid p-0">
            <nav class="navbar navbar-expand-xl align-items-center p-0">
                <a class="site-logo site-title" href="{{ route('home') }}"><img src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="site-logo"><span class="logo-icon"><i class="flaticon-fire"></i></span></a>
                <button class="navbar-toggler ml-auto" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" type="button" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="menu-toggle"></span>
                </button>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav main-menu ms-xl-5 mx-auto">
                        <li><a href="{{ route('home') }}">@lang('Home')</a></li>
                        @foreach ($categories as $category)
                            @if ($category->subcategories->where('status', 1)->count() > 0)
                                <li class="menu_has_children">
                                    <a href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a>
                                    <ul class="sub-menu">
                                        @foreach ($category->subcategories as $subcategory)
                                            <li><a href="{{ route('subCategory', $subcategory->id) }}">{{ __($subcategory->name) }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @else
                                <li><a href="{{ route('category', $category->id) }}">{{ __($category->name) }}</a></li>
                            @endif
                        @endforeach
                        <li><a href="{{ route('live.tv') }}">@lang('Live TV')</a></li>
                        @guest
                            <li><a href="{{ route('contact') }}">@lang('Contact')</a></li>
                        @else
                            <li><a href="{{ route('user.deposit.history') }}">@lang('Payments')</a></li>
                            <li><a href="{{ route('user.wishlist.index') }}">@lang('My Wishlist')</a></li>
                            <li><a href="{{ route('user.watch.history') }}">@lang('Watch History')</a></li>
                            <li class="menu_has_children">
                                <a href="javascript:void(0)">@lang('Support Ticket')</a>
                                <ul class="sub-menu">
                                    <li><a href="{{ route('ticket.open') }}">@lang('Create New')</a></li>
                                    <li><a href="{{ route('ticket.index') }}">@lang('My Ticket')</a></li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                    <div class="nav-right d-flex ml-auto flex-wrap gap-4">
                        <button class="nav-right__search-btn"><i class="fas fa-search"></i></button>
                        @guest
                            <a href="{{ route('user.login') }}"><i class="las la-sign-in-alt"></i> @lang('Login')</a>
                            <a href="{{ route('user.register') }}"><i class="las la-user-plus"></i> @lang('Registration')</a>
                        @else
                            <div class="dropdown">
                                <button class="" data-bs-toggle="dropdown" data-display="static" type="button" aria-haspopup="true" aria-expanded="false"><i class="las la-user-plus"></i>{{ __(auth()->user()->username) }}</button>
                                <div class="dropdown-menu dropdown-menu--sm box--shadow1 dropdown-menu-right border-0 p-0">
                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.home') }}">
                                        <i class="dropdown-menu__icon las la-home"></i>
                                        <span class="dropdown-menu__caption">@lang('Dashboard')</span>
                                    </a>
                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.profile.setting') }}">
                                        <i class="dropdown-menu__icon las la-user-circle"></i>
                                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                                    </a>

                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.change.password') }}">
                                        <i class="dropdown-menu__icon las la-key"></i>
                                        <span class="dropdown-menu__caption">@lang('Password')</span>
                                    </a>

                                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.logout') }}">
                                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                                    </a>
                                </div>
                            </div>
                            @endif
                            @if ($general->multi_language)
                                @php
                                    $language = App\Models\Language::all();
                                @endphp
                                <select class="language-select langSel" id="langSel">
                                    @foreach ($language as $lang)
                                        <option value="{{ $lang->code }}" @if (Session::get('lang') === $lang->code) selected @endif>{{ __($lang->name) }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <!-- header-section end  -->
    <!-- header-search-area start -->
    <div class="header-search-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <form class="header-search-form" action="{{ route('search') }}">
                        <input name="search" type="text" placeholder="@lang('Search here')....">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- header-search-area end -->
    <!-- header-section end  -->
