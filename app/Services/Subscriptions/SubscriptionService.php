<?php

namespace App\Services\Subscriptions;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\DS_BalanceTransaction;
use App\Traits\DS_UploadHelper;
use App\Notifications\SystemNotification;
use App\Services\SettingsService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Events\PaymentRejected;
use App\Events\SubscriptionActivated;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Exception;

/**
 * Class SubscriptionService
 * 
 * Manages the full lifecycle of user subscriptions, including 
 * initialization, completion, renewals, and grace period handling.
 */
class SubscriptionService
{
    use DS_UploadHelper;

    public function __construct(
        protected SettingsService $settingsService
    ) {}

    /**
     * Initiate a plan purchase.
     * 
     * @param User $user
     * @param Plan $plan
     * @param string $gateway
     * @return Subscription
     */
    public function initiatePurchase(User $user, Plan $plan, string $gateway): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $gateway) {
            $planName = $plan->name['en'] ?? 'Default';
            $isFree = $plan->price === 0;
            
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status'  => $isFree ? 'active' : 'pending',
                'starts_at' => now(),
                'ends_at'   => now()->addDays($plan->duration_days),
                'transaction_id' => 'SUB-' . strtoupper(Str::orderedUuid()),
            ]);

            DS_BalanceTransaction::create([
                'user_id'      => $user->id,
                'amount'       => $plan->price,
                'type'         => 'debit',
                'description'  => "Subscription to Plan: {$planName}",
                'gateway_slug' => $gateway,
                'status'       => $isFree ? 'completed' : 'pending',
                'payment_id'   => $subscription->transaction_id,
            ]);

            if ($isFree) {
                $this->completePurchase($subscription->transaction_id);
            }

            return $subscription;
        });
    }

    /**
     * Complete an online purchase (Online Gateways).
     * 
     * @param string $transactionId Internal transaction ID or external payment ID
     * @return bool
     */
    public function completePurchase(string $transactionId): bool
    {
        return DB::transaction(function () use ($transactionId) {
            $subscription = Subscription::where('transaction_id', $transactionId)
                ->orWhere('id', $transactionId)
                ->first();

            if (!$subscription || $subscription->status === 'active') {
                return false;
            }

            $user = $subscription->user;
            $plan = $subscription->plan;

            $activeSub = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('ends_at', '>', now())
                ->where('id', '!=', $subscription->id)
                ->first();

            $startsAt = now();
            $endsAt = now()->addDays($plan->duration_days);

            if ($activeSub) {
                if ($activeSub->plan_id == $plan->id) {
                    $startsAt = $activeSub->ends_at; 
                    $endsAt = $activeSub->ends_at->addDays($plan->duration_days);
                } else {
                    $activeSub->update(['status' => 'cancelled', 'ends_at' => now()]);
                }
            }

            $hasPreviousSub = Subscription::where('user_id', $user->id)
                ->where('status', '!=', 'pending')
                ->exists();

            $trialEndsAt = null;
            if (!$hasPreviousSub && $plan->trial_days > 0) {
                $trialEndsAt = now()->addDays($plan->trial_days);
                $endsAt = $endsAt->addDays($plan->trial_days);
            }

            $subscription->update([
                'status'    => 'active',
                'starts_at' => $startsAt,
                'ends_at'   => $endsAt,
                'trial_ends_at' => $trialEndsAt,
            ]);

            DS_BalanceTransaction::where('payment_id', $subscription->transaction_id)
                ->update(['status' => 'completed']);

            SubscriptionActivated::dispatch($subscription);
            
            return true;
        });
    }

    /**
     * Handle bank transfer proof submission.
     * 
     * @param Subscription $subscription
     * @param UploadedFile $file
     * @return void
     */
    public function submitBankProof(Subscription $subscription, UploadedFile $file): void
    {
        $path = $this->uploadFile($file, 'receipts');

        DS_BalanceTransaction::where('payment_id', $subscription->transaction_id)
            ->update([
                'receipt_path' => $path,
                'status'       => 'pending',
            ]);
            
        try {
            $admins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->get();
            Notification::send($admins, new SystemNotification(
                title: __('admin.admin_pending_payment_title'),
                message: __('admin.admin_pending_payment_message', [
                    'name' => $subscription->user->name,
                    'plan' => $subscription->plan->translated_name
                ]),
                url: url('/admin/transactions'),
                type: 'warning'
            ));
        } catch (Exception $e) {
            Log::error('Admin pending payment notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Manually approve a bank transfer.
     * 
     * @param Subscription $subscription
     * @return void
     */
    public function approveBankTransfer(Subscription $subscription): void
    {
        $this->completePurchase($subscription->transaction_id);
    }

    /**
     * Decline a bank transfer.
     * 
     * @param Subscription $subscription
     * @return void
     */
    public function declineBankTransfer(Subscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            $subscription->update(['status' => 'cancelled']);
            DS_BalanceTransaction::where('payment_id', $subscription->transaction_id)
                ->update(['status' => 'rejected']);
            
            PaymentRejected::dispatch($subscription);
        });
    }

    /**
     * Send renewal reminders to users whose subscriptions are about to expire.
     * 
     * @return int Count of reminders sent
     */
    public function sendRenewalReminders(): int
    {
        $reminderDays = (int) $this->settingsService->get('renewal_reminder_days', 3);
        $count = 0;

        if ($reminderDays > 0) {
            $subscriptions = Subscription::where('status', 'active')
                ->whereDate('ends_at', now()->addDays($reminderDays)->toDateString())
                ->get();

            foreach ($subscriptions as $subscription) {
                $subscription->user->notify(new SystemNotification(
                    title: __('admin.renewal_reminder_title'),
                    message: __('admin.renewal_reminder_msg', ['days' => $reminderDays]),
                    url: url('/dashboard'),
                    type: 'warning',
                    templateSlug: 'renewal_reminder',
                    templateData: [
                        'user_name' => $subscription->user->name,
                        'plan_name' => $subscription->plan->translated_name,
                        'ends_at'   => $subscription->ends_at->format('Y-m-d'),
                    ]
                ));
                $count++;
            }
        }

        $urgentSubscriptions = Subscription::where('status', 'active')
            ->whereDate('ends_at', now()->toDateString())
            ->get();

        foreach ($urgentSubscriptions as $subscription) {
            $subscription->user->notify(new SystemNotification(
                title: __('admin.renewal_reminder_urgent_title'),
                message: __('admin.renewal_reminder_urgent_msg'),
                url: url('/dashboard'),
                type: 'error',
                templateSlug: 'renewal_reminder_urgent',
                templateData: [
                    'user_name' => $subscription->user->name,
                    'plan_name' => $subscription->plan->translated_name,
                    'ends_at'   => $subscription->ends_at->format('Y-m-d'),
                ]
            ));
            $count++;
        }

        return $count;
    }

    /**
     * Handle grace periods and final expirations.
     * 
     * @return int Count of subscriptions suspended/expired
     */
    public function handleGracePeriods(): int
    {
        $graceDays = $this->settingsService->get('grace_period_days', 1);
        
        $justExpired = Subscription::where('status', 'active')
            ->whereDate('ends_at', now()->subDay()->toDateString())
            ->get();

        foreach ($justExpired as $subscription) {
            $subscription->user->notify(new SystemNotification(
                title: __('admin.grace_period_title'),
                message: __('admin.grace_period_msg', ['days' => $graceDays]),
                url: url('/dashboard'),
                type: 'error',
                templateSlug: 'grace_period_warning',
                templateData: [
                    'user_name'  => $subscription->user->name,
                    'plan_name'  => $subscription->plan->translated_name,
                    'grace_days' => $graceDays,
                ]
            ));
        }

        $expiredLimit = now()->subDays($graceDays);
        $toSuspend = Subscription::where('status', 'active')
            ->where('ends_at', '<', $expiredLimit)
            ->get();

        foreach ($toSuspend as $subscription) {
            $subscription->update(['status' => 'expired']);

            try {
                $admins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->get();
                Notification::send($admins, new SystemNotification(
                    title: __('admin.admin_subscription_suspended_title'),
                    message: __('admin.admin_subscription_suspended_message', [
                        'name' => $subscription->user->name,
                        'plan' => $subscription->plan->translated_name
                    ]),
                    url: url('/admin/transactions'),
                    type: 'error'
                ));
            } catch (Exception $e) {
                Log::error('Admin suspension notification failed: ' . $e->getMessage());
            }
        }

        return $toSuspend->count();
    }

    /**
     * Expire old subscriptions.
     * 
     * @return int Count of expired subscriptions
     */
    public function expireOldSubscriptions(): int
    {

        return Subscription::where('status', 'active')
            ->where('ends_at', '<', now())
            ->update(['status' => 'expired']);
    }
}
