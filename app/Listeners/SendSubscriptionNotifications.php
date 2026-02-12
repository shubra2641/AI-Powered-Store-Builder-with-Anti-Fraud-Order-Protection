<?php

namespace App\Listeners;

use App\Events\SubscriptionActivated;
use App\Events\SubscriptionCancelled;
use App\Events\PaymentRejected;
use App\Notifications\SystemNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

class SendSubscriptionNotifications
{
    /**
     * Handle SubscriptionActivated event.
     */
    public function handleSubscriptionActivated(SubscriptionActivated $event): void
    {
        $subscription = $event->subscription;
        $user = $subscription->user;
        $plan = $subscription->plan;

        // User Notification: Activation
        $user->notify(new SystemNotification(
            title: __('admin.subscription_activated'),
            message: __('admin.subscription_activated_msg', ['plan' => $plan->translated_name]),
            url: url('/dashboard'),
            type: 'success',
            templateSlug: 'subscription_confirmation',
            templateData: [
                'user_name'      => $user->name,
                'plan_name'      => $plan->translated_name,
                'transaction_id' => $subscription->transaction_id,
                'ends_at'        => $subscription->ends_at->format('Y-m-d'),
            ]
        ));

        // User Notification: Invoice
        $user->notify(new SystemNotification(
            title: __('admin.subscription_invoice_title'),
            message: __('admin.subscription_invoice_msg', ['id' => $subscription->transaction_id]),
            url: url('/dashboard'),
            type: 'info',
            templateSlug: 'subscription_invoice',
            templateData: [
                'user_name'      => $user->name,
                'plan_name'      => $plan->translated_name,
                'transaction_id' => $subscription->transaction_id,
                'amount'         => number_format($plan->price, 2) . ' ' . config('app.currency', 'USD'),
                'ends_at'        => $subscription->ends_at->format('Y-m-d'),
            ]
        ));

        // Admin Notification
        try {
            $admins = User::whereHas('role', fn($q) => $q->where('slug', 'admin'))->get();
            Notification::send($admins, new SystemNotification(
                title: __('admin.admin_new_subscription_title'),
                message: __('admin.admin_new_subscription_message', [
                    'name' => $user->name,
                    'plan' => $plan->translated_name
                ]),
                url: url('/admin/transactions'),
                type: 'success'
            ));
        } catch (Exception $e) {
            Log::error('Admin subscription notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle PaymentRejected event.
     */
    public function handlePaymentRejected(PaymentRejected $event): void
    {
        $subscription = $event->subscription;

        $subscription->user->notify(new SystemNotification(
            title: __('admin.payment_rejected_title'),
            message: __('admin.payment_rejected_msg', ['plan' => $subscription->plan->translated_name]),
            url: url('/dashboard'),
            type: 'error',
            templateSlug: 'payment_rejected',
            templateData: [
                'user_name' => $subscription->user->name,
                'plan_name' => $subscription->plan->translated_name,
            ]
        ));
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(object $event): void
    {
        if ($event instanceof SubscriptionActivated) {
            $this->handleSubscriptionActivated($event);
        } elseif ($event instanceof PaymentRejected) {
            $this->handlePaymentRejected($event);
        }
    }
}
