@extends('layouts.admin')

@section('content')
<!-- Header Section -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text uppercase tracking-wider">{{ __('admin.landing_pages') }}</h2>
        <p class="text-muted fs-xs m-0 mt-1 font-600 opacity-60">{{ __('admin.manage_independent_landing_pages') }}</p>
    </div>
    <button data-ds-modal-open="createPageModal" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700 shadow-glow hover-scale">
        <i class="fas fa-plus fs-xs"></i>
        <span>{{ __('admin.create_new_page') }}</span>
    </button>
</div>

<!-- Premium Stats Grid (Standardized) -->
<div class="grid cols-4 gap-4 mb-5">
    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0">{{ number_format($stats['total'] ?? 0) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.total_pages') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-purple">
                <i class="fas fa-file-invoice fs-lg"></i>
            </div>
        </div>
    </div>

    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-success">{{ number_format($stats['active'] ?? 0) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.active_pages') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center bg-green-soft-overlay text-success">
                <i class="fas fa-check-circle fs-lg"></i>
            </div>
        </div>
    </div>

    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-orange">{{ number_format($stats['pending'] ?? 0) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.pending_pages') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-orange">
                <i class="fas fa-clock fs-lg"></i>
            </div>
        </div>
    </div>

    <div class="stat-card-premium">
        <div class="d-flex justify-between align-start">
            <div>
                <h2 class="fs-xl font-800 m-0 text-cyan">{{ number_format($stats['views'] ?? 0) }}</h2>
                <p class="text-muted fs-xs mt-1">{{ __('admin.total_views') }}</p>
            </div>
            <div class="stat-icon-box border-radius-sm w-48 h-48 d-flex align-center justify-center stat-icon-cyan">
                <i class="fas fa-eye fs-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Elite Filter Bar (Standardized) -->
<div class="filter-bar mb-5">
    <div class="glass-card py-2 px-3 d-flex align-center gap-2 w-full mw-md-400">
        <i class="fas fa-search text-muted opacity-50"></i>
        <input type="text" placeholder="{{ __('admin.search_placeholder') }}..." class="bg-none border-none text-white outline-none w-full font-600 fs-sm placeholder:opacity-40" data-ds-search="pagesGrid">
    </div>
    
    <div class="ds-filter-dropdown" data-ds-filter="pagesGrid" data-filter-col="status">
        <button type="button" class="filter-item">
            <i class="fas fa-filter fs-xs text-primary"></i>
            <span class="ds-filter-label">{{ __('admin.all_statuses') }}</span>
            <i class="fas fa-chevron-down fs-2xs opacity-50"></i>
        </button>
        <div class="ds-filter-menu">
            <button type="button" class="ds-filter-option active" data-value="all">{{ __('admin.all_statuses') }}</button>
            <button type="button" class="ds-filter-option" data-value="active">{{ __('admin.active') }}</button>
            <button type="button" class="ds-filter-option" data-value="draft">{{ __('admin.draft') }}</button>
        </div>
    </div>

    <div class="ds-filter-dropdown" data-ds-sort="pagesGrid" data-sort-col="title">
        <button type="button" class="filter-item">
            <i class="fas fa-sort-alpha-down fs-xs text-primary"></i>
            <span class="ds-filter-label">{{ __('admin.title') }}</span>
            <i class="fas fa-chevron-down fs-2xs opacity-50"></i>
        </button>
        <div class="ds-filter-menu">
            <button type="button" class="ds-sort-option active" data-dir="asc">
                <i class="fas fa-sort-alpha-down me-2"></i> A → Z
            </button>
            <button type="button" class="ds-sort-option" data-dir="desc">
                <i class="fas fa-sort-alpha-up me-2"></i> Z → A
            </button>
        </div>
    </div>
</div>

