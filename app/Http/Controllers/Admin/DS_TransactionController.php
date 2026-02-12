<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DS_BalanceTransaction;
use App\Models\User;
use Illuminate\View\View;
use App\Services\TransactionService;

class DS_TransactionController extends Controller
{
    /**
     * Display a listing of balance transactions.
     */
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Display a listing ofbalance transactions.
     */
    public function index(): View
    {
        $transactions = $this->transactionService->getPaginatedTransactions(15);

        return view('admin.transactions.index', compact('transactions'));
    }
}
