@extends('layouts.admin')

@section('content')
<!-- Header & Actions -->
<div class="d-flex justify-between align-center mb-5">
    <div>
        <h2 class="fs-xl font-800 m-0 gradient-text">{{ __('admin.email_templates') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.manage_email_templates') }}</p>
    </div>
    <div class="d-flex gap-3">
        <a href="{{ route('admin.emails.send-form') }}" class="btn-dark d-flex align-center gap-2 py-2 px-4 border-none font-700 pointer transition-all hover:scale-105">
            <i class="fas fa-paper-plane fs-xs text-primary"></i>
            <span>{{ __('admin.send_bulk_email') }}</span>
        </a>
        <a href="{{ route('admin.emails.create') }}" class="btn-gradient d-flex align-center gap-2 py-2 px-4 cursor-pointer border-none font-700">
            <i class="fas fa-plus fs-xs"></i>
            <span>{{ __('admin.add_template') }}</span>
        </a>
    </div>
</div>

<!-- Table Container -->
<div class="table-container table-responsive-premium" id="templatesTable">
    <table class="table-premium">
        <thead>
            <tr>
                <th>{{ __('admin.name') }}</th>
                <th class="d-none-mobile">{{ __('admin.language') }}</th>
                <th>{{ __('admin.status') }}</th>
                <th class="text-end">{{ __('admin.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
                <tr>
                    <td>
                        <div class="vstack">
                            <span class="font-700 text-white">{{ $template->name }}</span>
                            <span class="text-muted fs-2xs font-mono opacity-50">{{ $template->slug }}</span>
                        </div>
                    </td>
                    <td class="d-none-mobile">
                        <span class="badge-pill badge-secondary fs-xs">
                            {{ $template->language->name ?? 'Default' }}
                        </span>
                    </td>
                    <td>
                        @if($template->is_system)
                            <span class="status-badge status-active">
                                <i class="fas fa-shield-alt"></i> {{ __('admin.is_system') }}
                            </span>
                        @else
                            <span class="status-badge status-inactive">
                                <i class="fas fa-user-edit"></i> {{ __('admin.custom') }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-end gap-2">
                            <a href="{{ route('admin.emails.edit', $template->id) }}" 
                               class="btn-action btn-action-edit" 
                               title="{{ __('admin.edit_template') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$template->is_system)
                                <button data-ds-confirm="{{ route('admin.emails.destroy', $template->id) }}"
                                        data-ds-message="{{ __('admin.confirm_delete') }}"
                                        data-ds-method="DELETE"
                                        data-ds-btn-class="bg-danger"
                                        class="btn-action btn-action-delete" 
                                        title="{{ __('admin.delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $templates->links() }}
    </div>
</div>
@endsection
