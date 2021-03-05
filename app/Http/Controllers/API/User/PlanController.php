<?php

namespace App\Http\Controllers\API\User;

use App\Http\Requests\PaymentMethodRequest;
use App\Http\Requests\TokenRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    /**
     * list Plans
     * @return mixed
     */
    public function index()
    {
        $query = Plan::all();
        return response()->json($query);
    }

    /**
     * add Plan
     * @param Request $request
     * @return PlanResource
     */
    public function create(Request $request)
    {
        $plan = Plan::createPlan($request);
        return new PlanResource($plan);
    }

    /**
     * Create Payment method
     * @param PaymentMethodRequest $request
     * @return mixed
     */
    public function stripePaymentMethodsCreate(PaymentMethodRequest $request)
    {
        return Plan::createStripePaymentMethods($request->all());
    }

    /**
     * Create Token
     * @param TokenRequest $request
     * @return mixed
     */
    public function stripeTokenCreate(TokenRequest $request)
    {
        return Plan::createStripeToken($request->all());
    }
}
