<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StatisticService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateDashboardStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ds:update-stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and cache dashboard statistics to improve performance.';

    /**
     * Execute the console command.
     */
    public function handle(StatisticService $statisticService)
    {
        $this->info('Starting dashboard stats calculation...');

        try {
            // Force calculation (bypass cache check in service if needed, 
            // but simpler to implemented a calculate method in service).
            // Let's modify service to have a public calculate method.
            
            // For now, let's assume we call a method on service that returns the data, 
            // and we cache it here or the service caches it.
            // Better pattern: Service has getDashboardMetrics which checks cache.
            // This command forces a refresh.
            
            $stats = $statisticService->calculateDashboardMetrics();
            
            Cache::forever('dashboard_stats', $stats);
            Cache::put('dashboard_stats_last_updated', now(), 60 * 60 * 24); // 24 hours just in case

            $this->info('Dashboard stats updated successfully.');
            Log::info('Dashboard stats updated via cron.');

        } catch (\Exception $e) {
            $this->error('Failed to update stats: ' . $e->getMessage());
            Log::error('Failed to update dashboard stats: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
