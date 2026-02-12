@extends('layouts.admin')

@section('content')
<!-- Header & Actions -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.transactions') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.view_all_transactions') }}</p>
    </div>
</div>

<!-- Filters & Search -->
<div class="filter-bar mb-4">
    <div class="glass-card py-2 px-3 d-flex align-center gap-2 w-full mw-400">
        <i class="fas fa-search text-muted"></i>
        <input type="text"
               placeholder="{{ __('admin.search_placeholder') }}"
               class="bg-none border-none text-white outline-none w-full"
               data-ds-search="transactionsTable">
    </div>
    
    <div class="ds-filter-dropdown" data-ds-filter="transactionsTable" data-filter-col="type">
        <button type="button" class="filter-item">
            <i class="fas fa-filter fs-xs"></i>
            <span class="ds-filter-label">{{ __('admin.all') }}</span>
            <i class="fas fa-chevron-down fs-2xs"></i>
        </button>
        <div class="ds-filter-menu">
            <button type="button" class="ds-filter-option active" data-value="all">{{ __('admin.all') }}</button>
            <button type="button" class="ds-filter-option" data-value="credit">{{ __('admin.credit') }}</button>
            <button type="button" class="ds-filter-option" data-value="debit">{{ __('admin.debit') }}</button>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar (hidden until rows selected) -->
<div class="ds-bulk-bar hidden" id="bulkBar-transactionsTable">
    <div class="d-flex align-center gap-3">
        <span class="ds-bulk-count">
            <strong id="bulkCount-transactionsTable">0</strong> {{ __('admin.selected') }}
        </span>
    </div>
    <button type="button" class="ds-bulk-clear" data-table="transactionsTable">
        <i class="fas fa-times me-1"></i> {{ __('admin.clear_selection') }}
    </button>
</div>

<!-- Table Container -->
<div class="table-container table-responsive-premium" id="transactionsTable">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="w-50">
                    <input type="checkbox" class="ds-checkbox" data-ds-select-all="transactionsTable">
                </th>
                <th>{{ __('admin.user') }}</th>
                <th>{{ __('admin.amount') }}</th>
                <th>{{ __('admin.type') }}</th>
                <th class="d-none-mobile">{{ __('admin.description') }}</th>
                <th class="d-none-mobile">{{ __('admin.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr data-searchable="{{ strtolower($transaction->user->name . ' ' . $transaction->user->email . ' ' . $transaction->description) }}"
                    data-filter-type="{{ $transaction->type }}"
                    data-id="{{ $transaction->id }}">
                    <td>
                        <input type="checkbox" class="ds-checkbox" data-ds-row-check="transactionsTable" value="{{ $transaction->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->user->name) }}&background=8b5cf6&color=fff" class="avatar-circle" width="32" alt="{{ $transaction->user->name }}">
                            <div class="vstack">
                                <span class="text-white font-600 fs-sm">{{ $transaction->user->name }}</span>
                                <span class="text-muted fs-2xs">{{ $transaction->user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="font-700 {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type == 'credit' ? '+' : '-' }}{{ number_format($transaction->amount) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge-pill {{ $transaction->type == 'credit' ? 'badge-pill-success' : 'badge-pill-danger' }}">
                            <i class="fas {{ $transaction->type == 'credit' ? 'fa-arrow-up' : 'fa-arrow-down' }} fs-2xs"></i>
                            {{ __('admin.' . $transaction->type) }}
                        </span>
                    </td>
                    <td class="d-none-mobile">
                        <span class="text-muted fs-sm truncate-1 mw-200" title="{{ $transaction->description }}">
                            {{ $transaction->description ?: '---' }}
                        </span>
                    </td>
                    <td class="d-none-mobile">
                        <span class="text-muted fs-xs">
                            {{ $transaction->created_at->format('Y-m-d H:i') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="vstack align-center gap-2">
                            <i class="fas fa-history fs-2xl text-muted opacity-20"></i>
                            <span class="text-muted">{{ __('admin.no_data_found') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- No Results Message -->
    <div class="ds-no-results hidden">
        <i class="fas fa-search"></i>
        <p>{{ __('admin.no_results_found') }}</p>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="pagination-premium">
            {{ $transactions->links() }}
        </div>
    @endif
</div>
</div>

@endsection
