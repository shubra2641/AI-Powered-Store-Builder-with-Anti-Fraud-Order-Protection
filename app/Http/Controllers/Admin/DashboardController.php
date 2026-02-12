<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use Illuminate\View\View;
use App\Services\StatisticService;

class DashboardController extends Controller
{
    protected AIService $aiService;
    protected StatisticService $statService;

    /**
     * DashboardController constructor.
     *
     * @param StatisticService $statService
     * @param AIService $aiService
     */
    public function __construct(StatisticService $statService, AIService $aiService)
    {
        $this->statService = $statService;
        $this->aiService = $aiService;
    }

    /**
     * Display the admin dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $metrics = $this->statService->getDashboardMetrics();
        $transactions = $this->statService->getRecentTransactions(5);
        $latestSubscriptions = $this->statService->getLatestSubscribers(5);

        if (request()->ajax()) {
            if (request()->has('transactions_page')) {
                return view('admin.dashboard.partials._transactions_table', compact('transactions'));
            }
            if (request()->has('subscribers_page')) {
                return view('admin.dashboard.partials._subscribers_table', compact('latestSubscriptions'));
            }
            
            return response()->json(['error' => 'Invalid request parameters'], 400);
        }

        return view('admin.dashboard', compact('metrics', 'transactions', 'latestSubscriptions'));
    }
}