<!-- Landing Pages Container (Table structure for Filter JS compatibility) -->
<div class="p-0 bg-none shadow-none" id="pagesGrid">
    <table class="w-full d-block">
        <tbody class="grid cols-3 md:cols-2 sm:cols-1 gap-6 d-grid">
            @forelse($pages as $page)
                <tr class="service-card group p-0 bg-dark-card/40 backdrop-blur-xl shadow-2xl overflow-hidden d-flex flex-column" 
                    data-searchable="{{ $page->search_text }}"
                    data-filter-status="{{ $page->status_slug }}"
                    data-sort-title="{{ $page->search_text }}">
                    <td class="p-0 border-none">
                        <!-- Preview Frame -->
                        @php
                            $icons = ['rocket', 'chart-bar', 'graduation-cap', 'briefcase', 'mobile-alt', 'gift'];
                            $colors = ['text-primary', 'text-secondary', 'text-accent', 'text-success', 'text-purple', 'text-cyan'];
                            
                            $cardIcon = $icons[$page->id % count($icons)];
                            $iconColorClass = $colors[$page->id % count($colors)];
                            
                            $badgeClass = $page->is_active ? 'status-badge status-active' : 'status-badge status-inactive';
                        @endphp

                        <div class="bg-dark-overlay relative d-flex align-center justify-center overflow-hidden border-bottom border-white/5 h-180">
                            <div class="text-center">
                                <i class="fas fa-{{ $cardIcon }} fs-4xl {{ $iconColorClass }} mb-2"></i>
                                <p class="text-muted fs-xs font-700 opacity-60">{{ __('admin.page_preview') }}</p>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="d-flex align-center justify-between mb-3">
                                <h3 class="fs-lg font-800 m-0">{{ $page->translations->first()?->title ?? __('admin.untitled') }}</h3>
                                <span class="{{ $badgeClass }} px-2 py-1">
                                    {{ $page->status_label }}
                                </span>
                            </div>
                            
                            <p class="text-muted fs-xs mb-5 truncate-2 font-600 line-height-relaxed text-start opacity-70">
                                {{ $page->translations->first()?->meta_description ?? __('admin.manage_independent_landing_pages') }}
                            </p>
                            
                            <div class="d-flex align-center gap-4 fs-2xs text-muted mb-5 font-800 opacity-60">
                                <span><i class="fas fa-eye me-1 text-secondary"></i> {{ number_format($page->views ?? 0) }}</span>
                                <span><i class="fas fa-mouse-pointer me-1 text-primary"></i> 0%</span>
                                <span><i class="fas fa-calendar me-1 text-accent"></i> {{ $page->created_at->format('Y/m/d') }}</span>
                            </div>

                            <div class="d-flex align-center gap-2 mt-auto">
                                <a href="{{ route('admin.landing-pages.builder', $page->id) }}" class="btn-action btn-action-edit flex-1 rounded-lg d-flex align-center justify-center gap-2 h-40" title="{{ __('admin.edit') }}">
                                    <i class="fas fa-edit"></i>
                                    <span class="fs-xs font-800">{{ __('admin.edit') }}</span>
                                </a>
                                <a href="{{ $page->preview_url }}" target="_blank" class="btn-action btn-action-view flex-1 rounded-lg d-flex align-center justify-center gap-2 h-40" title="{{ __('admin.view') }}">
                                    <i class="fas fa-eye"></i>
                                    <span class="fs-xs font-800">{{ __('admin.view') }}</span>
                                </a>
                                <button type="button" class="btn-action btn-action-delete rounded-lg d-flex align-center justify-center w-40 h-40"
                                        data-ds-confirm="{{ route('admin.landing-pages.destroy', $page->id) }}"
                                        data-ds-message="{{ __('admin.confirm_delete') }}"
                                        data-ds-method="DELETE"
                                        data-ds-btn-class="bg-danger"
                                        title="{{ __('admin.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="col-span-3 d-block">
                    <td class="p-0 border-none w-full">
                        <div class="glass-card d-flex flex-column align-center justify-center py-10 px-5 text-center min-h-300">
                            <div class="stat-icon-box w-80 h-80 d-flex align-center justify-center border-radius-circle mb-4 bg-primary-soft-overlay text-primary">
                                <i class="fas fa-layer-group fs-3xl"></i>
                            </div>
                            <h3 class="fs-xl font-800 text-white mb-2">{{ __('admin.no_landing_pages_found') }}</h3>
                            <p class="text-muted fs-sm mw-md-400 mx-auto mb-5 line-height-relaxed opacity-70">
                                {{ __('admin.manage_independent_landing_pages') }}
                            </p>
                            <button data-ds-modal-open="createPageModal" class="btn-gradient d-flex align-center gap-2 py-3 px-6 border-radius-lg hover-scale cursor-pointer border-none font-700 shadow-glow">
                                <i class="fas fa-magic"></i>
                                <span>{{ __('admin.create_new_page') }}</span>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- No Results Message (Used by DS_UI JS) -->
    <div class="ds-no-results hidden">
        <i class="fas fa-search"></i>
        <p>{{ __('admin.no_results_found') }}</p>
    </div>

    <!-- Pagination -->
    @if(method_exists($pages, 'links'))
        {{ $pages->links('vendor.pagination.ds-premium') }}
    @endif
</div>

<!-- Create Page Modal (Standardized) -->
<x-modal id="createPageModal" title="{{ __('admin.create_landing_page') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-plus-circle"></i>
        </div>
    </div>
    <form action="{{ route('admin.landing-pages.store') }}" method="POST">
        @csrf
        <div class="ds-form-group-horizontal mb-4">
            <label class="form-label-premium">{{ __('admin.title') }}</label>
            <input type="text" name="title" class="input-premium" placeholder="e.g. AI SaaS Launch Page" required>
        </div>
        
        <div class="alert alert-info py-2 px-3 fs-xs mb-3 text-white bg-primary-soft border-primary-soft border-radius-sm">
            <i class="fas fa-info-circle me-1"></i> {{ __('admin.title_slug_hint') }}
        </div>

        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="createPageModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.create') }}</button>
        </div>
    </form>
</x-modal>
@endsection
