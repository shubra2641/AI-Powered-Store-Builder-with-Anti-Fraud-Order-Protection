<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\Admin\PlanService;
use App\Http\Requests\Admin\PlanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\StatisticService;
use App\Models\DS_BalanceTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Exception;

class PlanController extends Controller
{
    public function __construct(
        protected PlanService $planService,
        protected StatisticService $statisticService
    ) {}

    /**
     * Display a listing of the plans.
     */
    public function index(): View
    {
        $plans = $this->planService->getAllPlans();
        $latestSubscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->take(10)
            ->get();
            
        $stats = $this->statisticService->getPlansStats();

        return view('admin.plans.index', compact('plans', 'latestSubscriptions', 'stats'));
    }

    /**
     * Store a newly created plan in storage.
     */
    public function store(PlanRequest $request): RedirectResponse
    {
        $this->planService->createPlan($request->validated());
        return redirect()->route('admin.plans.index')->with('success', __('admin.plan_created_success'));
    }

    /**
     * Update the specified plan in storage.
     */
    public function update(PlanRequest $request, Plan $plan): RedirectResponse
    {
        $this->planService->updatePlan($plan, $request->validated());
        return redirect()->route('admin.plans.index')->with('success', __('admin.plan_updated_success'));
    }

    /**
     * Remove the specified plan from storage.
     */
    public function destroy(Plan $plan): RedirectResponse
    {
        try {
            $this->planService->deletePlan($plan);
            return redirect()->route('admin.plans.index')->with('success', __('admin.plan_deleted_success'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle plan status via AJAX.
     */
    public function toggleStatus(Plan $plan): JsonResponse
    {
        $this->planService->toggleStatus($plan);
        return response()->json(['success' => true, 'message' => __('admin.status_updated_success')]);
    }

    /**
     * Set plan as default for new users via AJAX.
     */
    public function setDefault(Plan $plan): JsonResponse
    {
        $this->planService->setDefault($plan);
        return response()->json(['success' => true, 'message' => __('admin.default_plan_updated_success')]);
    }
}
