<div class="table-responsive-premium">
    <table class="table-premium">
        <thead>
            <tr>
                <th>{{ __('admin.user') }}</th>
                <th>{{ __('admin.plan') }}</th>
                <th class="text-center">{{ __('admin.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($latestSubscriptions as $sub)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-3 text-nowrap">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($sub->user->name) }}&background=06b6d4&color=fff" class="avatar-circle" width="32" alt="{{ $sub->user->name }}">
                            <div class="vstack">
                                <span class="text-white font-600 fs-sm truncate-1 mw-100">{{ $sub->user->name }}</span>
                                <span class="text-muted fs-2xs">{{ $sub->created_at->format('Y/m/d') }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-pill badge-pill-purple fs-2xs font-700 text-nowrap">
                            {{ $sub->plan->translated_name }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($sub->status === 'active')
                            <span class="status-badge status-active fs-3xs text-nowrap">
                                <i class="fas fa-check-circle me-1"></i> {{ __('admin.active') }}
                            </span>
                        @else
                            <span class="status-badge status-inactive fs-3xs text-nowrap">
                                <i class="fas fa-clock me-1"></i> {{ __('admin.' . $sub->status) }}
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-5">
                        <div class="vstack align-center gap-2">
                            <i class="fas fa-user-clock fs-2xl text-muted opacity-20"></i>
                            <span class="text-muted">{{ __('admin.no_data_found') }}</span>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($latestSubscriptions->hasPages())
    <div class="pagination-premium mt-4 d-flex justify-center" id="subscribers-pagination">
        {{ $latestSubscriptions->appends(['transactions_page' => request('transactions_page')])->links('vendor.pagination.ds-premium') }}
    </div>
@endif
