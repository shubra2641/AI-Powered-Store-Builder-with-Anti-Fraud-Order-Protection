@props(['id', 'title', 'size' => 'md'])

<div id="{{ $id }}" class="ds-modal-overlay">
    <div class="ds-modal-card ds-modal-{{ $size }}">
        <div class="ds-modal-header">
            <h3 class="ds-modal-title">{{ $title }}</h3>
            <button type="button" data-ds-modal-close="{{ $id }}" class="ds-modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="ds-modal-body">
            {{ $slot }}
        </div>
    </div>
</div>
