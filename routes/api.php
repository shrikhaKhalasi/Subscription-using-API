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

    Route::get('plans', 'API\User\PlanController@index');
    Route::post('create-plans', 'API\User\PlanController@create');
    Route::post('payment-methods', 'API\User\PlanController@stripePaymentMethodsCreate');
    Route::post('tokens', 'API\User\PlanController@stripeTokenCreate');

    Route::post('subscription','API\User\SubscriptionController@createSubscription');
});

