<?php

namespace App\Services;

use App\Models\User;
use App\Models\DS_BalanceTransaction;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\DB;

/**
 * Class DS_BalanceService
 * Handles user balance operations and transaction logging.
 */
class DS_BalanceService
{
    /**
     * Add credit to user balance.
     *
     * @param User $user
     * @param int $amount
     * @param string|null $description
     * @return void
     */
    public function addCredit(User $user, int $amount, ?string $description = null): void
    {
        DB::transaction(function () use ($user, $amount, $description) {
            $user->increment('balance', $amount);

            DS_BalanceTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'credit',
                'description' => $description,
            ]);
        });

        $user->notify(new SystemNotification(
            __('admin.credit_added_title'),
            __('admin.credit_added_message', ['amount' => number_format($amount)]),
            route('admin.users.index'),
            'success',
            'credit_added_notification',
            ['amount' => number_format($amount), 'balance' => number_format($user->balance)]
        ));
    }
}
