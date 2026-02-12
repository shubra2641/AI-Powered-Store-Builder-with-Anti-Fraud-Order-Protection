@extends('layouts.admin')

@section('content')
<!-- Header & Actions -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.language_management') }}</h2>
    </div>
    <button data-ds-modal-open="addLanguageModal" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700">
        <i class="fas fa-plus fs-xs"></i>
        <span>{{ __('admin.add_language') }}</span>
    </button>
</div>

<!-- Filters & Search -->
<div class="filter-bar">
    <div class="glass-card py-2 px-3 d-flex align-center gap-2 w-full mw-400">
        <i class="fas fa-search text-muted"></i>
        <input type="text"
               placeholder="{{ __('admin.search_placeholder') }}"
               class="bg-none border-none text-white outline-none w-full"
               data-ds-search="languagesTable">
    </div>
    <div class="ds-filter-dropdown" data-ds-filter="languagesTable" data-filter-col="direction">
        <button type="button" class="filter-item">
            <i class="fas fa-filter fs-xs"></i>
            <span class="ds-filter-label">{{ __('admin.all') }}</span>
            <i class="fas fa-chevron-down fs-2xs"></i>
        </button>
        <div class="ds-filter-menu">
            <button type="button" class="ds-filter-option active" data-value="all">{{ __('admin.all') }}</button>
            <button type="button" class="ds-filter-option" data-value="ltr">{{ __('admin.ltr') }}</button>
            <button type="button" class="ds-filter-option" data-value="rtl">{{ __('admin.rtl') }}</button>
        </div>
    </div>
    <div class="ds-filter-dropdown" data-ds-sort="languagesTable" data-sort-col="name">
        <button type="button" class="filter-item">
            <i class="fas fa-sort-alpha-down fs-xs ds-sort-icon"></i>
            <span class="ds-filter-label">{{ __('admin.name') }}</span>
            <i class="fas fa-chevron-down fs-2xs"></i>
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

<!-- Bulk Actions Bar (hidden until rows selected) -->
<div class="ds-bulk-bar hidden" id="bulkBar-languagesTable">
    <div class="d-flex align-center gap-3">
        <span class="ds-bulk-count">
            <strong id="bulkCount-languagesTable">0</strong> {{ __('admin.selected') }}
        </span>
        <button type="button" class="btn-action btn-action-delete ds-bulk-delete"
                data-ds-bulk-action="delete"
                data-table="languagesTable"
                data-bulk-url="{{ route('admin.languages.bulk-delete') }}">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    <button type="button" class="ds-bulk-clear" data-table="languagesTable">
        <i class="fas fa-times me-1"></i> {{ __('admin.clear_selection') }}
    </button>
</div>

<!-- Table Container -->
<div class="table-container table-responsive-premium" id="languagesTable">
    <table class="table-premium">
        <thead>
            <tr>
                <th class="w-50">
                    <input type="checkbox" class="ds-checkbox" data-ds-select-all="languagesTable">
                </th>
                <th>{{ __('admin.name') }}</th>
                <th class="d-none-mobile">{{ __('admin.code') }}</th>
                <th class="d-none-mobile">{{ __('admin.direction') }}</th>
                <th class="text-center">{{ __('admin.status') }}</th>
                <th class="text-end">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($languages as $lang)
                <tr data-searchable="{{ strtolower($lang->name . ' ' . $lang->code) }}"
                    data-filter-direction="{{ $lang->direction }}"
                    data-sort-name="{{ strtolower($lang->name) }}"
                    data-id="{{ $lang->id }}">
                    <td>
                        <input type="checkbox" class="ds-checkbox" data-ds-row-check="languagesTable" value="{{ $lang->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-center gap-3">
                            <div class="avatar-circle {{ $loop->index % 2 == 0 ? 'avatar-primary' : 'avatar-secondary' }}">
                                {{ substr($lang->code, 0, 2) }}
                            </div>
                            <div class="vstack">
                                <span class="fs-sm font-700">{{ $lang->name }}</span>
                                @if($lang->is_default)
                                    <span class="text-primary fs-2xs font-700">{{ __('admin.default') }}</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="d-none-mobile">
                        <span class="badge-tag bg-purple-soft text-purple fs-xs font-700 uppercase">{{ $lang->code }}</span>
                    </td>
                    <td class="d-none-mobile">
                        <span class="badge-pill {{ $lang->direction == 'rtl' ? 'badge-pill-warning' : 'badge-pill-primary' }}">
                            <i class="fas {{ $lang->direction == 'rtl' ? 'fa-align-right' : 'fa-align-left' }}"></i>
                            {{ $lang->direction == 'rtl' ? __('admin.rtl') : __('admin.ltr') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge-pill badge-pill-success">
                            <i class="fas fa-check-circle"></i>
                            {{ __('admin.active') }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-end gap-2">
                            @if(!$lang->is_default)
                                <button data-ds-confirm="{{ route('admin.languages.set-default', $lang->id) }}"
                                        data-ds-message="{{ __('admin.confirm_default') }}"
                                        data-ds-method="POST"
                                        data-ds-btn-class="bg-success"
                                        class="btn-action btn-action-default" title="{{ __('admin.set_as_default') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                            @endif
                            <button class="btn-action btn-action-edit ds-edit-language"
                                    data-lang="{{ json_encode($lang) }}"
                                    data-url="{{ route('admin.languages.update', $lang->id) }}"
                                    title="{{ __('admin.edit_language') }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            @if(!$lang->is_default)
                                <button data-ds-confirm="{{ route('admin.languages.destroy', $lang->id) }}"
                                        data-ds-message="{{ __('admin.confirm_delete') }}"
                                        data-ds-method="DELETE"
                                        data-ds-btn-class="bg-danger"
                                        class="btn-action btn-action-delete" title="{{ __('admin.delete_language') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- No Results Message -->
    <div class="ds-no-results hidden">
        <i class="fas fa-search"></i>
        <p>{{ __('admin.no_results_found') }}</p>
    </div>

    <!-- Pagination -->
    @if(method_exists($languages, 'links'))
        {{ $languages->links('vendor.pagination.ds-premium') }}
    @endif
</div>

@include('admin.languages.modal')

@endsection
