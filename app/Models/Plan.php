<?php

namespace App\Models;

use App\Events\MastersBroadcasting;
use App\Http\Resources\PlanResource;
use App\Traits\StripeTrait;
use Illuminate\Database\Eloquent\Model;
use Stripe\Stripe;


class Plan extends Model
{
    use StripeTrait;

    protected $fillable = [
        'name',
        'stripe_plan_id',
        'price',
        'currency',
        'interval',
        'interval_count',
        'total_subscriptions',
        'is_active',
        'is_trial',
        'trial_period_days'
    ];

    public $constant = 'constants.plans.';


    /**
     * common function for get the stripe secret key of project.
     */
    public static function setStripeSecretKey()
    {
        return env('STRIPE_SECRET');
    }

    /**
     * common function for get the stripe secret key of project.
     *
     * @param $stripeSecretKey - pass you custom key
     * @return \Stripe\StripeClient
     */
    public static function setStripeSecret($stripeSecretKey = null)
    {
        // set in to stripe function
        if (is_null($stripeSecretKey))
            Stripe::setApiKey(Plan::setStripeSecretKey());
        else
            Stripe::setApiKey($stripeSecretKey);

        // set via client
        if (is_null($stripeSecretKey))
            $stripe = new \Stripe\StripeClient(
                Plan::setStripeSecretKey()
            );
        else
            $stripe = new \Stripe\StripeClient(
                $stripeSecretKey
            );

        return $stripe;
    }

    /**
     * Create Payment method
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeCreateStripePaymentMethods($query, $request)
    {
        return $this->stripeCreatePaymentMethods($request);
    }

    /**
     * Create Token
     * @param $query
     * @param $request
     * @return mixed
     */
    public function scopeCreateStripeToken($query, $request)
    {
        return $this->stripeCreateToken($request);
    }

    /**
     * create plan function.
     *
     * @param $query
     * @param $request
     * @return mixed
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function scopeCreatePlan($query, $request)
    {
        $this->setStripeSecret();

        if (!$request->filled('currency')) {
            $currencyCode = env('CASHIER_CURRENCY');
        } else {
            $currencyCode = $request->get('currency');
        }
        $product = \Stripe\Product::create([
            'name' => $request->get('name'),
        ]);
        $filter = $this->getTrialAndActiveFilterValueForCreate($request);
        $stripePlan = \Stripe\Plan::create([
            'nickname' => $request->get('name'),
            'amount' => $request->get('price') * 100,   //stripe accept amount multiple by 100 so it basically $20.00
            'currency' => $currencyCode,
            'interval' => strtolower(config($this->constant . 'interval.' . $request->get('interval'))),
            'interval_count' => $request->get('interval_count'),
            'product' => $product->id,
            'active' => $filter['active'],
            'trial_period_days' => $filter['trial'],
        ]);
        $data = $request->all();
        $data['stripe_product_id'] = $product->id;
        $data['stripe_plan_id'] = $stripePlan->id;
        $data['currency'] = $currencyCode;
        $data['total_subscriptions'] = '0';

        $plan = $this->create($data);
        event(new MastersBroadcasting($plan, config('constants.broadcasting.operation_code.add')));

        return $plan;
    }

    /**
     * common function for filter value as per the create stripe plan API.
     *
     * @param $request
     * @return array
     */
    public function getTrialAndActiveFilterValueForCreate($request)
    {
        $trialDays = $request->get('trial_period_days');
        if ($request->get('is_trial') == config($this->constant . 'is_trial_code.no'))
            $trialDays = null;

        $active = false;
        if ($request->get('is_active') == config($this->constant . 'is_active_code.yes'))
            $active = true;

        return [
            'trial' => $trialDays,
            'active' => $active,
        ];
    }
}
