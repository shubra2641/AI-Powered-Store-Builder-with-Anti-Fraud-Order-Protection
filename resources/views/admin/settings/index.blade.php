@extends('layouts.admin')

@section('content')
<div class="position-relative">

    <div class="mb-8">
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.website_settings') }}</h2>
        <p class="text-muted fs-sm m-0 mt-2">{{ __('admin.manage_site_configuration') }}</p>
    </div>


    <div class="ds-tabs-container mb-8 bg-transparent border-none overflow-x-auto pb-2">
        <div class="d-flex gap-3">
            <button class="settings-tab ds-tab-btn px-6 py-3 rounded-xl glass-card {{ request('tab', 'general') === 'general' ? 'active' : '' }} tab-switch-btn" data-tab="general">
                <i class="fas fa-cog me-2"></i> {{ __('admin.general_settings') }}
            </button>
            <button class="settings-tab ds-tab-btn px-6 py-3 rounded-xl glass-card {{ request('tab') === 'smtp' ? 'active' : '' }} tab-switch-btn" data-tab="smtp">
                <i class="fas fa-envelope me-2"></i> {{ __('admin.smtp_settings') }}
            </button>
            <button class="settings-tab ds-tab-btn px-6 py-3 rounded-xl glass-card {{ request('tab') === 'seo' ? 'active' : '' }} tab-switch-btn" data-tab="seo">
                <i class="fas fa-search me-2"></i> {{ __('admin.seo_settings') }}
            </button>
            <button class="settings-tab ds-tab-btn px-6 py-3 rounded-xl glass-card {{ request('tab') === 'contact' ? 'active' : '' }} tab-switch-btn" data-tab="contact">
                <i class="fas fa-address-book me-2"></i> {{ __('admin.contact_settings') }}
            </button>
            <button class="settings-tab ds-tab-btn px-6 py-3 rounded-xl glass-card {{ request('tab') === 'ai' ? 'active' : '' }} tab-switch-btn" data-tab="ai">
                <i class="fas fa-robot me-2"></i> {{ __('admin.ai_settings') }}
            </button>
        </div>
    </div>

    <div class="settings-container vstack gap-8">

        <div id="general-tab" class="tab-pane {{ request('tab', 'general') === 'general' ? 'active' : 'hidden' }}">
            <div class="vstack gap-8">

                <div class="glass-card rounded-2xl p-8">
                    <div class="d-flex align-center gap-3 mb-6 border-bottom border-primary-soft pb-4">
                        <div class="stat-icon-box bg-primary-soft w-40 h-40">
                            <i class="fas fa-images fs-sm"></i>
                        </div>
                        <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.global_assets') }}</h3>
                    </div>
                    
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="group" value="general">
                        
                        <div class="grid cols-1 cols-2 cols-3 gap-8 mb-8">
                            <div class="vstack gap-4">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.site_logo') }}</label>
                                <div class="border-2 border-dashed border-primary-soft rounded-2xl p-8 text-center hover-bright cursor-pointer bg-white-5 transition-all" onclick="this.querySelector('input').click()">
                                    @if($ds_settings->logoUrl())
                                        <div class="mb-4 d-inline-block p-3 glass-card rounded-xl">
                                            <img src="{{ $ds_settings->logoUrl() }}" class="mh-80">
                                        </div>
                                    @else
                                        <div class="mb-4 opacity-50"><i class="fas fa-cloud-upload-alt fs-2xl"></i></div>
                                    @endif
                                    <p class="text-muted fs-xs m-0 font-600">{{ __('admin.upload_png_jpg') }}</p>
                                    <input type="file" name="site_logo" class="hidden" accept="image/*">
                                </div>
                            </div>
                            <div class="vstack gap-4">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.site_favicon') }}</label>
                                <div class="border-2 border-dashed border-primary-soft rounded-2xl p-8 text-center hover-bright cursor-pointer bg-white-5 transition-all" onclick="this.querySelector('input').click()">
                                    @if($ds_settings->faviconUrl())
                                        <div class="mb-4 d-inline-block p-3 glass-card rounded-xl">
                                            <img src="{{ $ds_settings->faviconUrl() }}" class="img-40">
                                        </div>
                                    @else
                                        <div class="mb-4 opacity-50"><i class="fas fa-icons fs-2xl"></i></div>
                                    @endif
                                    <p class="text-muted fs-xs m-0 font-600">{{ __('admin.upload_ico_png') }}</p>
                                    <input type="file" name="site_favicon" class="hidden" accept="image/*">
                                </div>
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.site_currency') }}</label>
                                <div class="position-relative w-full">
                                    <select name="site_currency" class="input-premium no-appearance">
                                        @foreach(['USD', 'EUR', 'GBP', 'SAR', 'EGP', 'AED', 'KWD', 'QAR', 'INR'] as $curr)
                                            <option value="{{ $curr }}" {{ $ds_settings->get('site_currency', 'USD', null) === $curr ? 'selected' : '' }}>{{ $curr }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fas fa-chevron-down position-absolute right-4 top-50 translate-middle-y fs-2xs opacity-50 pointer-none"></i>
                                </div>
                            </div>
                        </div>


                        <div class="grid cols-1 cols-2 gap-8 mb-8">
                            @forelse($languages as $lang)
                                <div class="ds-form-group-vertical vstack gap-2">
                                    <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.site_name') }} ({{ strtoupper($lang->code) }})</label>
                                    <input type="text" name="site_name_{{ $lang->id }}" value="{{ $ds_settings->get('site_name', null, $lang->id) }}" required class="input-premium" placeholder="e.g. DropSaaS">
                                </div>
                                <div class="ds-form-group-vertical vstack gap-2">
                                    <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.site_description') }} ({{ strtoupper($lang->code) }})</label>
                                    <textarea name="site_description_{{ $lang->id }}" class="input-premium" rows="3" placeholder="{{ __('admin.site_description_placeholder') }}">{{ $ds_settings->get('site_description', null, $lang->id) }}</textarea>
                                </div>
                            @empty
                            @endforelse
                        </div>

                        <div class="d-flex justify-end pt-6 border-top border-primary-soft">
                            <button type="submit" class="btn-gradient px-8 py-3 fs-sm font-700 rounded-xl">
                                <i class="fas fa-save me-2"></i> {{ __('admin.save_assets') }}
                            </button>
                        </div>
                    </form>
                </div>


                <div class="glass-card rounded-2xl p-8 mt-8">
                    <div class="d-flex align-center gap-3 mb-6 border-bottom border-primary-soft pb-4">
                        <div class="stat-icon-box bg-purple-soft w-40 h-40">
                            <i class="fas fa-file-invoice-dollar fs-sm"></i>
                        </div>
                        <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.billing_renewal_settings') }}</h3>
                    </div>
                    
                    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="group" value="general">
                        
                        <div class="grid cols-1 cols-2 gap-8 mb-8">
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.renewal_reminder_days') }}</label>
                                <input type="number" name="renewal_reminder_days" value="{{ $ds_settings->get('renewal_reminder_days', 3) }}" min="0" max="30" required class="input-premium">
                                <p class="text-muted fs-2xs m-0">{{ __('admin.renewal_reminder_hint') }}</p>
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.grace_period_days') }}</label>
                                <input type="number" name="grace_period_days" value="{{ $ds_settings->get('grace_period_days', 1) }}" min="0" max="30" required class="input-premium">
                                <p class="text-muted fs-2xs m-0">{{ __('admin.grace_period_hint') }}</p>
                            </div>
                        </div>

                        <div class="d-flex justify-end pt-6 border-top border-primary-soft">
                            <button type="submit" class="btn-gradient px-8 py-3 fs-sm font-700 rounded-xl">
                                <i class="fas fa-save me-2"></i> {{ __('admin.save') }}
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div id="smtp-tab" class="tab-pane {{ request('tab') === 'smtp' ? 'active' : 'hidden' }}">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <input type="hidden" name="group" value="smtp">
                <div class="vstack gap-8">

                    <div class="glass-card rounded-2xl p-8">
                        <div class="d-flex align-center gap-3 mb-8 border-bottom border-primary-soft pb-4">
                            <div class="stat-icon-box bg-info-soft w-40 h-40">
                                <i class="fas fa-user-tag fs-sm"></i>
                            </div>
                            <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.email_sender_identity') }}</h3>
                        </div>

                        <div class="grid cols-1 cols-2 cols-3 gap-8">
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.mail_from_name') }}</label>
                                <input type="text" name="mail_from_name" value="{{ $ds_settings->get('mail_from_name') }}" required class="input-premium" placeholder="e.g. DropSaaS Admin">
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.mail_from_address') }}</label>
                                <input type="email" name="mail_from_address" value="{{ $ds_settings->get('mail_from_address') }}" required class="input-premium" placeholder="e.g. noreply@example.com">
                            </div>
                        </div>
                    </div>


                    <div class="glass-card rounded-2xl p-8">
                        <div class="d-flex align-center gap-3 mb-8 border-bottom border-primary-soft pb-4">
                            <div class="stat-icon-box bg-warning-soft w-40 h-40">
                                <i class="fas fa-server fs-sm"></i>
                            </div>
                            <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.mail_server_configuration') }}</h3>
                        </div>

                        <div class="grid cols-1 cols-2 cols-3 gap-8">
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.smtp_host') }}</label>
                                <input type="text" name="smtp_host" value="{{ $ds_settings->get('smtp_host') }}" required class="input-premium" placeholder="e.g. smtp.mailtrap.io">
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.smtp_port') }}</label>
                                <input type="number" name="smtp_port" value="{{ $ds_settings->get('smtp_port') }}" required class="input-premium" placeholder="587">
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.smtp_encryption') }}</label>
                                <select name="smtp_encryption" class="input-premium no-appearance">
                                    <option value="tls" {{ $ds_settings->get('smtp_encryption') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ $ds_settings->get('smtp_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ $ds_settings->get('smtp_encryption') === 'null' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>

                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.smtp_username') }}</label>
                                <input type="text" name="smtp_username" value="{{ $ds_settings->get('smtp_username') }}" required class="input-premium" placeholder="SMTP Username">
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.smtp_password') }}</label>
                                <div class="position-relative">
                                    <input type="password" name="smtp_password" value="{{ $ds_settings->get('smtp_password') }}" required class="input-premium" placeholder="SMTP Password">
                                    <button type="button" class="position-absolute right-4 top-50 translate-middle-y border-none bg-transparent opacity-50 hover-bright" onclick="const i = this.previousElementSibling; i.type = i.type === 'password' ? 'text' : 'password'; this.querySelector('i').classList.toggle('fa-eye'); this.querySelector('i').classList.toggle('fa-eye-slash')">
                                        <i class="fas fa-eye fs-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-end mt-10 pt-6 border-top border-primary-soft">
                            <div class="d-flex align-center gap-4">
                                <button type="button" id="test-smtp-btn" class="btn-action-alt px-6 py-2 fs-sm font-700 rounded-xl border border-primary-soft hover-bright">
                                    <i class="fas fa-paper-plane me-2"></i> {{ __('admin.test_connection') }}
                                </button>
                                <button type="submit" class="btn-gradient px-8 py-3 fs-sm font-700 rounded-xl">
                                    <i class="fas fa-save me-2"></i> {{ __('admin.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>


        <div id="seo-tab" class="tab-pane {{ request('tab') === 'seo' ? 'active' : 'hidden' }}">
            <div class="glass-card rounded-2xl p-8">
                <div class="d-flex justify-between align-center mb-8 border-bottom border-primary-soft pb-4">
                    <div class="d-flex align-center gap-3">
                        <div class="stat-icon-box bg-success-soft w-40 h-40">
                            <i class="fas fa-search fs-sm"></i>
                        </div>
                        <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.seo_settings') }}</h3>
                    </div>
                    <div class="ds-tabs-container p-1 glass-card rounded-lg">
                        @foreach($languages as $lang)
                            <button class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn rounded-md" data-group="seo" data-lang="{{ $lang->code }}">
                                {{ strtoupper($lang->code) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @forelse($languages as $lang)
                    <div data-lang-pane="{{ $lang->code }}" class="lang-pane-seo {{ $loop->first ? '' : 'hidden' }}">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="seo">
                            <input type="hidden" name="language_id" value="{{ $lang->id }}">
                            <div class="grid cols-1 cols-2 gap-6">
                                <div class="ds-form-group-vertical vstack gap-2">
                                    <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.seo_title') }}</label>
                                    <input type="text" name="seo_title" value="{{ $ds_settings->get('seo_title', null, $lang->id) }}" required class="input-premium">
                                </div>
                                <div class="ds-form-group-vertical vstack gap-2">
                                    <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.seo_keywords') }}</label>
                                    <input type="text" name="seo_keywords" value="{{ $ds_settings->get('seo_keywords', null, $lang->id) }}" class="input-premium" placeholder="keyword1, keyword2, keyword3">
                                </div>
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.seo_description') }}</label>
                                <textarea name="seo_description" class="input-premium" rows="4">{{ $ds_settings->get('seo_description', null, $lang->id) }}</textarea>
                            </div>
                            <div class="d-flex justify-end mt-8 pt-6 border-top border-primary-soft">
                                <button type="submit" class="btn-gradient px-6 py-2 fs-sm font-700 rounded-xl">
                                    <i class="fas fa-save me-2"></i> {{ __('admin.save') }} ({{ strtoupper($lang->code) }})
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    @include('admin.partials.no_languages')
                @endforelse
            </div>
        </div>


        <div id="contact-tab" class="tab-pane {{ request('tab') === 'contact' ? 'active' : 'hidden' }}">
            <div class="glass-card rounded-2xl p-8">
                <div class="d-flex justify-between align-center mb-8 border-bottom border-primary-soft pb-4">
                    <div class="d-flex align-center gap-3">
                        <div class="stat-icon-box bg-danger-soft w-40 h-40">
                            <i class="fas fa-address-book fs-sm"></i>
                        </div>
                        <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.contact_settings') }}</h3>
                    </div>
                    <div class="ds-tabs-container p-1 glass-card rounded-lg">
                        @foreach($languages as $lang)
                            <button class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn rounded-md" data-group="contact" data-lang="{{ $lang->code }}">
                                {{ strtoupper($lang->code) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @forelse($languages as $lang)
                    <div data-lang-pane="{{ $lang->code }}" class="lang-pane-contact {{ $loop->first ? '' : 'hidden' }}">
                        <form action="{{ route('admin.settings.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="group" value="contact">
                            <input type="hidden" name="language_id" value="{{ $lang->id }}">
                            <div class="grid cols-1 cols-2 gap-6">
                                <div class="ds-form-group-vertical vstack gap-2">
                                    <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.contact_email') }}</label>
                                    <input type="email" name="contact_email" value="{{ $ds_settings->get('contact_email', null, $lang->id) }}" required class="input-premium">
                                </div>
                                <div class="ds-form-group-vertical vstack gap-2">
                                    <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.contact_phone') }}</label>
                                    <input type="text" name="contact_phone" value="{{ $ds_settings->get('contact_phone', null, $lang->id) }}" class="input-premium">
                                </div>
                            </div>
                            <div class="ds-form-group-vertical vstack gap-2">
                                <label class="form-label-premium fs-xs uppercase letter-spacing-1">{{ __('admin.contact_address') }}</label>
                                <textarea name="contact_address" class="input-premium" rows="3">{{ $ds_settings->get('contact_address', null, $lang->id) }}</textarea>
                            </div>
                            <div class="d-flex justify-end mt-8 pt-6 border-top border-primary-soft">
                                <button type="submit" class="btn-gradient px-6 py-2 fs-sm font-700 rounded-xl">
                                    <i class="fas fa-save me-2"></i> {{ __('admin.save') }} ({{ strtoupper($lang->code) }})
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    @include('admin.partials.no_languages')
                @endforelse
            </div>
        </div>
        

        <div id="ai-tab" class="tab-pane {{ request('tab') === 'ai' ? 'active' : 'hidden' }}">
            <div class="glass-card rounded-2xl p-8">
                <div class="d-flex justify-between align-center mb-8 border-bottom border-primary-soft pb-4">
                    <div class="d-flex align-center gap-3">
                        <div class="stat-icon-box bg-purple-soft w-40 h-40">
                            <i class="fas fa-robot fs-sm"></i>
                        </div>
                        <h3 class="fs-md font-700 m-0 text-white">{{ __('admin.ai_settings') }}</h3>
                    </div>
                    <button type="button" data-ds-modal-open="addAIKeyModal" class="btn-gradient px-6 py-3 rounded-xl fs-xs font-700 border-none pointer transition-all hover-scale">
                        <i class="fas fa-plus me-2"></i> {{ __('admin.add_api_key') }}
                    </button>
                </div>

                @if($aiKeys->isEmpty())
                    <div class="vstack align-center justify-center p-12 opacity-50 bg-white-5 rounded-2xl border border-primary-soft">
                        <i class="fas fa-robot fs-2xl mb-4 text-primary"></i>
                        <p class="font-700 m-0 text-white">{{ __('admin.no_ai_keys_configured') }}</p>
                    </div>
                @else
                    <div class="grid cols-1 cols-2 cols-3 cols-4 gap-8">
                        @foreach($aiKeys as $key)
                            <div class="integration-card glass-card rounded-2xl p-6 hover-bright transition-all">
                                <div class="d-flex justify-between align-start mb-6">
                                    <div class="d-flex align-center gap-4">
                                        <div class="stat-icon-box bg-purple-soft w-56 h-56 rounded-2xl shadow-premium">
                                            <i class="fas fa-microchip text-primary fs-lg"></i>
                                        </div>
                                        <div>
                                            <p class="font-800 fs-md m-0 text-white">{{ strtoupper($key->provider) }}</p>
                                            <p class="fs-2xs text-muted m-0 font-600 uppercase letter-spacing-1">{{ $key->model }}</p>
                                        </div>
                                    </div>
                                    <form action="{{ route('admin.ai-keys.activate', $key->id) }}" method="POST">
                                        @csrf
                                        <div class="toggle-switch {{ $key->is_active ? 'active' : '' }}" onclick="this.closest('form').submit()"></div>
                                    </form>
                                </div>
                                <div class="d-flex justify-end gap-3 pt-5 border-top border-primary-soft">
                                    <button type="button" class="btn-action-premium test-ai-key-btn" data-url="{{ route('admin.ai-keys.test', $key->id) }}" title="{{ __('admin.test') }}">
                                        <i class="fas fa-vial"></i>
                                    </button>
                                    <button type="button" class="btn-action-premium ds-edit-ai-key" data-key="{{ json_encode($key) }}" data-url="{{ route('admin.ai-keys.update', $key->id) }}" title="{{ __('admin.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-action-premium btn-danger-soft" data-ds-confirm="{{ route('admin.ai-keys.destroy', $key->id) }}" data-ds-method="DELETE" title="{{ __('admin.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.settings.model')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testSmtpBtn = document.getElementById('test-smtp-btn');
    if (testSmtpBtn) {
        testSmtpBtn.addEventListener('click', function() {
            // Show loading
            DS_UI.loading(true);
            
            fetch("{{ route('admin.settings.test-smtp') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                DS_UI.loading(false);
                if (data.success) {
                    DS_UI.showToast('success', data.message);
                } else {
                    DS_UI.showToast('error', data.message);
                }
            })
            .catch(error => {
                DS_UI.loading(false);
                DS_UI.showToast('error', "{{ __('admin.smtp_test_failed') }}");
                console.error('SMTP Test Error:', error);
            });
        });
    }
});
</script>
@endpush
@endsection
