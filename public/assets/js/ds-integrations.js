/* ds-integrations.js */

window.DS_Integrations = window.DS_Integrations || {};

(function (DS) {
    'use strict';

    // Shared configuration variables
    let toggleRoute, csrfToken, statusUpdatedMsg, statusFailedMsg, errorOccurredMsg, settingsText;

    DS.toggle = async function (service, element) {
        if (window.DS_UI) DS_UI.loading(true);
        const isActive = element.classList.contains('active');
        const newStatus = !isActive;

        // Optimistic UI update
        element.classList.toggle('active');

        try {
            const response = await fetch(toggleRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    service: service,
                    status: newStatus
                })
            });

            const data = await response.json();

            if (!data.success) {
                // Revert if failed
                element.classList.toggle('active');
                if (window.DS_UI && window.DS_UI.toast) {
                    DS_UI.toast(statusFailedMsg, 'error');
                }
            } else {
                if (window.DS_UI && window.DS_UI.toast) {
                    DS_UI.toast(statusUpdatedMsg, 'success');
                }
            }

        } catch (error) {
            console.error('Error:', error);
            // Revert
            element.classList.toggle('active');
            if (window.DS_UI && window.DS_UI.toast) {
                DS_UI.toast(errorOccurredMsg, 'error');
            }
        } finally {
            if (window.DS_UI) DS_UI.loading(false);
        }
    };

    DS.openSettings = function (service, name) {
        const title = document.getElementById('modalTitle');
        const input = document.getElementById('modalServiceInput');
        const fieldsContainer = document.getElementById('modalFields');

        if (title) title.textContent = name + ' ' + settingsText;
        if (input) input.value = service;
        if (fieldsContainer) fieldsContainer.innerHTML = ''; // Clear previous fields

        // Define fields based on service from backend
        const fields = window.currentIntegrations[service].fields || {};

        // Loop through fields object
        for (const [key, fieldConfig] of Object.entries(fields)) {
            const group = document.createElement('div');
            group.className = 'ds-form-group';

            // Checkbox Special Case
            if (fieldConfig.type === 'checkbox') {
                group.className = 'ds-form-group flex items-center gap-3';

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `settings[${key}]`;
                hiddenInput.value = '0';
                group.appendChild(hiddenInput);

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = `settings[${key}]`;
                checkbox.value = '1';
                checkbox.className = 'form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300';

                if (window.currentIntegrations[service].settings && window.currentIntegrations[service].settings[key]) {
                    const val = window.currentIntegrations[service].settings[key];
                    checkbox.checked = (val == 1 || val == '1' || val == true || val == 'true');
                }
                group.appendChild(checkbox);

                const label = document.createElement('label');
                label.className = 'form-label-premium mb-0 cursor-pointer';
                label.textContent = fieldConfig.label;
                label.onclick = function () { checkbox.click(); };
                group.appendChild(label);

            } else {
                // Standard Label
                const label = document.createElement('label');
                label.className = 'form-label-premium mb-2 block';
                label.textContent = fieldConfig.label;
                group.appendChild(label);

                let fieldInput;

                if (fieldConfig.type === 'textarea') {
                    fieldInput = document.createElement('textarea');
                    fieldInput.rows = 4;
                    fieldInput.className = 'input-premium w-full';
                } else if (fieldConfig.type === 'select') {
                    fieldInput = document.createElement('select');
                    fieldInput.className = 'input-premium w-full';
                    if (fieldConfig.options) {
                        for (const [optVal, optLabel] of Object.entries(fieldConfig.options)) {
                            const option = document.createElement('option');
                            option.value = optVal;
                            option.textContent = optLabel;
                            fieldInput.appendChild(option);
                        }
                    }
                } else {
                    fieldInput = document.createElement('input');
                    fieldInput.type = fieldConfig.type || 'text';
                    fieldInput.className = 'input-premium w-full';
                }

                fieldInput.name = `settings[${key}]`;
                fieldInput.placeholder = fieldConfig.placeholder || '';

                if (window.currentIntegrations[service].settings && window.currentIntegrations[service].settings.hasOwnProperty(key)) {
                    fieldInput.value = window.currentIntegrations[service].settings[key];
                }

                group.appendChild(fieldInput);
            }

            fieldsContainer.appendChild(group);
        }

        // Use global DS_UI to open modal
        if (window.DS_UI && window.DS_UI.openModal) {
            DS_UI.openModal('settingsModal');
        } else {
            document.getElementById('settingsModal').classList.add('show');
        }
    };


    // Initialize Event Listeners when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        const configEl = document.getElementById('integrations-config');
        if (!configEl) return;

        toggleRoute = configEl.dataset.toggleRoute;
        csrfToken = configEl.dataset.csrfToken;
        statusUpdatedMsg = configEl.dataset.statusUpdatedMsg;
        statusFailedMsg = configEl.dataset.statusFailedMsg;
        errorOccurredMsg = configEl.dataset.errorOccurredMsg;
        settingsText = configEl.dataset.settingsText;

        // Make available globally if needed by other scripts, or use locally
        window.currentIntegrations = JSON.parse(configEl.dataset.currentIntegrations || '{}');

        // Auto-submit filter forms
        document.querySelectorAll('.js-auto-submit').forEach(el => {
            el.addEventListener('change', function () {
                this.form.submit();
            });
        });

        // Helper to open Add Payment Modal
        window.openAddPaymentModal = function (slug) {
            const select = document.getElementById('add_gateway_slug');
            if (select) {
                select.value = slug;
                const event = new Event('change', { bubbles: true });
                select.dispatchEvent(event);
            }
            if (window.DS_UI) DS_UI.openModal('addGatewayModal');
        };

        // Settings Form Loading
        const settingsForm = document.getElementById('settingsForm');
        if (settingsForm) {
            settingsForm.addEventListener('submit', function () {
                if (window.DS_UI) DS_UI.loading(true);
            });
        }

        // Global Click Delegation
        document.body.addEventListener('click', function (e) {

            // Toggle Integration
            const toggleIntegration = e.target.closest('.js-toggle-integration');
            if (toggleIntegration) {
                const service = toggleIntegration.dataset.service;
                DS.toggle(service, toggleIntegration);
                return;
            }

            // Toggle Payment
            const togglePayment = e.target.closest('.js-toggle-payment');
            if (togglePayment) {
                const formId = togglePayment.dataset.formId;
                const form = document.getElementById(formId);
                if (form) {
                    if (window.DS_UI) DS_UI.loading(true);
                    form.submit();
                }
                return;
            }

            // Edit Payment
            const editPayment = e.target.closest('.js-edit-payment');
            if (editPayment) {
                const id = editPayment.dataset.id;
                if (window.DS_Payments && window.DS_Payments.editGateway) {
                    DS_Payments.editGateway(id);
                }
                return;
            }

            // Add Payment
            const addPayment = e.target.closest('.js-add-payment');
            if (addPayment) {
                const slug = addPayment.dataset.slug;
                if (typeof openAddPaymentModal === 'function') {
                    openAddPaymentModal(slug);
                }
                return;
            }

            // Configure Integration
            const configIntegration = e.target.closest('.js-config-integration');
            if (configIntegration) {
                const service = configIntegration.dataset.service;
                const name = configIntegration.dataset.name;
                DS.openSettings(service, name);
                return;
            }

            // Close Modal
            const closeModal = e.target.closest('.js-close-modal');
            if (closeModal) {
                const modalId = closeModal.dataset.modal;
                if (window.DS_UI && window.DS_UI.closeModal) {
                    DS_UI.closeModal(modalId);
                }
                return;
            }
        });
    });

})(window.DS_Integrations);
