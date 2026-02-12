<!-- Profile Edit Modal -->
<x-modal id="profileModal" title="{{ __('auth.edit_profile') }}">
    <div class="ds-modal-avatar-header">
        <div class="glass-card p-2 border-radius-circle d-flex align-center justify-center bg-purple-soft w-80 h-80">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=8b5cf6&color=fff" 
                 alt="{{ auth()->user()->name }}" 
                 class="w-full h-full object-cover border-radius-circle">
        </div>
    </div>
    
    <form action="{{ route('profile.update') }}" method="POST" class="vstack gap-4">
        @csrf
        @method('PUT')

        <div class="d-flex gap-4 flex-column-mobile">
            <div class="ds-form-group-horizontal flex-1">
                <label for="profile_name" class="form-label-premium">{{ __('auth.full_name_label') }}</label>
                <input type="text" name="name" id="profile_name" 
                       class="input-premium @error('name') border-danger @enderror" 
                       value="{{ old('name', auth()->user()->name) }}" required>
                @error('name')
                    <span class="text-danger fs-2xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="ds-form-group-horizontal flex-1">
                <label for="profile_email" class="form-label-premium">{{ __('auth.email_label') }}</label>
                <input type="email" name="email" id="profile_email" 
                       class="input-premium @error('email') border-danger @enderror" 
                       value="{{ old('email', auth()->user()->email) }}" required>
                @error('email')
                    <span class="text-danger fs-2xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="hr-white-5 my-2"></div>
        <p class="text-muted fs-xs font-600 uppercase tracking-wider mb-2">{{ __('auth.password_label') }} <span class="opacity-50 lowercase font-400">({{ __('auth.leave_password_blank') }})</span></p>

        <div class="d-flex gap-4 flex-column-mobile">
            <div class="ds-form-group-horizontal flex-1">
                <label for="profile_password" class="form-label-premium">{{ __('auth.password_label') }}</label>
                <input type="password" name="password" id="profile_password" 
                       class="input-premium @error('password') border-danger @enderror" 
                       placeholder="••••••••">
                @error('password')
                    <span class="text-danger fs-2xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="ds-form-group-horizontal flex-1">
                <label for="profile_password_confirmation" class="form-label-premium">{{ __('auth.confirm_password_label') }}</label>
                <input type="password" name="password_confirmation" id="profile_password_confirmation" 
                       class="input-premium" 
                       placeholder="••••••••">
            </div>
        </div>

        <div class="ds-modal-footer mt-4">
            <button type="button" data-ds-modal-close="profileModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">
                <i class="fas fa-save me-2"></i> {{ __('auth.save_changes') }}
            </button>
        </div>
    </form>
</x-modal>

{{-- Auto-open modal if there are profile validation errors --}}
@if($errors->any() && old('_method') === 'PUT')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof DS_UI !== 'undefined') {
                DS_UI.openModal('profileModal');
            }
        });
    </script>
@endif
