<?php

namespace App\Traits;

use Exception;
use Stripe;
use Illuminate\Support\Facades\Artisan;

trait StripeTrait {

    /**
     * common function for get the stripe secret key of project.
     *
     * @param bool $stripeSecretKey - pass you custom key
     */
    public static function setStripeSecret($stripeSecretKey = false)
    {
        if (!$stripeSecretKey)
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        else
            \Stripe\Stripe::setApiKey($stripeSecretKey);
    }

    /**
     * Stripe create customers
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeStripeCreateCustomer($query, $request) {
        try {
            Artisan::call('config:clear');
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            return \Stripe\Customer::create($request);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Stripe create payment methods
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeStripeCreatePaymentMethods($query, $request) {
        try {
            Artisan::call('config:clear');
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            return \Stripe\PaymentMethod::create($request);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Stripe create card token
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeStripeCreateToken($query, $request) {
        try {
            Artisan::call('config:clear');
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            return \Stripe\Token::create($request);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Stripe create subscriptions
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeStripeCreateSubscriptions($query, $request) {
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            return \Stripe\Subscription::create($request);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Retrieve stripe Ppyment method
     *
     * @param $query
     * @param $id - Payment method id
     * @return Stripe\PaymentMethod
     * @throws Stripe\Exception\ApiErrorException
     */
    public function scopeRetrievePaymentMethod($query, $id)
    {
        return \Stripe\PaymentMethod::retrieve(
            $id,
            []
        );
    }
}

