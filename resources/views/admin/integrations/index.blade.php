@extends('layouts.admin')

@section('title', __('admin.integrations'))

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/ds-integrations.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-white">{{ __('admin.integrations') }}</h1>
    </div>


    <div class="glass-card mb-6 py-4">
        <form action="{{ route('admin.integrations.index') }}" method="GET" class="d-flex flex-wrap items-center gap-4">

            <div class="relative flex-1 min-w-[300px] glass-card py-2 px-3 d-flex align-center gap-2">
                <i class="fas fa-search text-muted"></i>
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('admin.search') }}..." 
                       class="bg-none border-none text-white outline-none w-full">
            </div>
            

            <div class="ds-filter-dropdown">
                <select name="category" class="input-premium no-appearance cursor-pointer js-auto-submit">
                    <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>{{ __('admin.all_categories') }}</option>
                    <option value="payment" {{ request('category') == 'payment' ? 'selected' : '' }}>{{ __('admin.payment_gateways') }}</option>
                    <option value="communication" {{ request('category') == 'communication' ? 'selected' : '' }}>{{ __('admin.communication') }}</option>
                    <option value="tracking" {{ request('category') == 'tracking' ? 'selected' : '' }}>{{ __('admin.tracking_pixels') }}</option>
                    <option value="shopping" {{ request('category') == 'shopping' ? 'selected' : '' }}>{{ __('admin.shopping') }}</option>
                    <option value="security" {{ request('category') == 'security' ? 'selected' : '' }}>{{ __('admin.security') }}</option>
                </select>
            </div>


            <div class="ds-filter-dropdown">
                 <select name="status" class="input-premium no-appearance cursor-pointer js-auto-submit">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('admin.all_statuses') }}</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                </select>
            </div>
            
            <button type="submit" class="btn-gradient py-2 px-4 border-none font-700">
                <i class="fas fa-filter fs-xs"></i>
                <span>{{ __('admin.filter') }}</span>
            </button>
            
            @if(request()->hasAny(['search', 'category', 'status']))
                <a href="{{ route('admin.integrations.index') }}" class="btn btn-soft-secondary">
                    <i class="fas fa-times me-1"></i> {{ __('admin.clear') }}
                </a>
            @endif
        </form>
    </div>

    <div id="integrations" class="settings-content">
        <div class="ds-integration-grid">
            @foreach($integrations as $item)
                <div class="integration-card glass-card" data-service="{{ $item->key }}" data-type="{{ $item->type }}">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-wrapper {{ $item->color }}">
                                <i class="{{ $item->icon }}"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 font-weight-bold text-white">{{ $item->name }}</h5>
                                <small class="text-muted">{{ __($item->desc_key) }}</small>
                            </div>
                        </div>
                        

                        @if($item->type === 'payment' && !$item->is_configured)
                             <div class="toggle-switch disabled opacity-50 cursor-not-allowed"></div>
                        @else
                             <div class="toggle-switch {{ $item->is_active ? 'active' : '' }} js-toggle-integration" data-service="{{ $item->key }}"></div>
                        @endif
                    </div>
                    
                    <p class="text-muted small mb-3 description">{{ __($item->sub_key) }}</p>
                    
                    @if($item->type === 'payment')
                         @if($item->is_configured)
                            <button class="btn btn-soft-primary w-100 btn-sm js-edit-payment" data-id="{{ $item->id }}">
                                <i class="fas fa-cog me-1"></i> {{ __('admin.configure') }}
                            </button>
                         @else
                            <button class="btn btn-soft-primary w-100 btn-sm js-add-payment" data-slug="{{ $item->key }}">
                                <i class="fas fa-cog me-1"></i> {{ __('admin.configure') }}
                            </button>
                         @endif
                    @else
                        <button class="btn btn-soft-primary w-100 btn-sm js-config-integration" data-service="{{ $item->key }}" data-name="{{ $item->name }}">
                            <i class="fas fa-cog me-1"></i> {{ __('admin.configure') }}
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="ds-modal-overlay" id="settingsModal">
    <div class="ds-modal-card">
        <div class="ds-modal-header border-bottom border-white-5 p-4">
            <h3 class="ds-modal-title m-0" id="modalTitle">{{ __('admin.settings') }}</h3>
            <button class="ds-modal-close js-close-modal" data-modal="settingsModal"><i class="fas fa-times"></i></button>
        </div>
        <div class="ds-modal-body p-4">
            <form id="settingsForm" action="{{ route('admin.integrations.update') }}" method="POST">
                @csrf
                <input type="hidden" name="service" id="modalServiceInput">
                <div id="modalFields">
                    <!-- Dynamic Fields -->
                </div>
                <div class="ds-modal-footer">
                    <button type="button" class="btn btn-soft-secondary js-close-modal" data-modal="settingsModal">{{ __('admin.cancel') }}</button>
                    <button type="submit" class="btn btn-gradient">{{ __('admin.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Add Gateway Modal (Adapted) -->
<x-modal id="addGatewayModal" title="{{ __('admin.add_new_gateway') }}" size="md">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-credit-card"></i>
        </div>
    </div>
    <form action="{{ route('admin.payments.store') }}" method="POST">
        @csrf
        <div class="vstack gap-4">
            <div class="d-flex gap-4">
                <div class="ds-form-group-horizontal flex-1">
                    <label class="form-label-premium">{{ __('admin.gateway_provider') }}</label>
                    <select name="slug" id="add_gateway_slug" class="input-premium no-appearance gateway-select-handler" data-container="addCredentialsContainer" required>
                        <option value="" disabled selected>{{ __('admin.select_gateway') }}</option>
                        @foreach(app(\App\Services\Payments\DS_PaymentGatewayService::class)->getAvailableGateways() as $slug => $provider)
                            <option value="{{ $slug }}">{{ $provider['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ds-form-group-horizontal flex-1">
                    <label class="form-label-premium">{{ __('admin.display_name') }}</label>
                    <input type="text" name="name" id="add_gateway_name" class="input-premium" placeholder="e.g. My Stripe" required>
                </div>
            </div>

            <div id="add_environment_section">
                <div class="d-flex gap-4 mb-4">
                    <div class="ds-form-group-horizontal flex-1">
                        <label class="form-label-premium">{{ __('admin.environment') }}</label>
                        <select name="environment" class="input-premium no-appearance">
                            <option value="sandbox_test" selected>Sandbox / Test Mode</option>
                            <option value="live_prod">Live / Production</option>
                        </select>
                    </div>
                    <div class="ds-form-group-horizontal flex-1">
                        <label class="form-label-premium">{{ __('admin.status') }}</label>
                        <select name="is_active" class="input-premium no-appearance">
                            <option value="1">{{ __('admin.active') }}</option>
                            <option value="0" selected>{{ __('admin.inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="addCredentialsSection" class="border-top border-primary-soft pt-4 hidden">
                <h4 class="fs-sm font-700 mb-3 text-white">{{ __('admin.gateway_credentials') }}</h4>
                <div id="addCredentialsContainer" class="vstack gap-3">
                    <!-- Dynamic Fields Here -->
                </div>
            </div>
        </div>

        <div class="ds-modal-footer">
            <button type="button" class="btn-dark js-close-modal" data-modal="addGatewayModal">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.confirm') }}</button>
        </div>
    </form>
</x-modal>

<!-- Edit Credentials Modal -->
<x-modal id="editGatewayModal" title="{{ __('admin.configure_gateway') }}" size="md">
    <div class="ds-modal-avatar-header">
        <div id="edit_gateway_avatar" class="ds-modal-avatar-circle">
            <i class="fas fa-cog"></i>
        </div>
    </div>
    <form id="editGatewayForm" method="POST">
        @csrf
        @method('PUT')
        <div class="vstack gap-4">
            <div class="d-flex gap-4">
                <div class="ds-form-group-horizontal flex-1">
                    <label class="form-label-premium">{{ __('admin.display_name') }}</label>
                    <input type="text" name="name" id="edit_gateway_name_field" class="input-premium" required>
                </div>
            </div>
            <div id="edit_environment_section">
                <div class="d-flex gap-4 mb-4">
                    <div class="ds-form-group-horizontal flex-1">
                        <label class="form-label-premium">{{ __('admin.environment') }}</label>
                        <select name="environment" id="edit_environment" class="input-premium no-appearance">
                            <option value="sandbox_test">Sandbox / Test Mode</option>
                            <option value="live_prod">Live / Production</option>
                        </select>
                    </div>
                    <div class="ds-form-group-horizontal flex-1">
                        <label class="form-label-premium">{{ __('admin.status') }}</label>
                        <select name="is_active" id="edit_is_active" class="input-premium no-appearance">
                            <option value="1">{{ __('admin.active') }}</option>
                            <option value="0">{{ __('admin.inactive') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="border-top border-primary-soft pt-4">
                <h4 class="fs-sm font-700 mb-3 text-white">{{ __('admin.gateway_credentials') }}</h4>
                <div id="editCredentialsContainer" class="vstack gap-3">
                    <!-- Dynamic Fields Here -->
                </div>
            </div>
        </div>
        <div class="ds-modal-footer">
            <button type="button" class="btn-dark js-close-modal" data-modal="editGatewayModal">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save_changes') }}</button>
        </div>
    </form>
</x-modal>
@endsection

@push('scripts')
    <div id="integrations-config" 
         data-save-route="{{ route('admin.integrations.update') }}"
         data-toggle-route="{{ route('admin.integrations.toggle') }}"
         data-csrf-token="{{ csrf_token() }}"
         data-status-updated-msg="{{ __('admin.status_updated_success') }}"
         data-status-failed-msg="{{ __('admin.error_occurred') }}"
         data-error-occurred-msg="{{ __('admin.generic_error') }}"
         data-settings-text="{{ __('admin.settings') }}"
         data-current-integrations='@json(collect($integrations)->keyBy('key'))'>
    </div>
<script src="{{ asset('assets/js/ds-integrations.js') }}"></script>
<script src="{{ asset('assets/js/admin-payments.js') }}"></script>
@endpush
