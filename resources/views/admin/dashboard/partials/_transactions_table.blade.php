<div class="table-responsive-premium">
    <table class="table-premium">
        <thead>
            <tr>
                <th>{{ __('admin.user') }}</th>
                <th>{{ __('admin.amount') }}</th>
                <th>{{ __('admin.type') }}</th>
                <th class="d-none-mobile">{{ __('admin.description') }}</th>
                <th class="d-none-mobile">{{ __('admin.date') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-3 text-nowrap">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($transaction->user->name) }}&background=8b5cf6&color=fff" class="avatar-circle" width="32" alt="{{ $transaction->user->name }}">
                            <div class="vstack">
                                <span class="text-white font-600 fs-sm">{{ $transaction->user->name }}</span>
                                <span class="text-muted fs-2xs">{{ $transaction->user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="font-700 text-nowrap {{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
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
                        <span class="text-muted fs-xs text-nowrap">
                            {{ $transaction->created_at->diffForHumans() }}
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
</div>

<!-- Pagination -->
@if($transactions->hasPages())
    <div class="pagination-premium mt-4 d-flex justify-center" id="transactions-pagination">
        {{ $transactions->links('vendor.pagination.ds-premium') }}
    </div>
@endif
