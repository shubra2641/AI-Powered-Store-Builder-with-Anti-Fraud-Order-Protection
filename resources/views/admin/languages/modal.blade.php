<!-- Add Modal -->
<x-modal id="addLanguageModal" title="{{ __('admin.add_language') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-language"></i>
        </div>
    </div>
    <form action="{{ route('admin.languages.store') }}" method="POST">
        @csrf
        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.name') }}</label>
                <input type="text" name="name" required class="input-premium" placeholder="e.g. English">
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.code') }}</label>
                <input type="text" name="code" required class="input-premium" placeholder="e.g. en">
            </div>
        </div>
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.direction') }}</label>
            <select name="direction" required class="input-premium no-appearance">
                <option value="ltr">{{ __('admin.ltr') }}</option>
                <option value="rtl">{{ __('admin.rtl') }}</option>
            </select>
        </div>
        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="addLanguageModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.confirm') }}</button>
        </div>
    </form>
</x-modal>

<!-- Edit Modal -->
<x-modal id="editLanguageModal" title="{{ __('admin.edit_language') }}">
    <div class="ds-modal-avatar-header">
        <div id="edit_avatar" class="ds-modal-avatar-circle">
            <i class="fas fa-language"></i>
        </div>
    </div>
    <form id="editLanguageForm" method="POST">
        @csrf
        @method('PUT')
        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.name') }}</label>
                <input type="text" name="name" id="edit_name" required class="input-premium">
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.code') }}</label>
                <input type="text" name="code" id="edit_code" required class="input-premium" onkeyup="DS_UI.updateEditAvatar(this.value)">
            </div>
        </div>
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.direction') }}</label>
            <select name="direction" id="edit_direction" required class="input-premium no-appearance">
                <option value="ltr">{{ __('admin.ltr') }}</option>
                <option value="rtl">{{ __('admin.rtl') }}</option>
            </select>
        </div>
        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="editLanguageModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save_changes') }}</button>
        </div>
    </form>
</x-modal>
