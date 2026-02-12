@extends('layouts.admin')

@section('content')
<!-- Header & Actions -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.user_management') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.manage_users_roles') }}</p>
    </div>
    <button data-ds-modal-open="addUserModal" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700">
        <i class="fas fa-plus fs-xs"></i>
        <span>{{ __('admin.add_user') }}</span>
    </button>
</div>

<!-- Statistics Grid (Elite Style) -->
<div class="grid cols-4 gap-6 mb-8">
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ number_format($stats['total']) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.total_users') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-purple">
                <i class="fas fa-users fs-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ number_format($stats['active']) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.active') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-cyan">
                <i class="fas fa-user-check fs-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ number_format($stats['pending']) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.pending') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-orange">
                <i class="fas fa-user-clock fs-lg"></i>
            </div>
        </div>
    </div>
    
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-white">{{ number_format($stats['suspended']) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.suspended') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-pink">
                <i class="fas fa-user-slash fs-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Search -->
<div class="glass-card mb-6 py-4">
    <div class="d-flex flex-wrap items-center gap-4">
        <div class="relative flex-1 min-w-[300px] glass-card py-2 px-3 d-flex align-center gap-2">
            <i class="fas fa-search text-muted"></i>
            <input type="text" 
                   placeholder="{{ __('admin.search_users_placeholder') }}" 
                   class="bg-none border-none text-white outline-none w-full"
                   data-ds-search="usersTable">
        </div>
        
        <!-- Role Filter (Standardized) -->
        <div class="ds-filter-dropdown" data-ds-filter="usersTable" data-filter-col="role">
            <button type="button" class="filter-item">
                <i class="fas fa-user-shield fs-xs"></i>
                <span class="ds-filter-label">{{ __('admin.all_roles') }}</span>
                <i class="fas fa-chevron-down fs-2xs"></i>
            </button>
            <div class="ds-filter-menu">
                <button type="button" class="ds-filter-option active" data-value="all">{{ __('admin.all_roles') }}</button>
                @foreach($roles as $role)
                    <button type="button" class="ds-filter-option" data-value="{{ $role->name }}">
                        {{ $role->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Status Filter -->
        <div class="ds-filter-dropdown" data-ds-filter="usersTable" data-filter-col="status">
            <button type="button" class="filter-item">
                <i class="fas fa-circle-dot fs-xs"></i>
                <span class="ds-filter-label">{{ __('admin.all_statuses') }}</span>
                <i class="fas fa-chevron-down fs-2xs"></i>
            </button>
            <div class="ds-filter-menu">
                <button type="button" class="ds-filter-option active" data-value="all">{{ __('admin.all_statuses') }}</button>
                <button type="button" class="ds-filter-option" data-value="{{ __('admin.active') }}">{{ __('admin.active') }}</button>
                <button type="button" class="ds-filter-option" data-value="{{ __('admin.inactive') }}">{{ __('admin.inactive') }}</button>
            </div>
        </div>
        
        <button class="btn-gradient py-2 px-4 border-none font-700">
            <i class="fas fa-filter fs-xs"></i>
            <span>{{ __('admin.filter') }}</span>
        </button>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div class="ds-bulk-bar hidden" id="bulkBar-usersTable">
    <div class="d-flex align-center gap-3">
        <span class="ds-bulk-count">
            <strong id="bulkCount-usersTable">0</strong> {{ __('admin.selected') }}
        </span>
        <button type="button" class="btn-action btn-action-delete ds-bulk-delete"
                data-ds-bulk-action="delete"
                data-table="usersTable"
                data-bulk-url="{{ route('admin.users.bulk-delete') }}">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <button type="button" class="ds-bulk-clear" data-table="usersTable">
        <i class="fas fa-times me-1"></i> {{ __('admin.clear_selection') }}
    </button>
</div>

<!-- Table Container -->
<div class="table-container table-responsive-premium" id="usersTable">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="w-50">
                    <input type="checkbox" class="ds-checkbox" data-ds-select-all="usersTable">
                </th>
                <th>{{ __('admin.user') }}</th>
                <th class="d-none-mobile text-center">{{ __('admin.email_status') }}</th>
                <th class="d-none-mobile text-center">{{ __('admin.role') }}</th>
                <th class="d-none-mobile text-center">{{ __('admin.plan') }}</th>
                <th class="text-center">{{ __('admin.status') }}</th>
                <th class="d-none-mobile text-center">{{ __('admin.balance') }}</th>
                <th class="d-none-mobile text-center">{{ __('admin.joined_at') }}</th>
                <th class="text-end">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="user-row" 
                    data-searchable="{{ strtolower($user->name . ' ' . $user->email) }}"
                    data-filter-role="{{ $user->role->name ?? 'User' }}"
                    data-filter-status="{{ $user->status_label }}"
                    data-id="{{ $user->id }}">
                    <td>
                        @if(auth()->id() !== $user->id)
                            <input type="checkbox" class="ds-checkbox" data-ds-row-check="usersTable" value="{{ $user->id }}">
                        @else
                            <i class="fas fa-lock text-muted fs-xs opacity-50"></i>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-center gap-3">
                            <div class="avatar-circle avatar-primary">
                                {{ $user->avatar_initials }}
                            </div>
                            <div class="vstack">
                                <span class="font-700 text-white">{{ $user->name }}</span>
                                <span class="text-muted fs-xs">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="d-none-mobile text-center">
                        <span class="{{ $user->email_verification_badge }}">
                            @if($user->is_email_verified)
                                <i class="fas fa-envelope-circle-check"></i>
                                {{ __('admin.verified') }}
                            @else
                                <i class="fas fa-envelope-open"></i>
                                {{ __('admin.unverified') }}
                            @endif
                        </span>
                    </td>
                    <td class="d-none-mobile text-center">
                        <span class="{{ $user->role_badge_class }}">
                            {{ $user->role->name ?? 'User' }}
                        </span>
                    </td>
                    <td class="d-none-mobile text-center">
                        <span class="{{ $user->plan_badge_class }}">
                            {{ $user->plan_name }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="{{ $user->status_badge_class }}">
                            @if($user->is_active)
                                <i class="fas fa-check-circle"></i>
                            @else
                                <i class="fas fa-times-circle"></i>
                            @endif
                            {{ $user->status_label }}
                        </span>
                    </td>
                    <td class="text-white font-700 d-none-mobile text-center">
                        <span class="ds-badge ds-badge-cyan">
                            {{ ds_currency($user->balance) }}
                        </span>
                    </td>
                    <td class="text-muted fs-sm d-none-mobile text-center">
                        {{ $user->created_at->format('Y/m/d') }}
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-end gap-2">
                            <button class="btn-action btn-action-edit ds-edit-user" 
                                    data-user='@json($user)'
                                    data-url="{{ route('admin.users.update', $user->id) }}"
                                    title="{{ __('admin.edit_user') }}">
                                <i class="fas fa-user-pen"></i>
                            </button>
                            
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.impersonate', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-action btn-action-view" title="{{ __('admin.secret_login') }}">
                                        <i class="fas fa-user-secret"></i>
                                    </button>
                                </form>
                            @endif

                            <button class="btn-action btn-action-warning ds-add-credit"
                                    data-user-id="{{ $user->id }}"
                                    data-user-name="{{ $user->name }}"
                                    title="{{ __('admin.add_credit') }}">
                                <i class="fas fa-wallet"></i>
                            </button>

                            @if(!$user->is_email_verified)
                                <button data-ds-confirm="{{ route('admin.users.verify', $user->id) }}"
                                        data-ds-message="{{ __('admin.confirm_action') }}"
                                        data-ds-method="POST"
                                        data-ds-btn-class="bg-success"
                                        class="btn-action btn-action-success"
                                        title="{{ __('admin.verify_email') }}">
                                    <i class="fas fa-envelope-circle-check"></i>
                                </button>
                            @endif

                            @if(auth()->id() !== $user->id)
                                <button data-ds-confirm="{{ route('admin.users.destroy', $user->id) }}"
                                        data-ds-message="{{ __('admin.confirm_delete') }}"
                                        data-ds-method="DELETE"
                                        data-ds-btn-class="bg-danger"
                                        class="btn-action btn-action-delete" 
                                        title="{{ __('admin.delete_user') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination if needed -->
    @if(method_exists($users, 'links'))
        {{ $users->links('vendor.pagination.ds-premium') }}
    @endif
</div>

<!-- Modals -->
@include('admin.users.modal')

@if(session('bulk_modal_message'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof DS_UI !== 'undefined' && typeof DS_UI.confirm === 'function') {
                DS_UI.confirm({
                    title: "{{ __('admin.notice') }}",
                    message: "{{ session('bulk_modal_message') }}",
                    confirmText: "{{ __('admin.ok') }}",
                    cancelText: null, // Hide cancel button
                });
            }
        });
    </script>
@endif
@endsection
