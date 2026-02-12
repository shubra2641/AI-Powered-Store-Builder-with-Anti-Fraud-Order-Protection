
<x-modal id="addUserModal" title="{{ __('admin.add_user') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-user"></i>
        </div>
    </div>
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.name') }}</label>
                <input type="text" name="name" required class="input-premium" placeholder="John Doe">
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.email') }}</label>
                <input type="email" name="email" required class="input-premium" placeholder="john@example.com">
            </div>
        </div>

        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.role') }}</label>
                <select name="role_id" required class="input-premium no-appearance">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.status') }}</label>
                <select name="is_active" class="input-premium no-appearance">
                    <option value="1">{{ __('admin.active') }}</option>
                    <option value="0">{{ __('admin.inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.password') }}</label>
                <input type="password" name="password" required class="input-premium" minlength="8">
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.confirm_password') }}</label>
                <input type="password" name="password_confirmation" required class="input-premium" minlength="8">
            </div>
        </div>

        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="addUserModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save') }}</button>
        </div>
    </form>
</x-modal>


<x-modal id="editUserModal" title="{{ __('admin.edit_user') }}">
    <div class="ds-modal-avatar-header">
        <div id="edit_user_avatar" class="ds-modal-avatar-circle">
            <i class="fas fa-user"></i>
        </div>
    </div>
    <form id="editUserForm" action="" method="POST">
        @csrf
        @method('PUT')
        
        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.name') }}</label>
                <input type="text" name="name" id="edit_name" required class="input-premium" onkeyup="DS_UI.updateUserAvatar(this.value)">
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.email') }}</label>
                <input type="email" name="email" id="edit_email" required class="input-premium">
            </div>
        </div>

        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.role') }}</label>
                <select name="role_id" id="edit_role_id" required class="input-premium no-appearance">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.status') }}</label>
                <select name="is_active" id="edit_is_active" class="input-premium no-appearance">
                    <option value="1">{{ __('admin.active') }}</option>
                    <option value="0">{{ __('admin.inactive') }}</option>
                </select>
            </div>
        </div>

        <div class="alert alert-info py-2 px-3 fs-xs mb-3 text-white bg-primary-soft border-primary-soft border-radius-sm">
            <i class="fas fa-info-circle me-1"></i> {{ __('admin.leave_password_blank') }}
        </div>

        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.password') }}</label>
                <input type="password" name="password" class="input-premium" minlength="8">
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.confirm_password') }}</label>
                <input type="password" name="password_confirmation" class="input-premium" minlength="8">
            </div>
        </div>

        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="editUserModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save_changes') }}</button>
        </div>
    </form>
</x-modal>


<x-modal id="addCreditModal" title="{{ __('admin.add_credit') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-wallet"></i>
        </div>
        <h4 class="text-white mt-2 mb-0" id="credit_user_name"></h4>
    </div>
    <form id="addCreditForm" action="" method="POST">
        @csrf
        
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.amount') }}</label>
            <input type="number" name="amount" min="1" required class="input-premium" placeholder="0">
        </div>

        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.description') }}</label>
            <input type="text" name="description" class="input-premium" placeholder="{{ __('admin.optional') }}">
        </div>

        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="addCreditModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save') }}</button>
        </div>
    </form>
</x-modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Add Credit Modal
    document.querySelectorAll('.ds-add-credit').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const form = document.getElementById('addCreditForm');
            const nameEl = document.getElementById('credit_user_name');
            
            form.action = `/admin/users/${userId}/add-credit`;
            nameEl.textContent = userName;
            
            DS_UI.openModal('addCreditModal');
        });
    });
});
</script>