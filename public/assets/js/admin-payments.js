/**
 * DropSaaS Payment Gateway Management
 * Handles dynamic credential fields and simplified environment settings.
 */
(function (DS) {
    'use strict';

    const gatewaySchemas = {
        stripe: [
            { key: 'public_key', type: 'input' },
            { key: 'secret_key', type: 'input' },
            { key: 'webhook_secret', type: 'input' }
        ],
        paypal: [
            { key: 'client_id', type: 'input' },
            { key: 'client_secret', type: 'input' },
            { key: 'webhook_id', type: 'input' }
        ],
        razorpay: [
            { key: 'key_id', type: 'input' },
            { key: 'key_secret', type: 'input' },
            { key: 'webhook_secret', type: 'input' }
        ],
        bank_transfer: [
            { key: 'details', type: 'textarea' },
            { key: 'require_proof', type: 'select', options: { '1': 'Required', '0': 'Optional' } }
        ]
    };

    /**
     * Helper to create a credential input field.
     */
    function createCredentialField(field, value = '') {
        const wrapper = document.createElement('div');
        wrapper.className = 'ds-form-group';

        const label = document.createElement('label');
        label.className = 'form-label-premium mb-2 block';
        // Format key to label: 'public_key' -> 'Public Key'
        label.textContent = field.key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');

        let input;

        if (field.type === 'select') {
            input = document.createElement('select');
            input.className = 'input-premium no-appearance';
            for (const [optVal, optLabel] of Object.entries(field.options)) {
                const option = document.createElement('option');
                option.value = optVal;
                option.textContent = optLabel;
                if (String(value) === String(optVal)) option.selected = true;
                input.appendChild(option);
            }
        } else if (field.type === 'textarea') {
            input = document.createElement('textarea');
            input.className = 'input-premium';
            input.rows = 3;
            input.value = value;
        } else {
            input = document.createElement('input');
            input.type = 'text';
            input.className = 'input-premium';
            input.value = value;
        }

        input.name = `credentials[${field.key}]`;
        input.placeholder = label.textContent;

        wrapper.appendChild(label);
        wrapper.appendChild(input);

        return wrapper;
    }

    /**
     * Handles provider selection in Add Gateway modal.
     */
    function handleGatewayChange(slug, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const section = container.closest('.border-top');
        const envSection = document.getElementById('add_environment_section');

        container.innerHTML = '';

        if (slug && gatewaySchemas[slug]) {
            if (section) section.classList.remove('hidden');

            if (nameInput && !nameInput.value) {
                nameInput.value = slug.charAt(0).toUpperCase() + slug.slice(1).replace('_', ' ');
            }

            // Hide Environment for Bank Transfer
            if (slug === 'bank_transfer') {
                if (envSection) envSection.classList.add('hidden');
            } else {
                if (envSection) envSection.classList.remove('hidden');
            }

            gatewaySchemas[slug].forEach(field => {
                const element = createCredentialField(field, '');
                container.appendChild(element);
            });
        } else if (section) {
            section.classList.add('hidden');
        }
    }

    /**
     * Populates and opens the Edit Gateway modal.
     */
    function editGateway(id) {
        DS.loading(true);

        fetch(`/admin/payments/${id}/data`)
            .then(response => response.json())
            .then(data => {
                const form = document.getElementById('editGatewayForm');
                form.action = `/admin/payments/${id}`;

                document.getElementById('edit_gateway_name_field').value = data.name;

                const envValue = (data.mode === 'sandbox' && data.is_test_mode) ? 'sandbox_test' : 'live_prod';
                document.getElementById('edit_environment').value = envValue;

                document.getElementById('edit_is_active').value = data.is_active ? 1 : 0;

                const envSection = document.getElementById('edit_environment_section');
                if (data.slug === 'bank_transfer') {
                    if (envSection) envSection.classList.add('hidden');
                } else {
                    if (envSection) envSection.classList.remove('hidden');
                }

                const container = document.getElementById('editCredentialsContainer');
                container.innerHTML = '';

                if (data.credentials && gatewaySchemas[data.slug]) {
                    gatewaySchemas[data.slug].forEach(field => {
                        const val = data.credentials[field.key] || '';
                        container.appendChild(createCredentialField(field, val));
                    });
                }

                const avatarIcon = document.querySelector('#edit_gateway_avatar i');
                if (avatarIcon) {
                    let iconClass = 'fa-receipt';
                    if (data.slug === 'stripe') iconClass = 'fa-credit-card';
                    else if (data.slug === 'paypal') iconClass = 'fa-brands fa-paypal';
                    else if (data.slug === 'bank_transfer') iconClass = 'fa-building-columns';
                    avatarIcon.className = 'fas ' + iconClass;
                }

                DS.loading(false);
                DS.openModal('editGatewayModal');
            })
            .catch(error => {
                DS.loading(false);
                DS.handleError(error);
            });
    }

    // Initialize listeners
    document.addEventListener('DOMContentLoaded', () => {
        // Edit button
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.edit-gateway-btn');
            if (btn) {
                editGateway(btn.dataset.id);
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.classList.contains('gateway-select-handler')) {
                handleGatewayChange(e.target.value, e.target.dataset.container);
            }
        });
    });

    // Expose globally for Integrations page
    window.DS_Payments = window.DS_Payments || {};
    window.DS_Payments.handleGatewayChange = handleGatewayChange;
    window.DS_Payments.editGateway = editGateway;

})(window.DS_UI);
