@extends('layouts.admin')

@section('content')
<!-- Header & Actions -->
<div class="d-flex justify-between align-center mb-6">
    <div>
        <h2 class="fs-2xl font-800 m-0 gradient-text">{{ __('admin.notifications') }}</h2>
        <p class="text-muted fs-sm m-0 mt-1">{{ __('admin.notifications_desc') }}</p>
    </div>
    <div class="d-flex gap-3">
        @if($notifications->count() > 0)
            <button type="button" 
                    class="btn-dark d-flex align-center gap-2 py-2 px-4 border-none font-700 pointer transition-all hover:scale-105"
                    data-ds-confirm="{{ route('admin.notifications.mark-all-read') }}"
                    data-ds-message="{{ __('admin.confirm_mark_all_read') }}"
                    data-ds-method="POST"
                    data-ds-btn-class="bg-success">
                <i class="fas fa-check-double fs-xs text-primary"></i>
                <span>{{ __('admin.mark_all_read') }}</span>
            </button>

            <button type="button" 
                    class="btn-dark d-flex align-center gap-2 py-2 px-4 border-none font-700 text-danger-hover pointer transition-all hover:scale-105"
                    data-ds-confirm="{{ route('admin.notifications.destroy-all') }}"
                    data-ds-message="{{ __('admin.confirm_delete_all_notifications') }}"
                    data-ds-method="DELETE"
                    data-ds-btn-class="bg-danger">
                <i class="fas fa-trash-alt fs-xs"></i>
                <span>{{ __('admin.delete_all') }}</span>
            </button>
        @endif
    </div>
</div>

<!-- Notifications List -->
<div class="vstack gap-3">
    @forelse($notifications as $notification)
        <div class="glass-card p-0 notif-card {{ $notification->read_at ? '' : 'unread' }}">
            @if(!$notification->read_at)
                <div class="unread-glow" title="{{ __('admin.new') }}"></div>
            @endif
            
            <div class="d-flex flex-column flex-md-row align-start align-md-center gap-4 p-4">
                <!-- Premium Icon Box -->
                <div class="notif-icon-wrapper type-{{ $notification->data['type'] ?? 'info' }}">
                    <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                </div>
                
                <!-- Content -->
                <div class="flex-1">
                    <div class="d-flex flex-column flex-md-row justify-between align-start align-md-center mb-1 gap-2">
                        <h4 class="m-0 fs-md font-700 text-white truncate-1 mw-full mw-md-250">
                            {{ $notification->data['title'] ?? 'Notification' }}
                        </h4>
                        <span class="text-muted fs-xs font-500 bg-dark py-1 px-2 border-radius-sm text-nowrap">
                            <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="m-0 text-muted fs-sm leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                    
                    @if(isset($notification->data['url']))
                        <div class="mt-3">
                            <a href="{{ $notification->data['url'] }}" class="btn-dark py-1 px-3 fs-xs font-600 text-primary hover-underline border-radius-sm">
                                {{ __('admin.view_details') }} <i class="fas fa-arrow-right fs-2xs ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 ms-0 ms-md-4 mt-3 mt-md-0">
                    @if(!$notification->read_at)
                        <form action="{{ route('admin.notifications.mark-read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-action btn-action-edit" title="{{ __('admin.mark_as_read') }}">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    @endif
                    <button type="button" 
                            class="btn-action btn-action-delete" 
                            title="{{ __('admin.delete') }}"
                            data-ds-confirm="{{ route('admin.notifications.destroy', $notification->id) }}"
                            data-ds-message="{{ __('admin.confirm_delete') }}"
                            data-ds-method="DELETE"
                            data-ds-btn-class="bg-danger">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="glass-card vstack align-center justify-center py-10 opacity-50">
            <div class="glass-card-circle mb-4">
                <i class="far fa-bell-slash fs-xl"></i>
            </div>
            <h3 class="fs-lg font-700 text-white mb-2">{{ __('admin.no_notifications') }}</h3>
            <p class="text-muted m-0 fs-sm">{{ __('admin.all_caught_up') }}</p>
        </div>
    @endforelse

    <!-- Pagination Container -->
    @if($notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
