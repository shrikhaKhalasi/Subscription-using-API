<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (string)$this->id,
            'name' => (string)$this->name,
            'price' => (string)$this->price,
            'stripe_plan_id' => (string)$this->stripe_plan_id,
            'stripe_product_id' => (string)$this->stripe_product_id,
            'currency' => (string)$this->currency,
            'interval' => (string)$this->interval,
            'interval_text' => (string)config('constants.plans.interval.' . $this->interval),
            'interval_count' => (string)$this->interval_count,
            'total_subscriptions' => (string)$this->total_subscriptions,
            'is_active' => (string)$this->is_active,
            'is_active_text' => (string)config('constants.plans.is_active.' . $this->is_active),
            'is_trial' => (string)$this->is_trial,
            'is_trial_text' => (string)config('constants.plans.is_trial.' . $this->is_trial),
            'trial_period_days' => (string)$this->trial_period_days,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
