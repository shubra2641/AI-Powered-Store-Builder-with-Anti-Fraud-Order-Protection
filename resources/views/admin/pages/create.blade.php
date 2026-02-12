@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="mb-5">
    <a href="{{ route('admin.pages.index') }}" class="text-muted fs-xs d-inline-flex align-center gap-1 mb-2 hover-text-primary transition-all">
        <i class="fas fa-arrow-left"></i>
        <span>{{ __('admin.back_to_pages') }}</span>
    </a>
    <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.add_new_page') }}</h2>
    <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.create_legal_page_description') }}</p>
</div>

<form action="{{ route('admin.pages.store') }}" method="POST">
    @csrf
    <div class="grid grid-cols-12 gap-5">
        <!-- Main Content (Left) -->
        <div class="col-span-8">
            <div class="glass-card p-4">
                <div class="d-flex justify-between align-center mb-4 border-bottom border-primary-soft pb-2">
                    <h4 class="fs-md font-700 m-0 text-white">{{ __('admin.page_content') }}</h4>
                    <!-- Language Tabs -->
                    <div class="ds-tabs-container">
                        @foreach($languages as $lang)
                            <button type="button" 
                                    class="ds-tab-btn-xs {{ $loop->first ? 'active' : '' }} lang-switch-btn" 
                                    data-group="pages"
                                    data-lang="{{ $lang->code }}">
                                {{ strtoupper($lang->code) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @foreach($languages as $lang)
                    <div data-lang-pane="{{ $lang->code }}" class="lang-pane-pages {{ $loop->first ? '' : 'hidden' }}">
                        <div class="vstack gap-4">
                            <div class="ds-form-group">
                                <label class="form-label-premium">{{ __('admin.title') }} ({{ $lang->name }})</label>
                                <input type="text" 
                                       name="translations[{{ $lang->id }}][title]" 
                                       required 
                                       class="input-premium" 
                                       placeholder="{{ __('admin.enter_page_title') }}">
                            </div>
                            
                            <div class="ds-form-group">
                                <label class="form-label-premium">{{ __('admin.content') }} ({{ $lang->name }})</label>
                                <textarea name="translations[{{ $lang->id }}][content]" 
                                          class="input-premium ds-editor" 
                                          rows="15" 
                                          placeholder="{{ __('admin.enter_page_content') }}"></textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Sidebar (Right) -->
        <div class="col-span-4">
            <div class="vstack gap-4 sticky-top-5">
                <!-- Settings Card -->
                <div class="glass-card p-4">
                    <h4 class="fs-md font-700 mb-4 text-white border-bottom border-primary-soft pb-2">{{ __('admin.settings') }}</h4>
                    
                    <div class="ds-form-group">
                        <label class="form-label-premium">{{ __('admin.visibility') }}</label>
                        <select name="is_active" class="input-premium no-appearance">
                            <option value="1">{{ __('admin.published') }}</option>
                            <option value="0">{{ __('admin.hidden') }}</option>
                        </select>
                        <p class="text-muted fs-2xs mt-2">{{ __('admin.visibility_help') }}</p>
                    </div>

                    <div class="ds-divider-v w-full my-4 bg-primary-soft opacity-20"></div>

                    <div class="vstack gap-2">
                        <button type="submit" class="btn-gradient w-full py-3 font-700 d-flex align-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            <span>{{ __('admin.publish_page') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="glass-card p-4 bg-primary-soft border-primary-soft">
                    <div class="d-flex align-center gap-2 text-primary mb-2">
                        <i class="fas fa-magic"></i>
                        <span class="font-700 fs-sm">{{ __('admin.auto_slug') }}</span>
                    </div>
                    <p class="fs-2xs text-muted m-0">
                        {{ __('admin.auto_slug_description') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
