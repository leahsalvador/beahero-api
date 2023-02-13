<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    // Unsecure API
    Route::post('register', 'UsersController@register');

    // create customer (Register)
    Route::post('customer', 'CustomersController@create');
    Route::post('login', 'UsersController@authenticate');
    Route::post('loginWithFacebook', 'UsersController@loginWithFacebook');
    Route::get('open', 'DataController@open');

    //Secure API
    Route::group(['middleware' => ['jwt.verify']], function() {
        // Route::get('user', 'UsersController@getAuthenticatedUser');
        // Route::get('closed', 'DataController@closed');
        
        // Categories
        Route::get('categories', 'CategoriesController@index');
        Route::post('category', 'CategoriesController@create');
        Route::put('category/{id}', 'CategoriesController@update');
        Route::get('category/{id}', 'CategoriesController@getCategory');
        Route::delete('category/{id}', 'CategoriesController@delete');
        Route::get('categories-merchant/{id}', 'CategoriesController@getCategoryByMerchant');

        // Products
        Route::get('products', 'ProductsController@index');
        Route::post('product', 'ProductsController@create');
        Route::put('product/{id}', 'ProductsController@update');
        Route::get('product/{id}', 'ProductsController@getProduct');
        Route::get('product-category/{id}', 'ProductsController@getProductsByCategory');
        Route::get('products-merchant/{id}', 'ProductsController@getProductsByMerchant');
        Route::delete('product/{id}', 'ProductsController@delete');
        Route::get('nearest-merchant-products', 'ProductsController@getNearestMerchantProducts');

        // Riders
        Route::get('riders', 'RidersController@index');
        Route::post('rider', 'RidersController@create');
        Route::put('rider/{id}', 'RidersController@update');
        Route::get('rider/{id}', 'RidersController@getRider');
        Route::delete('rider/{id}', 'RidersController@delete');
        Route::get('get-rider-info/{id}', 'RidersController@getRiderInfo');
        Route::post('rider-confirm', 'RidersController@confirmRider');
        Route::post('rider-response-transaction/{id}', 'RidersController@responseRider');
        Route::post('rider-coordinates', 'RidersController@updateCoordinatesAndGetActiveTransaction');
        Route::get('rider-latest-transaction', 'RidersController@getLatestTransaction');
        Route::get('riders-nearby', 'RidersController@getNearbyRiders');
        Route::post('riders-notify', 'RidersController@notifyRiders');
        Route::post('riders-accept-delivery', 'RidersController@acceptDeliveryRiders');
        Route::put('rider-update-current-position', 'RidersController@updateRiderCurrentPosition');
        Route::post('rider-accepted/{id}', 'RidersController@riderAccepted');

        // Customers
        Route::get('customers', 'CustomersController@index');
        Route::put('customer/{id}', 'CustomersController@update');
        Route::get('customer/{id}', 'CustomersController@getCustomer');
        Route::delete('customer/{id}', 'CustomersController@delete');
        Route::get('customer-latest-transaction', 'CustomersController@getLatestTransaction');
        Route::post('customer-coordinates', 'CustomersController@updateCoordinatesAndGetActiveTransaction');
        Route::post('customer-notify/{id}', 'CustomersController@notifyCustomer');

        // Merchant
        Route::get('merchants', 'MerchantsController@index');
        Route::get('merchant-transactions/{id}', 'MerchantsController@getMerhantTransaction');
        Route::post('merchant', 'MerchantsController@create');
        Route::put('merchant/{id}', 'MerchantsController@update');
        Route::get('merchant/{id}', 'MerchantsController@getCustomer');
        Route::delete('merchant/{id}', 'MerchantsController@delete');
        Route::get('merchant-products/{id}', 'MerchantsController@getMerhantProducts');
        Route::get('merchants-business-category/{id}', 'MerchantsController@getMerchantsBusinessType');

        // Admin
        Route::get('corpadmins', 'CorpAdminsController@index');
        Route::post('corpadmin', 'CorpAdminsController@create');
        Route::put('corpadmin/{id}', 'CorpAdminsController@update');
        Route::get('corpadmin/{id}', 'CorpAdminsController@getCustomer');
        Route::delete('corpadmin/{id}', 'CorpAdminsController@delete');

        // Transactions
        Route::get('transactions', 'TransactionsController@index');
        Route::get('transaction/{id}', 'TransactionsController@getTransaction');
        Route::post('transaction/{id}', 'TransactionsController@update');
        Route::post('transaction', 'TransactionsController@create');
        Route::put('transaction/{id}', 'TransactionsController@update');
        Route::put('transaction-status/{id}', 'TransactionsController@updateStatus');
        Route::delete('transaction/{id}', 'TransactionsController@delete');
        Route::post('transaction-successfully-delivered/{id}', 'TransactionsController@transactionSuccessfullyDelivered');
        Route::get('rider-transactions/{id}', 'RidersController@riderTransactions');
        Route::get('customer-transactions/{id}', 'CustomersController@customerTransactions');

        // Rates
        Route::get('rates', 'RatesController@index');
        Route::post('rate', 'RatesController@create');
        Route::put('rate/{id}', 'RatesController@update');
        Route::get('rate/{id}', 'RatesController@getRate');
        Route::delete('rate/{id}', 'RatesController@delete');

        // Rider Wallets
        Route::get('wallets', 'RiderWalletsController@index');
        Route::post('wallet', 'RiderWalletsController@create');
        Route::put('wallet/{id}', 'RiderWalletsController@update');
        Route::get('wallet/{id}', 'RiderWalletsController@getWallet');
        Route::delete('wallet/{id}', 'RiderWalletsController@delete');
        Route::get('rider-wallet/{id}', 'RiderWalletsController@getRiderWallet');

    });


