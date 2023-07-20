<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{ticket}', 'replyTicket')->name('reply');
    Route::post('close/{ticket}', 'closeTicket')->name('close');
    Route::get('download/{ticket}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

//Wishlist
Route::controller('SiteController')->name('wishlist.')->prefix('wishlist')->group(function () {
    Route::post('add', 'addWishlist')->name('add');
    Route::post('remove', 'removeWishlist')->name('remove');
});

Route::controller('SiteController')->group(function () {
    Route::get('cron', 'cron')->name('cron');

    Route::get('live-tv', 'liveTelevision')->name('live.tv');
    Route::get('live-tv/{id?}', 'watchTelevision')->name('watch.tv');

    Route::get('/getSection', 'getSection')->name('getSection');
    Route::get('watch-video/{id}/{episode_id?}', 'watchVideo')->name('watch');

    Route::get('category/{id}', 'category')->name('category');
    Route::get('sub-category/{id}', 'subCategory')->name('subCategory');
    Route::get('search', 'search')->name('search');

    Route::get('load-more', 'loadMore')->name('loadmore.load_data');

    Route::get('company-policy/{id}/{slug}', 'policy')->name('policies');
    Route::get('links/{id}/{slug}', 'links')->name('links');
    Route::post('add-click', 'addClick')->name('add.click');
    Route::post('subscribe', 'subscribe')->name('subscribe');

    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');

    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');

    Route::get('blog/{slug}/{id}', 'blogDetails')->name('blog.details');

    Route::get('policy/{slug}/{id}', 'policyPages')->name('policy.pages');

    Route::get('placeholder-image/{size}', 'placeholderImage')->name('placeholder.image');

    Route::post('/device/token', 'storeDeviceToken')->name('store.device.token');

    Route::get('/', 'index')->name('home');
});
