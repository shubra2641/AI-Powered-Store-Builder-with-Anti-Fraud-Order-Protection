<?php

namespace App\Services\Admin;

use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class PlanService
{
    /**
     * Get all plans with subscriptions count.
     */
    public function getAllPlans()
    {
        return Plan::withCount('subscriptions')->get();
    }

    /**
     * Create a new plan.
     */
    public function createPlan(array $data): Plan
    {
        return Plan::create($data);
    }

    /**
     * Update an existing plan.
     */
    public function updatePlan(Plan $plan, array $data): Plan
    {
        $plan->update($data);
        return $plan;
    }

    /**
     * Set a plan as default for new users.
     */
    public function setDefault(Plan $plan): bool
    {
        return DB::transaction(function () use ($plan) {
            Plan::where('id', '!=', $plan->id)->update(['is_default' => false]);
            return $plan->update(['is_default' => true]);
        });
    }

    /**
     * Delete a plan.
     */
    public function deletePlan(Plan $plan): bool
    {
        if ($plan->subscriptions()->where('status', 'active')->exists()) {
            throw new \Exception(__('admin.cannot_delete_plan_with_active_subs'));
        }

        return $plan->delete();
    }

    /**
     * Toggle plan status.
     */
    public function toggleStatus(Plan $plan): bool
    {
        return $plan->update(['is_active' => !$plan->is_active]);
    }
}
