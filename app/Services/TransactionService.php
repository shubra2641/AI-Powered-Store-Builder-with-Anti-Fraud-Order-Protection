<?php

namespace App\Services;

use App\Models\DS_BalanceTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionService
{
    /**
     * Get paginated transactions.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedTransactions(int $perPage = 15): LengthAwarePaginator
    {
        return DS_BalanceTransaction::with('user')
            ->latest()
            ->paginate($perPage);
    }
}
