<?php

namespace App\Http\Controllers\API\User;

use App\Events\MastersBroadcasting;
use App\Http\Requests\PaymentMethodRequest;
use App\Http\Requests\PlanRequest;
use App\Http\Requests\TokenRequest;
use App\Http\Resources\DataTrueResource;
use App\Http\Resources\PlanCollection;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    /**
     * list Plans
     * @param Request $request
     * @return PlanCollection
     */
    public function index()
    {
        $query = Plan::all();
        return response()->json($query);
    }

    /**
     * Plans detail by id
     * @param Plan $plan
     * @return PlanResource
     */
    public function show(Plan $plan)
    {
        return new PlanResource($plan->load([]));
    }

    /**
     * add Plans
     * @param PlanRequest $request
     * @return PlanResource
     */
    public function store(PlanRequest $request)
    {
        $plan = Plan::createPlan($request);
        return new PlanResource($plan);
    }

    /**
     * update Plans
     * @param Request $request
     * @param Plan $plan
     * @return PlanResource
     */
    public function update(Request $request, Plan $plan)
    {
        $plan = Plan::updatePlan($request, $plan);
        return new PlanResource($plan);
    }

    /**
     * delete Plans
     *
     * @param Plan $plan
     * @return DataTrueResource|\Illuminate\Http\JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function destroy( Plan $plan)
    {
        $stripe = Plan::setStripeSecretViaClient();
        //check active subscriptions
        $totalSubscription = $stripe->subscriptions->all(['plan' => $plan->stripe_plan_id])->data;
        if (empty($totalSubscription)) {
            // delete stripe plan(price)
            $stripe->plans->delete(
                $plan->stripe_plan_id,
                []
            );

            // delete plan parent stripe product
            $stripe->products->delete(
                $plan->stripe_product_id,
                []
            );

            // delete plan from database.
            $delete= $plan->delete();
            event(new MastersBroadcasting($plan, config('constants.broadcasting.operation_code.delete')));

            return new DataTrueResource($plan);
        } else {
            return User::getError(config('constants.subscription_message.plan_delete'));
        }

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

}
