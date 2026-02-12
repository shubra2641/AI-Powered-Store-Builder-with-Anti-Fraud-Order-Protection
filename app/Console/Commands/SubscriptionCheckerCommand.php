<?php

namespace App\Console\Commands;

use App\Services\Subscriptions\SubscriptionService;
use Illuminate\Console\Command;

class SubscriptionCheckerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:check-expirations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for expired subscriptions and updates statuses.';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $this->info('Starting subscription expiration check...');
        
        $remindersCount = $subscriptionService->sendRenewalReminders();
        $this->info("Reminders sent: {$remindersCount}");

        $graceCount = $subscriptionService->handleGracePeriods();
        $this->info("Check complete. {$graceCount} subscriptions suspended/expired.");
    }
}
