@extends('layouts.admin')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.emails.index') }}" class="text-muted fs-xs d-inline-flex align-center gap-1 mb-2 hover-text-primary transition-all">
        <i class="fas fa-arrow-left"></i>
        <span>{{ __('admin.back_to_list') }}</span>
    </a>
    <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.send_bulk_email') }}</h2>
</div>

<form action="{{ route('admin.emails.send-bulk') }}" method="POST">
    @csrf
    <div class="grid grid-cols-12 gap-5">
        <!-- Main Form (75%) -->
        <div class="col-span-12 col-span-9">
            <div class="glass-card p-5">
                <!-- Mode Selection -->
                <div class="ds-form-group mb-5">
                    <label class="form-label-premium">{{ __('admin.select_template') }}</label>
                    <select name="template_id" id="template_select" class="input-premium no-appearance">
                        <option value="">-- {{ __('admin.direct_message') }} --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" data-subject="{{ $template->subject }}" data-content="{{ $template->content }}">
                                {{ $template->name }} ({{ $template->language->name ?? 'en' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="ds-form-group mb-4">
                    <label class="form-label-premium">{{ __('admin.message_subject') }}</label>
                    <input type="text" name="subject" id="message_subject" required class="input-premium" placeholder="{{ __('admin.template_subject') }}">
                </div>

                <div class="ds-form-group mb-4">
                    <label class="form-label-premium">{{ __('admin.message_content') }}</label>
                    <textarea name="content" id="message_content" rows="12" required class="input-premium" placeholder="{{ __('admin.template_content') }}"></textarea>
                </div>

                <div class="ds-form-group mb-5">
                    <label class="form-label-premium">{{ __('admin.select_users') }}</label>
                    <p class="text-muted fs-2xs mb-2">{{ __('admin.bulk_email_hint') }}</p>
                    <div class="user-selection-box selection-box border-radius-sm p-3 overflow-y-auto h-200">
                        @foreach($users as $user)
                            <label class="hstack gap-3 py-2 px-3 hover:bg-white-5 border-radius-xs pointer mb-1">
                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="ds-checkbox">
                                <div class="hstack gap-2">
                                    <span class="fs-xs font-600 text-white">{{ $user->name }}</span>
                                    <span class="fs-2xs text-muted">({{ $user->email }})</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="alert alert-info bg-primary-soft p-4 border-radius-sm text-white fs-xs mb-4">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    {{ __('admin.bulk_email_queue_info') }}
                </div>

                <div class="d-flex justify-end gap-3">
                    <a href="{{ route('admin.emails.index') }}" class="btn-dark">{{ __('admin.cancel') }}</a>
                    <button type="submit" class="btn-gradient px-6 py-2 fs-md d-flex align-center gap-2">
                        <i class="fas fa-paper-plane fs-xs"></i>
                        <span>{{ __('admin.send_now') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar (25%) -->
        <div class="col-span-12 col-span-3 vstack gap-5">
            <div class="glass-card p-4 sticky-top-5">
                <h4 class="fs-sm font-800 mb-3 text-primary d-flex align-center gap-2">
                    <i class="fas fa-eye"></i> {{ __('admin.live_preview') }}
                </h4>
                
                <div class="preview-container preview-light border-radius-sm p-3 overflow-hidden mb-3 min-h-300">
                    <h5 id="preview_subject" class="fs-sm font-700 mb-3 border-bottom pb-2"></h5>
                    <div id="preview_content" class="fs-xs"></div>
                </div>

                <p class="text-muted fs-3xs opacity-50 m-0">
                    <i class="fas fa-exclamation-triangle"></i> {{ __('admin.no_js_blade_execution') }}
                </p>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin-emails.js') }}"></script>
@endpush
