    <!-- Confirmation Modal Component -->
    <x-modal id="confirmationModal" title="{{ __('admin.confirm_action') }}" size="sm">
        <div class="ds-confirm-content">
            <div class="glass-card p-3 border-radius-circle d-flex align-center justify-center bg-orange-soft mb-4 w-72 h-72">
                <i class="fas fa-exclamation-triangle text-warning fs-xl"></i>
            </div>
            <h5 id="confirmMessage" class="ds-confirm-message"></h5>
            <span class="ds-confirm-subtext">{{ __('admin.confirm_undone') }}</span>
        </div>
        <div class="ds-confirm-footer">
            <div class="ds-confirm-btn-wrap">
                <button type="button" class="btn-dark" data-ds-modal-close="confirmationModal">
                    {{ __('admin.cancel') }}
                </button>
            </div>
            <form id="confirmForm" method="POST" class="ds-confirm-btn-wrap">
                @csrf
                <div id="confirmMethod"></div>
                <button type="submit" id="confirmBtn" class="btn-gradient">
                    {{ __('admin.confirm') }}
                </button>
            </form>
        </div>
    </x-modal>