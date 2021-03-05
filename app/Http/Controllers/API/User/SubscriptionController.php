<?php

namespace App\Http\Controllers\API\User;

use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Resources\UsersResource;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class SubscriptionController extends Controller
{
    public function createSubscription(CreateSubscriptionRequest $request)
    {
        $user = auth()->user();
        $plan = Plan::findOrFail($request->get('plan_id'));

        // set stripe secret
        $stripe = Plan::setStripeSecret();

        // Retrieve Payment Method
        $paymentMethod = Plan::retrievePaymentMethod($request->get('payment_method_id'));

        if ($plan->is_trial == config($plan->constant . 'is_trial_code.yes'))
            $subscription = $user->newSubscription('default', $plan->stripe_plan_id)->trialDays($plan->trial_days)->create($paymentMethod);
        else
            $subscription = $user->newSubscription('default', $plan->stripe_plan_id)->create($paymentMethod);

        $subscription->update();

        $user = User::find($user->id); // get the user with latest subscription.

        $totalSubscriptions = $stripe->subscriptions->all(['plan' => $plan->stripe_plan_id, 'limit' => '1', 'include[]' => 'total_count']);
        $plan->total_subscriptions = $totalSubscriptions->total_count;
        $plan->update();

        return new UsersResource($user);
    }
}
