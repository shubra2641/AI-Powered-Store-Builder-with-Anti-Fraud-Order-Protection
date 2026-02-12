@extends('layouts.admin')

@section('content')
<!-- Header -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.legal_pages') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.manage_legal_pages_description') }}</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700 fs-xs">
        <i class="fas fa-plus fs-2xs"></i>
        <span>{{ __('admin.add_new_page') }}</span>
    </a>
</div>

<!-- Filter Bar -->
<div class="d-flex justify-between align-center mb-4 gap-3">
    <div class="glass-card py-2 px-3 d-flex align-center gap-2 w-full mw-400">
        <i class="fas fa-search text-muted"></i>
        <input type="text" 
               placeholder="{{ __('admin.search_pages') }}..." 
               class="bg-none border-none text-white outline-none w-full"
               data-ds-search="pagesTable">
    </div>

    <!-- Status Filter -->
    <div class="ds-filter-dropdown" data-ds-filter="pagesTable" data-filter-col="status">
        <button type="button" class="filter-item">
            <i class="fas fa-filter fs-xs"></i>
            <span class="ds-filter-label">{{ __('admin.all') }}</span>
            <i class="fas fa-chevron-down fs-2xs"></i>
        </button>
        <div class="ds-filter-menu">
            <button type="button" class="ds-filter-option active" data-value="all">{{ __('admin.all') }}</button>
            <button type="button" class="ds-filter-option" data-value="active">{{ __('admin.active') }}</button>
            <button type="button" class="ds-filter-option" data-value="hidden">{{ __('admin.hidden') }}</button>
        </div>
    </div>
</div>

<!-- Table -->
<div class="glass-card p-0 overflow-hidden">
    <div class="table-container table-responsive-premium" id="pagesTable">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>{{ __('admin.page_title') }}</th>
                    <th class="d-none-mobile">{{ __('admin.slug') }}</th>
                    <th class="text-center">{{ __('admin.status') }}</th>
                    <th class="text-center d-none-mobile">{{ __('admin.last_updated') }}</th>
                    <th class="text-end">{{ __('admin.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr data-searchable="{{ strtolower(($page->translation()->title ?? __('admin.untitled')) . ' ' . $page->slug) }}"
                        data-filter-status="{{ $page->is_active ? 'active' : 'hidden' }}">
                        <td>
                            <div class="d-flex align-center gap-3">
                                <div class="avatar-circle avatar-primary">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div>
                                    <div class="fs-sm font-700 text-white">{{ $page->translation()->title ?? __('admin.untitled') }}</div>
                                    <div class="fs-2xs text-muted">{{ count($page->translations) }} {{ __('admin.translations') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="d-none-mobile">
                            <code class="fs-xs text-primary">{{ $page->slug }}</code>
                        </td>
                        <td class="text-center">
                            @if($page->is_active)
                                <span class="badge-pill badge-pill-success">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('admin.active') }}
                                </span>
                            @else
                                <span class="badge-pill badge-pill-danger opacity-70">
                                    <i class="fas fa-eye-slash"></i>
                                    {{ __('admin.hidden') }}
                                </span>
                            @endif
                        </td>
                        <td class="text-center d-none-mobile fs-2xs text-muted">
                            {{ $page->updated_at->diffForHumans() }}
                        </td>
                        <td>
                            <div class="d-flex justify-end gap-2">
                                <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn-action btn-action-edit" title="{{ __('admin.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button data-ds-confirm="{{ route('admin.pages.destroy', $page->id) }}"
                                        data-ds-message="{{ __('admin.confirm_delete_page') }}"
                                        data-ds-method="DELETE"
                                        data-ds-btn-class="bg-danger"
                                        class="btn-action btn-action-delete" title="{{ __('admin.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="ds-no-results">
                                <i class="fas fa-file-invoice"></i>
                                <p>{{ __('admin.no_pages_found') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pages->hasPages())
        <div class="p-4 border-top border-primary-soft">
            {{ $pages->links('vendor.pagination.ds-premium') }}
        </div>
    @endif
</div>
@endsection
