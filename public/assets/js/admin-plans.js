/**
 * DropSaaS — Plan Management JS
 * Handles plan editing and AJAX status toggles.
 */

window.DS_Plans = window.DS_Plans || {
    editPlan: function (plan) {
        var form = document.getElementById('editPlanForm');
        if (!form) return;

        form.action = '/admin/plans/' + plan.id;

        // Unpack multilingual name
        if (plan.name) {
            Object.keys(plan.name).forEach(function (lang) {
                var input = document.getElementById('edit_name_' + lang);
                if (input) input.value = plan.name[lang];
            });
        }

        // Unpack multilingual description
        if (plan.description) {
            Object.keys(plan.description).forEach(function (lang) {
                var input = document.getElementById('edit_desc_' + lang);
                if (input) input.value = plan.description[lang];
            });
        }

        if (document.getElementById('edit_price')) {
            document.getElementById('edit_price').value = plan.dollar_price || (plan.price / 100);
        }
        if (document.getElementById('edit_duration_days')) {
            document.getElementById('edit_duration_days').value = plan.duration_days;
        }
        if (document.getElementById('edit_trial_days')) {
            document.getElementById('edit_trial_days').value = plan.trial_days;
        }
        if (document.getElementById('edit_is_featured')) {
            document.getElementById('edit_is_featured').checked = !!plan.is_featured;
        }
        if (document.getElementById('edit_is_default')) {
            document.getElementById('edit_is_default').checked = !!plan.is_default;
        }

        // Quotas & Features
        if (plan.quotas) {
            // Reset all feature toggles and gateway checkboxes first
            document.querySelectorAll('.edit-feature-toggle, .edit-gateway-checkbox').forEach(function (el) {
                el.checked = false;
            });

            Object.keys(plan.quotas).forEach(function (key) {
                var value = plan.quotas[key];

                if (key === 'payment_gateways' && Array.isArray(value)) {
                    value.forEach(function (slug) {
                        var checkbox = document.getElementById('edit_gateway_' + slug);
                        if (checkbox) checkbox.checked = true;
                    });
                } else {
                    var input = document.getElementById('edit_quota_' + key);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = !!value;
                        } else {
                            input.value = value;
                        }
                    }
                }
            });
        }

        if (window.DS_UI && window.DS_UI.openModal) {
            window.DS_UI.openModal('editPlanModal');
        }
    },

    toggleStatus: function (route, isChecked) {
        if (window.DS_UI && window.DS_UI.loading) {
            window.DS_UI.loading(true);
        }

        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        var token = csrfToken ? csrfToken.getAttribute('content') : '';

        fetch(route, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (data.success) {
                    if (window.DS_UI && window.DS_UI.loading) window.DS_UI.loading(false);
                    if (window.DS_UI && window.DS_UI.showToast) {
                        window.DS_UI.showToast('success', data.message);
                    }
                } else {
                    if (window.DS_UI && window.DS_UI.loading) window.DS_UI.loading(false);
                    alert(data.message || 'Error updating status');
                    // Revert checkbox state on error
                    var checkbox = document.querySelector('.plan-status-toggle[data-route="' + route + '"]');
                    if (checkbox) checkbox.checked = !isChecked;
                }
            })
            .catch(function (error) {
                console.error('Error:', error);
                if (window.DS_UI && window.DS_UI.loading) window.DS_UI.loading(false);
                // Revert check on error
                var checkbox = document.querySelector('.plan-status-toggle[data-route="' + route + '"]');
                if (checkbox) checkbox.checked = !isChecked;
            });
    }
};

/**
 * Initialize event listeners for declarative triggers.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Plan Status Toggle
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('plan-status-toggle')) {
            var route = e.target.getAttribute('data-route');
            var isChecked = e.target.checked;
            if (window.DS_Plans) window.DS_Plans.toggleStatus(route, isChecked);
        }
    });

    // Edit Plan Button
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.edit-plan-btn');
        if (btn) {
            var planData = JSON.parse(btn.getAttribute('data-plan'));
            if (window.DS_Plans) window.DS_Plans.editPlan(planData);
        }
    });

    // Set Plan as Default
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.set-default-btn');
        if (btn) {
            var planId = btn.getAttribute('data-plan-id');
            var baseUrl = window.APP_URL || '';

            // Show loading state
            if (window.DS_UI && window.DS_UI.loading) window.DS_UI.loading(true);

            fetch(baseUrl + '/admin/plans/' + planId + '/default', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (window.DS_UI && window.DS_UI.loading) window.DS_UI.loading(false);
                    if (data.success) {
                        if (window.DS_UI && window.DS_UI.showToast) {
                            window.DS_UI.showToast('success', data.message);
                        } else {
                            alert(data.message);
                        }
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        if (window.DS_UI && window.DS_UI.showToast) {
                            window.DS_UI.showToast('error', data.message || 'Error occurred');
                        } else {
                            alert(data.message || 'Error occurred');
                        }
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    if (window.DS_UI && window.DS_UI.loading) window.DS_UI.loading(false);
                    if (window.DS_UI && window.DS_UI.showToast) {
                        window.DS_UI.showToast('error', 'An unexpected error occurred');
                    } else {
                        alert('An unexpected error occurred');
                    }
                });
        }
    });
});
