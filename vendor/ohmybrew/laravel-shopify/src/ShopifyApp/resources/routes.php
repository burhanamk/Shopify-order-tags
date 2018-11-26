<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All the routes for the Shopify App setup.
|
*/

Route::group(['middleware' => ['web']], function () {
    if (env('APP_ENV') === 'production') {
        URL::forceScheme('https');
    }
    if (env('APP_ENV') === 'local') {
        URL::forceScheme('https');
    }
    /*
    |--------------------------------------------------------------------------
    | Home Route
    |--------------------------------------------------------------------------
    |
    | Homepage for an authenticated store. Store is checked with the auth.shop
    | middleware and redirected to login if not.
    |
    */
    Route::get(
        '/',
        'OhMyBrew\ShopifyApp\Controllers\HomeController@index'
    )
    ->middleware(['auth.shop', 'billable'])
    ->name('home');
    Route::get(
        '/settings',
        'OhMyBrew\ShopifyApp\Controllers\HomeController@settings'
    )
    ->middleware(['auth.shop', 'billable'])
    ->name('settings');

    /*
    |--------------------------------------------------------------------------
    | Login Route
    |--------------------------------------------------------------------------
    |
    | Allows a shop to login/install.
    |
    */

    Route::get(
        '/login',
        'OhMyBrew\ShopifyApp\Controllers\AuthController@index'
    )->name('login');

    /*
    |--------------------------------------------------------------------------
    | Authenticate Method
    |--------------------------------------------------------------------------
    |
    | Authenticates a shop.
    |
    */

    Route::match(
        ['get', 'post'],
        '/authenticate',
        'OhMyBrew\ShopifyApp\Controllers\AuthController@authenticate'
    )
    ->name('authenticate');

    /*
    |--------------------------------------------------------------------------
    | Billing Handler
    |--------------------------------------------------------------------------
    |
    | Billing handler. Sends to billing screen for Shopify.
    |
    */

    Route::get(
        '/billing',
        'OhMyBrew\ShopifyApp\Controllers\BillingController@index'
    )
    ->name('billing');
    Route::get(
        '/get_allProductsInShopify',
        'OhMyBrew\ShopifyApp\Controllers\ApiController@get_allProductsInShopify'
    )
    ->name('get_allProductsInShopify');
    Route::get(
        '/CallStockey',
        'OhMyBrew\ShopifyApp\Controllers\ApiController@CallStockey'
    )
    ->name('CallStockey');

    /*
    |--------------------------------------------------------------------------
    | Billing Processor
    |--------------------------------------------------------------------------
    |
    | Processes the customer's response to the billing screen.
    |
    */

    Route::get(
    '/OrdersDetails',
    'OhMyBrew\ShopifyApp\Controllers\ApiController@OrdersDetails'
)
->name('OrdersDetails');
    Route::get(
'/inventory',
'OhMyBrew\ShopifyApp\Controllers\HomeController@inventory'
)
->name('inventory');

    /*
    |--------------------------------------------------------------------------
    | Ninja Api Processor
    |--------------------------------------------------------------------------
    |
    | Processes the Ninja Shipiing Api function.
    |
    */

    Route::get(
        '/billing/process',
        'OhMyBrew\ShopifyApp\Controllers\BillingController@process'
    )
    ->name('billing.process');
});


Route::group(['middleware' => ['api']], function () {
    /*
    |--------------------------------------------------------------------------
    | Webhook Handler
    |--------------------------------------------------------------------------
    |
    | Handles incoming webhooks.
    |
    */

    Route::post(
        '/webhook/{type}',
        'OhMyBrew\ShopifyApp\Controllers\WebhookController@handle'
    )
    ->middleware('auth.webhook')
    ->name('webhook');
});
