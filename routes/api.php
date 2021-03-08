<?php

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

Route::post('register-user', 'API\User\UsersAPIController@register');
Route::post('login-user', 'API\User\LoginController@login');

Route::group(['middleware' => 'auth:api'], function() {

    Route::apiResource('plans', 'API\User\PlanController');
    Route::post('payment-methods', 'API\User\PlanController@stripePaymentMethodsCreate');
    Route::post('tokens', 'API\User\PlanController@stripeTokenCreate');

    Route::post('subscription','API\User\SubscriptionController@createSubscription');
    Route::get('list_payment_methods', 'API\User\SubscriptionController@listPaymentMethods');
    Route::get('subscriber_invoice_list', 'API\User\SubscriptionController@getSubscriberInvoiceList');
    Route::post('download_invoice', 'API\User\SubscriptionController@invoiceDownload');
    Route::get('user_plans_listing', 'API\User\SubscriptionController@plansListing');

});

