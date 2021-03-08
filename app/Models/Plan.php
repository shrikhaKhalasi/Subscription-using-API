<?php

namespace App\Models;

use App\Events\MastersBroadcasting;
use App\Http\Resources\PlanResource;
use App\Traits\StripeTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stripe\Stripe;


class Plan extends Model
{
    use StripeTrait,SoftDeletes;

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

    }
    /**
     * common function for set the stripe secret key via client function.
     *
     * @param bool $stripeSecretKey - pass you custom key
     */
    public static function setStripeSecretViaClient($stripeSecretKey = false)
    {
        if (!$stripeSecretKey)
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

    /**
     * get the plan name by the stripe plan id.
     *
     * @param $stripePlanId - pass stripe plan id (ex. plan_Id6DLD0XsJ9Nzb)
     * @return string
     */
    public static function getPlanNameByStripeId($stripePlanId)
    {
        $planName = '';
        $plan = Plan::select('name')->where('stripe_plan_id', $stripePlanId)->first();
        if ($plan)
            $planName = $plan->name;

        return $planName;
    }

    /**
     * user plan subscription common response.
     *
     * @param $subscription - single subscription object.
     * @return string[]
     */
    public static function subscriptionResponseCommon($subscription)
    {
        $planName = self::getPlanNameByStripeId($subscription->stripe_plan);

        return [
            "id" => (string)$subscription->id,
            "user_id" => (string)$subscription->user_id,
            "name" => (string)$subscription->name,
            "stripe_id" => (string)$subscription->stripe_id,
            "stripe_status" => (string)$subscription->stripe_status,
            "stripe_plan" => (string)$subscription->stripe_plan,
            "plan_name" => (string)$planName,
            "quantity" => (string)$subscription->quantity,
            "trial_ends_at" => (string)$subscription->trial_ends_at,
            "ends_at" => (string)$subscription->ends_at,
            "created_at" => (string)$subscription->created_at,
            "updated_at" => (string)$subscription->updated_at,
            // "items" => $subscription->items,
        ];
    }

    /**
     * update plan.
     *
     * @param $query
     * @param $request
     * @param $plan - plan object
     * @return mixed
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function scopeUpdatePlan($query, $request, $plan)
    {
        $this->setStripeSecret();
        $filter = $this->getTrialAndActiveFilterValueForCreate($request);

        \Stripe\Plan::update($plan->stripe_plan_id, [
            'nickname' => $request->get('name'),
            'active' => $filter['active'],
            'trial_period_days' => $filter['trial'],
        ]);

        \stripe\Product::update($plan->stripe_product_id, [
            'name' => $request->get('name'),
        ]);

        $data = [
            'name' => $request->get('name'),
            'trial_period_days' => $request->get('trial_period_days'),
            'is_active' => $request->get('is_active'),
            'is_trial' => $request->get('is_trial'),
        ];

        $plan->update($data);
        event(new MastersBroadcasting($plan, config('constants.broadcasting.operation_code.edit')));

        return $plan;
    }
}
