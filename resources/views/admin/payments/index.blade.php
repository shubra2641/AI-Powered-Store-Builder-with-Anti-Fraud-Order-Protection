@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.payment_gateways') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.manage_payment_gateways_description') }}</p>
    </div>
    <button type="button" class="btn-gradient py-2 px-4 fs-xs font-700" data-ds-modal-open="addGatewayModal">
        <i class="fas fa-plus me-1"></i>
        {{ __('admin.add_new_gateway') }}
    </button>
</div>

<!-- Gateways Table -->
<div class="table-container table-responsive-premium" id="paymentsTable">
    <table class="table-premium">
        <thead>
            <tr>
                <th>{{ __('admin.gateway_name') }}</th>
                <th class="d-none-mobile">{{ __('admin.mode') }}</th>
                <th class="text-center">{{ __('admin.status') }}</th>
                <th class="text-center d-none-mobile">{{ __('admin.test_mode') }}</th>
                <th class="text-end">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gateways as $gateway)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-3">
                            <div class="avatar-circle avatar-{{ $gateway->slug == 'stripe' ? 'primary' : ($gateway->slug == 'paypal' ? 'secondary' : ($gateway->slug == 'bank_transfer' ? 'success' : 'purple')) }}">
                                <i class="fas {{ $gateway->slug == 'stripe' ? 'fa-credit-card' : ($gateway->slug == 'paypal' ? 'fa-brands fa-paypal' : ($gateway->slug == 'bank_transfer' ? 'fa-building-columns' : 'fa-receipt')) }}"></i>
                            </div>
                            <div class="vstack">
                                <span class="fs-sm font-700">{{ $gateway->name }}</span>
                                <span class="fs-2xs text-muted uppercase">{{ $gateway->slug }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="d-none-mobile">
                        <span class="badge-tag {{ $gateway->mode == 'live' ? 'badge-tag-green' : 'badge-tag-stat-pink' }} uppercase fs-2xs">
                            {{ $gateway->mode }}
                        </span>
                    </td>
                    <td class="text-center">
                        <form action="{{ route('admin.payments.toggle', $gateway->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="border-none bg-none p-0 cursor-pointer">
                                <span class="badge-pill {{ $gateway->is_active ? 'badge-pill-success' : 'badge-pill-danger' }}">
                                    <i class="fas {{ $gateway->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $gateway->is_active ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="text-center d-none-mobile">
                        <span class="fs-xs {{ $gateway->is_test_mode ? 'text-warning' : 'text-success' }}">
                            {{ $gateway->is_test_mode ? __('admin.enabled') : __('admin.disabled') }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-end gap-2">
                            <button type="button" class="btn-action btn-action-edit edit-gateway-btn" 
                                    data-id="{{ $gateway->id }}" 
                                    title="{{ __('admin.configure') }}">
                                <i class="fas fa-cog"></i>
                            </button>
                            <button type="button" class="btn-action btn-action-delete" 
                                    data-ds-confirm="{{ route('admin.payments.destroy', $gateway->id) }}" 
                                    data-ds-method="DELETE"
                                    title="{{ __('admin.delete') }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Gateway Modal -->
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
                        <option value="stripe">Stripe</option>
                        <option value="paypal">PayPal</option>
                        <option value="razorpay">Razorpay</option>
                        <option value="bank_transfer">Bank Transfer</option>
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
            <button type="button" class="btn-dark" data-ds-modal-close="addGatewayModal">{{ __('admin.cancel') }}</button>
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
            <button type="button" class="btn-dark" data-ds-modal-close="editGatewayModal">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save_changes') }}</button>
        </div>
    </form>
</x-modal>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/admin-payments.js') }}"></script>
@endpush
