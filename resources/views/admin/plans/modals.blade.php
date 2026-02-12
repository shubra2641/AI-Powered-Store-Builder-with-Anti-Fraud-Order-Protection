
<x-modal id="addPlanModal" title="{{ __('admin.add_plan') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-layer-group"></i>
        </div>
    </div>
    <form id="addPlanForm" action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        
        <!-- Multilingual Name -->
        <div class="glass-card mb-4 p-3 bg-primary-soft-overlay">
            <div class="d-flex justify-between align-center mb-3">
                <label class="form-label-premium m-0">{{ __('admin.plan_name') }}</label>
                <div class="ds-tabs-container">
                    @foreach($availableLanguages as $lang)
                        <button type="button" class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn" data-group="plan_name_add" data-lang="{{ $lang->code }}">
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>
            </div>
            @foreach($availableLanguages as $lang)
                <div data-lang-pane="{{ $lang->code }}" class="lang-pane-plan_name_add {{ $loop->first ? '' : 'hidden' }}">
                    <input type="text" name="translated_name[{{ $lang->code }}]" 
                           value="{{ old('translated_name.' . $lang->code) }}"
                           class="input-premium" placeholder="{{ $lang->name }}..." required>
                </div>
            @endforeach
        </div>

        <!-- Multilingual Description -->
        <div class="glass-card mb-4 p-3 bg-primary-soft-overlay">
            <div class="d-flex justify-between align-center mb-3">
                <label class="form-label-premium m-0">{{ __('admin.plan_description') }}</label>
                <div class="ds-tabs-container">
                    @foreach($availableLanguages as $lang)
                        <button type="button" class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn" data-group="plan_desc_add" data-lang="{{ $lang->code }}">
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>
            </div>
            @foreach($availableLanguages as $lang)
                <div data-lang-pane="{{ $lang->code }}" class="lang-pane-plan_desc_add {{ $loop->first ? '' : 'hidden' }}">
                    <textarea name="translated_description[{{ $lang->code }}]" 
                              class="input-premium" placeholder="{{ $lang->name }}..." rows="2">{{ old('translated_description.' . $lang->code) }}</textarea>
                </div>
            @endforeach
        </div>


        <div class="d-flex gap-4 mb-4">
            <div class="flex-2">
                <label class="form-label-premium">{{ __('admin.monthly_price') }} ({{ $ds_settings->get('site_currency', 'USD') }})</label>
                <input type="number" step="0.01" name="price" required class="input-premium" placeholder="0.00">
            </div>
            <div class="flex-1">
                <label class="form-label-premium">{{ __('admin.duration_days') }}</label>
                <input type="number" name="duration_days" value="30" required class="input-premium" placeholder="30">
            </div>
            <div class="flex-1">
                <label class="form-label-premium">{{ __('admin.trial_days') }}</label>
                <input type="number" name="trial_days" value="0" class="input-premium" placeholder="0">
            </div>
        </div>


        <div class="d-flex gap-4 mb-4 p-3 glass-card">
            <div class="d-flex align-center gap-3">
                <input type="checkbox" name="is_featured" class="ds-checkbox" id="add_is_featured">
                <label for="add_is_featured" class="fs-xs font-700 text-white">{{ __('admin.is_featured') }}</label>
            </div>
        </div>

        <!-- Quotas & Limits -->
        <div class="glass-card p-4">
            <h5 class="fs-xs font-800 text-white mb-4 border-bottom border-white-5 pb-2 uppercase letter-spacing-1">
                {{ __('admin.plans_quotas_limits') }}
            </h5>
            
            <!-- Numeric Limits -->
            <div class="grid cols-12 gap-3 mb-6">
                @foreach(['ai_pages', 'drag_drop_pages', 'custom_domains', 'storage_gb', 'orders', 'products', 'support_messages'] as $quota)
                    <div class="col-span-4">
                        <label class="form-label-premium fs-3xs">{{ __('admin.'.$quota) }}</label>
                        <input type="number" name="quotas[{{ $quota }}]" value="-1" class="input-premium py-2 fs-xs" placeholder="-1">
                    </div>
                @endforeach
            </div>

            <!-- Feature Toggles -->
            <div class="vstack gap-3 mb-6 p-3 bg-white-5 border-radius-sm">
                <label class="form-label-premium fs-xs mb-1">{{ __('admin.features') }}</label>
                <div class="grid cols-3 gap-3">
                    @foreach($availableFeatures as $feature)
                        <div class="d-flex align-center gap-3">
                            <input type="checkbox" name="quotas[{{ $feature }}]" value="1" class="ds-checkbox" id="add_feature_{{ $feature }}">
                            <label for="add_feature_{{ $feature }}" class="fs-2xs font-700 text-white">{{ __('admin.'.$feature) }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Gateways Selection -->
            <div class="p-3 bg-white-5 border-radius-sm">
                <label class="form-label-premium fs-xs mb-2">{{ __('admin.payment_gateways') }}</label>
                <div class="grid cols-3 gap-3">
                    @foreach($allGateways as $gateway)
                        <div class="d-flex align-center gap-2">
                            <input type="checkbox" name="quotas[payment_gateways][]" value="{{ $gateway['slug'] }}" class="ds-checkbox" id="add_gateway_{{ $gateway['slug'] }}">
                            <label for="add_gateway_{{ $gateway['slug'] }}" class="fs-2xs text-white">{{ $gateway['name'] }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="addPlanModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.confirm') }}</button>
        </div>
    </form>
</x-modal>


<x-modal id="editPlanModal" title="{{ __('admin.edit_plan') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle bg-purple-soft text-purple">
            <i class="fas fa-edit"></i>
        </div>
    </div>
    <form id="editPlanForm" method="POST">
        @csrf
        @method('PUT')
        
        <!-- Multilingual Name -->
        <div class="glass-card mb-4 p-3 bg-purple-soft-overlay">
            <div class="d-flex justify-between align-center mb-3">
                <label class="form-label-premium m-0">{{ __('admin.plan_name') }}</label>
                <div class="ds-tabs-container">
                    @foreach($availableLanguages as $lang)
                        <button type="button" class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn" data-group="plan_name_edit" data-lang="{{ $lang->code }}">
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>
            </div>
            @foreach($availableLanguages as $lang)
                <div data-lang-pane="{{ $lang->code }}" class="lang-pane-plan_name_edit {{ $loop->first ? '' : 'hidden' }}">
                    <input type="text" name="translated_name[{{ $lang->code }}]" 
                           id="edit_name_{{ $lang->code }}" 
                           value="{{ old('translated_name.' . $lang->code) }}"
                           class="input-premium" required>
                </div>
            @endforeach
        </div>

        <!-- Multilingual Description -->
        <div class="glass-card mb-4 p-3 bg-purple-soft-overlay">
            <div class="d-flex justify-between align-center mb-3">
                <label class="form-label-premium m-0">{{ __('admin.plan_description') }}</label>
                <div class="ds-tabs-container">
                    @foreach($availableLanguages as $lang)
                        <button type="button" class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn" data-group="plan_desc_edit" data-lang="{{ $lang->code }}">
                            {{ strtoupper($lang->code) }}
                        </button>
                    @endforeach
                </div>
            </div>
            @foreach($availableLanguages as $lang)
                <div data-lang-pane="{{ $lang->code }}" class="lang-pane-plan_desc_edit {{ $loop->first ? '' : 'hidden' }}">
                    <textarea name="translated_description[{{ $lang->code }}]" 
                              id="edit_desc_{{ $lang->code }}" 
                              class="input-premium" rows="2">{{ old('translated_description.' . $lang->code) }}</textarea>
                </div>
            @endforeach
        </div>

        <div class="d-flex gap-4 mb-4">
            <div class="flex-2">
                <label class="form-label-premium">{{ __('admin.monthly_price') }} ({{ $ds_settings->get('site_currency', 'USD') }})</label>
                <input type="number" step="0.01" name="price" id="edit_price" required class="input-premium">
            </div>
            <div class="flex-1">
                <label class="form-label-premium">{{ __('admin.duration_days') }}</label>
                <input type="number" name="duration_days" id="edit_duration_days" required class="input-premium">
            </div>
            <div class="flex-1">
                <label class="form-label-premium">{{ __('admin.trial_days') }}</label>
                <input type="number" name="trial_days" id="edit_trial_days" class="input-premium">
            </div>
        </div>

        <div class="d-flex gap-4 mb-4 p-3 glass-card">
            <div class="d-flex align-center gap-3">
                <input type="checkbox" name="is_featured" class="ds-checkbox" id="edit_is_featured">
                <label for="edit_is_featured" class="fs-xs font-700 text-white">{{ __('admin.is_featured') }}</label>
            </div>
        </div>

        <!-- Quotas & Limits -->
        <div class="glass-card p-4">
            <h5 class="fs-xs font-800 text-white mb-4 border-bottom border-white-5 pb-2 uppercase letter-spacing-1">
                {{ __('admin.plans_quotas_limits') }}
            </h5>
            
            <!-- Numeric Limits -->
            <div class="grid cols-12 gap-3 mb-6">
                @foreach(['ai_pages', 'drag_drop_pages', 'custom_domains', 'storage_gb', 'orders', 'products', 'support_messages'] as $quota)
                    <div class="col-span-4">
                        <label class="form-label-premium fs-3xs">{{ __('admin.'.$quota) }}</label>
                        <input type="number" name="quotas[{{ $quota }}]" id="edit_quota_{{ $quota }}" class="input-premium py-2 fs-xs" placeholder="-1">
                    </div>
                @endforeach
            </div>

            <!-- Feature Toggles -->
            <div class="vstack gap-3 mb-6 p-3 bg-white-5 border-radius-sm">
                <label class="form-label-premium fs-xs mb-1">{{ __('admin.features') }}</label>
                <div class="grid cols-3 gap-3">
                    @foreach($availableFeatures as $feature)
                        <div class="d-flex align-center gap-3">
                            <input type="checkbox" name="quotas[{{ $feature }}]" value="1" class="ds-checkbox edit-feature-toggle" id="edit_quota_{{ $feature }}">
                            <label for="edit_quota_{{ $feature }}" class="fs-2xs font-700 text-white">{{ __('admin.'.$feature) }}</label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Payment Gateways Selection -->
            <div class="p-3 bg-white-5 border-radius-sm">
                <label class="form-label-premium fs-xs mb-2">{{ __('admin.payment_gateways') }}</label>
                <div class="grid cols-3 gap-3">
                    @foreach($allGateways as $gateway)
                        <div class="d-flex align-center gap-2">
                            <input type="checkbox" name="quotas[payment_gateways][]" value="{{ $gateway['slug'] }}" class="ds-checkbox edit-gateway-checkbox" id="edit_gateway_{{ $gateway['slug'] }}">
                            <label for="edit_gateway_{{ $gateway['slug'] }}" class="fs-2xs text-white">{{ $gateway['name'] }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="editPlanModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save_changes') }}</button>
        </div>
    </form>
</x-modal>
