@extends('layouts.admin')

@section('content')
<div class="mb-5">
    <a href="{{ route('admin.emails.index') }}" class="text-muted fs-xs d-inline-flex align-center gap-1 mb-2 hover-text-primary transition-all">
        <i class="fas fa-arrow-left"></i>
        <span>{{ __('admin.back_to_list') }}</span>
    </a>
    <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.edit_template') }}</h2>
    <p class="text-muted fs-xs m-0 mt-1">{{ $template->slug }}</p>
</div>

<form action="{{ route('admin.emails.update', $template->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-12 gap-5">
        <!-- Main Form (75%) -->
        <div class="col-span-12 col-span-9">
            <div class="glass-card p-5">
                <div class="ds-form-group mb-4">
                    <label class="form-label-premium">{{ __('admin.template_name') }}</label>
                    <input type="text" name="name" value="{{ $template->name }}" required class="input-premium">
                </div>

                <div class="ds-form-group mb-4">
                    <label class="form-label-premium">{{ __('admin.template_subject') }}</label>
                    <input type="text" name="subject" id="template_subject" value="{{ $template->subject }}" required class="input-premium">
                </div>

                <div class="ds-form-group mb-4">
                    <label class="form-label-premium">{{ __('admin.template_content') }}</label>
                    <textarea name="content" id="template_content" rows="12" required class="input-premium">{{ $template->content }}</textarea>
                </div>

                <div class="ds-form-group mb-4">
                    <label class="form-label-premium">{{ __('admin.description') }}</label>
                    <textarea name="description" rows="2" class="input-premium">{{ $template->description }}</textarea>
                </div>

                <div class="d-flex justify-end gap-3 mt-4">
                    <a href="{{ route('admin.emails.index') }}" class="btn-dark">{{ __('admin.cancel') }}</a>
                    <button type="submit" class="btn-gradient px-5">{{ __('admin.save_changes') }}</button>
                </div>
            </div>
        </div>

        <!-- Sidebar (25%) -->
        <div class="col-span-12 col-span-3 vstack gap-5">
            <!-- Live Preview -->
            <div class="glass-card p-4">
                <h4 class="fs-sm font-800 mb-3 text-primary d-flex align-center gap-2">
                    <i class="fas fa-eye"></i> {{ __('admin.live_preview') }}
                </h4>
                
                <div class="preview-container preview-light border-radius-sm p-3 overflow-hidden mb-2 min-h-250">
                    <h5 id="preview_subject" class="fs-sm font-700 mb-2 border-bottom pb-2"></h5>
                    <div id="preview_content" class="fs-xs"></div>
                </div>
                
                <p class="text-muted fs-3xs opacity-50 m-0">
                    <i class="fas fa-info-circle"></i> {{ __('admin.no_js_blade_execution') }}
                </p>
            </div>

            <!-- Placeholders -->
            <div class="glass-card p-4">
                <h4 class="fs-sm font-800 mb-3 text-primary d-flex align-center gap-2">
                    <i class="fas fa-brackets-curly"></i> {{ __('admin.placeholders') }}
                </h4>
                <div class="vstack gap-2">
                    <div class="p-2 selection-box border-radius-sm border-white-5 hstack justify-between">
                        <span class="text-muted fs-xs">{{ __('admin.user_name_placeholder') }}</span>
                        <code class="text-primary fs-xs pointer copy-placeholder" data-copy="@{{ $user->name }}">@{{ $user->name }}</code>
                    </div>
                    <div class="p-2 selection-box border-radius-sm border-white-5 hstack justify-between">
                        <span class="text-muted fs-xs">{{ __('admin.user_email_placeholder') }}</span>
                        <code class="text-primary fs-xs pointer copy-placeholder" data-copy="@{{ $user->email }}">@{{ $user->email }}</code>
                    </div>
                    @if($template->slug === 'activation_email')
                        <div class="p-2 selection-box border-radius-sm border-white-5 hstack justify-between">
                            <span class="text-muted fs-xs">Activation URL</span>
                            <code class="text-primary fs-xs pointer copy-placeholder" data-copy="@{{ $activation_url }}">@{{ $activation_url }}</code>
                        </div>
                    @elseif($template->slug === 'password_reset_email')
                        <div class="p-2 selection-box border-radius-sm border-white-5 hstack justify-between">
                            <span class="text-muted fs-xs">Reset URL</span>
                            <code class="text-primary fs-xs pointer copy-placeholder" data-copy="@{{ $reset_url }}">@{{ $reset_url }}</code>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/admin-emails.js') }}"></script>
@endpush
