<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\DS_Page;
use App\Models\DS_AIKey;
use App\Models\DS_BalanceTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class StatisticService
{
    /**
     * Get primary dashboard metrics (Cached).
     *
     * @return array
     */
    public function getDashboardMetrics(): array
    {
        // Try to get from cache
        $stats = \Illuminate\Support\Facades\Cache::get('dashboard_stats');

        if ($stats) {
            return $stats;
        }

        // Fallback: Calculate and cache if not present (e.g. first run)
        $stats = $this->calculateDashboardMetrics();
        \Illuminate\Support\Facades\Cache::put('dashboard_stats', $stats, now()->addHours(1));

        return $stats;
    }

    /**
     * Calculate dashboard metrics (Heavy operations).
     *
     * @return array
     */
    public function calculateDashboardMetrics(): array
    {
        $currentUserCount = User::where('is_active', true)->count();
        $prevUserCount = User::where('is_active', true)
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        $usersTrend = $this->calculateTrend($currentUserCount, $prevUserCount);

        // Calculate Revenue (cents to dollars conversion)
        $currentCost = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price') / 100;
        
        $prevCost = Subscription::whereIn('status', ['active', 'expired'])
            ->where('subscriptions.created_at', '<', now()->subDays(30))
            ->where('subscriptions.created_at', '>=', now()->subDays(60))
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price') / 100;
        $costTrend = $this->calculateTrend($currentCost, $prevCost);

        $currentPageCount = DS_Page::count();
        $prevPageCount = DS_Page::where('created_at', '<', now()->subDays(30))->count();
        $pagesTrend = $this->calculateTrend($currentPageCount, $prevPageCount);

        // Tokens to requests approximation
        $todayRequestsCount = (int) (DS_AIKey::sum('tokens_used') / 250);
        $requestsTrend = ['value' => '+12%', 'type' => 'success']; // Placeholder for now

        return [
            'requests_today' => [
                'value' => number_format($todayRequestsCount),
                'trend' => $requestsTrend['value'],
                'trend_type' => $requestsTrend['type']
            ],
            'pages_total' => [
                'value' => number_format($currentPageCount),
                'trend' => $pagesTrend['value'],
                'trend_type' => $pagesTrend['type']
            ],
            'monthly_cost' => [
                'value' => '$' . number_format($currentCost, 2),
                'trend' => $costTrend['value'],
                'trend_type' => $costTrend['type']
            ],
            'active_users' => [
                'value' => number_format($currentUserCount),
                'trend' => $usersTrend['value'],
                'trend_type' => $usersTrend['type']
            ],
            'counts' => [
                'users' => User::count(),
                'subscriptions' => Subscription::count(),
                'plans' => Plan::count(),
                'pages' => $currentPageCount,
            ],
            'chart' => $this->getChartData($currentPageCount),
            'wave_data' => $this->getHistoricalWaveData(),
            'provider_status' => $this->getProviderStatus()
        ];
    }

    /**
     * Get status and relative availability per provider.
     *
     * @return array
     */
    private function getProviderStatus(): array
    {
        $definitions = [
            'openai'     => ['icon' => 'fa-robot', 'color' => 'green', 'name' => 'OpenAI'],
            'chatgpt'    => ['icon' => 'fa-robot', 'color' => 'green', 'name' => 'OpenAI'],
            'google'     => ['icon' => 'fa-brain', 'color' => 'purple', 'name' => 'Google Gemini'],
            'gemini'     => ['icon' => 'fa-brain', 'color' => 'purple', 'name' => 'Google Gemini'],
            'groq'       => ['icon' => 'fa-bolt', 'color' => 'cyan', 'name' => 'Groq'],
            'claude'     => ['icon' => 'fa-feather', 'color' => 'orange', 'name' => 'Anthropic'],
            'anthropic'  => ['icon' => 'fa-feather', 'color' => 'orange', 'name' => 'Anthropic'],
            'perplexity' => ['icon' => 'fa-search', 'color' => 'blue', 'name' => 'Perplexity'],
        ];

        $counts = DS_AIKey::select('provider')
            ->selectRaw('count(*) as count')
            ->groupBy('provider')
            ->get()
            ->pluck('count', 'provider')
            ->toArray();

        if (empty($counts)) {
            return [];
        }

        $maxCount = max($counts) ?: 1;

        $results = [];
        foreach ($counts as $slug => $count) {
            $lowerSlug = strtolower($slug);
            $config = $definitions[$lowerSlug] ?? ['icon' => 'fa-microchip', 'color' => 'gray', 'name' => ucfirst($slug)];
            
            $percentage = ($count / $maxCount) * 100;
            
            $results[] = [
                'name' => $config['name'],
                'icon' => $config['icon'],
                'color' => $config['color'],
                'count' => $count,
                'percentage' => round($percentage, 0)
            ];
        }

        usort($results, fn($a, $b) => $b['count'] <=> $a['count']);

        return $results;
    }

    /**
     * Get historical data for the wave chart (trailing 7 days).
     *
     * @return array
     */
    private function getHistoricalWaveData(): array
    {
        $days = [];
        $usersData = [];
        $freePlansData = [];
        $paidPlansData = [];
        $pagesData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->translatedFormat('D');

            $usersData[] = User::where('created_at', '<=', $date->endOfDay())->count();
            
            $freePlansData[] = Subscription::where('created_at', '<=', $date->endOfDay())
                ->whereHas('plan', fn($q) => $q->where('price', 0))
                ->count();
                
            $paidPlansData[] = Subscription::where('created_at', '<=', $date->endOfDay())
                ->whereHas('plan', fn($q) => $q->where('price', '>', 0))
                ->count();
                
            $pagesData[] = DS_Page::where('created_at', '<=', $date->endOfDay())->count();
        }

        return [
            'labels' => $days,
            'datasets' => [
                [
                    'label' => __('admin.users'),
                    'data' => $usersData,
                    'color' => '#6366f1',
                    'fill_color' => 'rgba(99, 102, 241, 0.1)'
                ],
                [
                    'label' => __('admin.total_pages'),
                    'data' => $pagesData,
                    'color' => '#06b6d4',
                    'fill_color' => 'rgba(6, 182, 212, 0.1)'
                ],
                [
                    'label' => __('admin.free_plans'),
                    'data' => $freePlansData,
                    'color' => '#10b981',
                    'fill_color' => 'rgba(16, 185, 129, 0.1)'
                ],
                [
                    'label' => __('admin.paid_plans'),
                    'data' => $paidPlansData,
                    'color' => '#8b5cf6',
                    'fill_color' => 'rgba(139, 92, 246, 0.1)'
                ],
            ]
        ];
    }

    /**
     * Get data for the usage chart.
     *
     * @param int $pagesCount
     * @return array
     */
    private function getChartData(int $pagesCount): array
    {
        $users = User::count();
        $freeSubscriptions = Subscription::whereHas('plan', fn($q) => $q->where('price', 0))->count();
        $paidSubscriptions = Subscription::whereHas('plan', fn($q) => $q->where('price', '>', 0))->count();
        
        $data = [
            ['label' => __('admin.users'), 'value' => $users, 'color' => 'primary'],
            ['label' => __('admin.total_pages'), 'value' => $pagesCount, 'color' => 'secondary'],
            ['label' => __('admin.free_plans'), 'value' => $freeSubscriptions, 'color' => 'success'],
            ['label' => __('admin.paid_plans'), 'value' => $paidSubscriptions, 'color' => 'accent'],
        ];

        $max = collect($data)->max('value') ?: 1;
        
        return array_map(function($item) use ($max) {
            $item['height'] = ($item['value'] / $max) * 100;
            if ($item['height'] < 5 && $item['value'] > 0) $item['height'] = 5;
            return $item;
        }, $data);
    }

    /**
     * Get recent transactions with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getRecentTransactions(int $perPage = 5): LengthAwarePaginator
    {
        return DS_BalanceTransaction::with('user')
            ->latest()
            ->paginate($perPage, ['*'], 'transactions_page');
    }

    /**
     * Get latest subscribers with pagination.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getLatestSubscribers(int $perPage = 5): LengthAwarePaginator
    {
        return Subscription::with(['user', 'plan'])
            ->latest()
            ->paginate($perPage, ['*'], 'subscribers_page');
    }

    /**
     * Calculate percentage change between current and previous values.
     *
     * @param float $current
     * @param float $prev
     * @return array
     */
    private function calculateTrend(float $current, float $prev): array
    {
        if ($prev <= 0) {
            return [
                'value' => $current > 0 ? '+100%' : '0%',
                'type' => 'success'
            ];
        }

        $diff = (($current - $prev) / $prev) * 100;
        $formatted = ($diff >= 0 ? '+' : '') . round($diff, 1) . '%';
        
        return [
            'value' => $formatted,
            'type' => $diff >= 0 ? 'success' : 'danger'
        ];
    }

    /**
     * Get statistics for Plans page (Cached).
     *
     * @return array
     */
    public function getPlansStats(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('admin_plans_stats', 3600, function () {
            $plans = Plan::all();
            $totalUsers = User::count();
            $activeSubscribers = User::whereHas('subscription')->count();
            
            $monthlyRevenueCents = DS_BalanceTransaction::where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(30))
                ->sum('amount');

            return [
                'total_plans'      => $plans->count(),
                'active_users'     => $activeSubscribers,
                'monthly_revenue'  => $monthlyRevenueCents / 100, // Centralized conversion
                'conversion_rate'  => $totalUsers > 0 ? round(($activeSubscribers / $totalUsers) * 100, 1) : 0,
            ];
        });
    }
}
