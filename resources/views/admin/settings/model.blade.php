
<x-modal id="addAIKeyModal" title="{{ __('admin.add_api_key') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-robot"></i>
        </div>
    </div>
    <form action="{{ route('admin.ai-keys.store') }}" method="POST">
        @csrf
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.ai_provider') }}</label>
            <select name="provider" required class="input-premium no-appearance" onchange="DS_AI.showProviderInstructions(this.value, 'add')">
                <option value="gemini">Gemini</option>
                <option value="chatgpt">ChatGPT</option>
                <option value="groq">Groq</option>
                <option value="claude">Claude</option>
                <option value="perplexity">Perplexity</option>
            </select>
        </div>
        <div id="provider_instructions_add" class="bg-primary-soft p-3 border-radius-sm mb-4 fs-xs text-muted border-dashed border-primary-soft hidden">
                    </div>
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.api_key') }}</label>
            <input type="password" name="api_key" required class="input-premium" placeholder="sk-...">
        </div>
        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-2">
                <label class="form-label-premium">{{ __('admin.model') }}</label>
                <select name="model" id="add_ai_model" required class="input-premium no-appearance">
                                    </select>
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.max_tokens') }}</label>
                <input type="number" name="max_tokens" value="2000" required class="input-premium">
            </div>
        </div>
        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="addAIKeyModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.confirm') }}</button>
        </div>
    </form>
</x-modal>


<x-modal id="editAIKeyModal" title="{{ __('admin.edit_api_key') }}">
    <div class="ds-modal-avatar-header">
        <div class="ds-modal-avatar-circle">
            <i class="fas fa-edit"></i>
        </div>
    </div>
    <form id="editAIKeyForm" method="POST">
        @csrf
        @method('PUT')
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.ai_provider') }}</label>
            <select name="provider" id="edit_ai_provider" required class="input-premium no-appearance" onchange="DS_AI.showProviderInstructions(this.value, 'edit')">
                <option value="gemini">Gemini</option>
                <option value="chatgpt">ChatGPT</option>
                <option value="groq">Groq</option>
                <option value="claude">Claude</option>
                <option value="perplexity">Perplexity</option>
            </select>
        </div>
        <div id="provider_instructions_edit" class="bg-primary-soft p-3 border-radius-sm mb-4 fs-xs text-muted border-dashed border-primary-soft hidden">
                    </div>
        <div class="ds-form-group-horizontal">
            <label class="form-label-premium">{{ __('admin.api_key') }}</label>
            <input type="password" name="api_key" id="edit_ai_api_key" required class="input-premium">
        </div>
        <div class="d-flex gap-4">
            <div class="ds-form-group-horizontal flex-2">
                <label class="form-label-premium">{{ __('admin.model') }}</label>
                <select name="model" id="edit_ai_model" required class="input-premium no-appearance">
                                    </select>
            </div>
            <div class="ds-form-group-horizontal flex-1">
                <label class="form-label-premium">{{ __('admin.max_tokens') }}</label>
                <input type="number" name="max_tokens" id="edit_ai_max_tokens" required class="input-premium">
            </div>
        </div>
        <div class="ds-modal-footer">
            <button type="button" data-ds-modal-close="editAIKeyModal" class="btn-dark">{{ __('admin.cancel') }}</button>
            <button type="submit" class="btn-gradient">{{ __('admin.save_changes') }}</button>
        </div>
    </form>
</x-modal>

@push('scripts')
<script>

    DS_UI.switchTab = function(tab) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.getElementById(tab + '-tab').classList.remove('hidden');
        document.getElementById(tab + '-tab').classList.add('active');

        document.querySelectorAll('.ds-tab-btn').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');

        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        window.history.pushState({}, '', url);
    };


    window.DS_AI = {
        instructions: {
            'gemini': "{{ __('admin.gemini_instructions') }}",
            'chatgpt': "{{ __('admin.chatgpt_instructions') }}",
            'groq': "{{ __('admin.groq_instructions') }}",
            'claude': "{{ __('admin.claude_instructions') }}",
            'perplexity': "{{ __('admin.perplexity_instructions') }}"
        },
        
        models: @json($aiModels),

        showProviderInstructions: function(provider, mode) {
            const container = document.getElementById('provider_instructions_' + mode);
            const modelSelect = document.getElementById(mode + '_ai_model');
            
            // Show instructions
            if (this.instructions[provider]) {
                container.innerHTML = '<i class="fas fa-info-circle me-1"></i> ' + this.instructions[provider];
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }

            // Update models dropdown
            if (modelSelect) {
                modelSelect.innerHTML = '';
                const providerModels = this.models[provider] || [];
                providerModels.forEach(model => {
                    const option = document.createElement('option');
                    option.value = model;
                    option.textContent = model;
                    modelSelect.appendChild(option);
                });
            }
        },

        testKey: function(url) {
            DS_UI.loading(true);
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                DS_UI.loading(false);
                if (data.success) {
                    DS_UI.showToast('success', "{{ __('admin.api_connected_successfully') }}");
                    DS_UI.confirm({
                        title: "{{ __('admin.success') }}",
                        message: data.message + (data.response ? "\n\nResponse: " + data.response : ""),
                        icon: 'fas fa-check-circle',
                        confirmText: 'OK',
                        cancelText: null
                    });
                } else {
                    DS_UI.showToast('error', "{{ __('admin.connection_failed') }}");
                    DS_UI.confirm({
                        title: "{{ __('admin.error') }}",
                        message: data.message || "{{ __('admin.connection_failed') }}",
                        icon: 'fas fa-exclamation-circle',
                        confirmText: 'OK',
                        cancelText: null
                    });
                }
            })
            .catch(err => {
                DS_UI.loading(false);
                DS_UI.showToast('error', "{{ __('admin.connection_failed') }}");
            });
        }
    };


    document.querySelectorAll('.ds-edit-ai-key').forEach(btn => {
        btn.onclick = function() {
            const keyData = JSON.parse(btn.dataset.key);
            const url = btn.dataset.url;
            
            const form = document.getElementById('editAIKeyForm');
            form.action = url;
            
            document.getElementById('edit_ai_provider').value = keyData.provider;
            document.getElementById('edit_ai_api_key').value = keyData.api_key;
            
            // Populate models first, then set value
            DS_AI.showProviderInstructions(keyData.provider, 'edit');
            document.getElementById('edit_ai_model').value = keyData.model;
            
            document.getElementById('edit_ai_max_tokens').value = keyData.max_tokens;
            
            DS_UI.openModal('editAIKeyModal');
        };
    });


    document.addEventListener('DOMContentLoaded', () => {
        DS_AI.showProviderInstructions('gemini', 'add');
    });


    DS_UI.switchLangEntry = function(group, langCode) {
        const container = event.currentTarget.closest('.glass-card');
        if (!container) return;

        // Hide all panes in this container
        container.querySelectorAll('.lang-pane-' + group).forEach(el => el.classList.add('hidden'));
        
        // Show specific lang pane
        const targetPane = container.querySelector('[data-lang-pane="' + langCode + '"]');
        if (targetPane) targetPane.classList.remove('hidden');

        // Update active button state
        container.querySelectorAll('.ds-tab-btn-xs').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');
    };
</script>
@endpush
