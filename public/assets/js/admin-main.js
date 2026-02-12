/**
 * DropSaaS Admin — Global UI Controller
 * Namespace: DS_UI (avoids polluting window global scope — Envato standard)
 * Handles: dropdowns, modals, confirmations, toasts, table interactions.
 */

window.DS_UI = window.DS_UI || {};

(function (DS) {
    'use strict';

    DS.loading = function (state) {
        var loader = document.getElementById('ds-global-loader');
        if (!loader) return;
        if (state) {
            loader.classList.remove('hidden');
            loader.classList.add('d-flex');
        } else {
            loader.classList.add('hidden');
            loader.classList.remove('d-flex');
        }
    };

    /**
     * Mobile Sidebar Toggle
     */
    DS.toggleSidebar = function () {
        document.body.classList.toggle('sidebar-open');
        var sidebar = document.querySelector('.sidebar');
        if (sidebar) {
            // Force paint to ensure transition works if needed
            // sidebar.style.display = 'flex'; 
        }
    };

    /**
     * Programmable Confirmation Modal (General Purpose)
     * @param {Object} options { title, message, icon, confirmText, cancelText, onConfirm }
     */
    DS.confirm = function (options) {
        var modal = document.getElementById('confirmationModal');
        if (!modal) return;

        if (options.title) {
            var titleEl = modal.querySelector('.ds-modal-title');
            if (titleEl) titleEl.textContent = options.title;
        }

        if (options.message) {
            var msgEl = modal.querySelector('#confirmMessage');
            if (msgEl) msgEl.textContent = options.message;
        }

        var confirmBtn = modal.querySelector('#confirmBtn');
        var cancelBtn = modal.querySelector('[data-ds-modal-close]');

        if (confirmBtn) {
            confirmBtn.textContent = options.confirmText || 'Confirm';
            var newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

            newConfirmBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (typeof options.onConfirm === 'function') {
                    options.onConfirm();
                }
                DS.closeModal('confirmationModal');
            });
        }

        if (cancelBtn) {
            if (options.cancelText === null) {
                cancelBtn.classList.add('hidden');
            } else {
                cancelBtn.classList.remove('hidden');
                cancelBtn.textContent = options.cancelText || 'Cancel';
            }
        }

        DS.openModal('confirmationModal');
    };

    document.addEventListener('DOMContentLoaded', function () {

        // =============================================================
        //  DROPDOWN SYSTEM
        // =============================================================
        DS.toggleDropdown = function (id) {
            var menu = document.getElementById(id);
            var allMenus = document.querySelectorAll('.dropdown-menu');

            allMenus.forEach(function (m) {
                if (m.id !== id) m.classList.remove('show');
            });
            if (menu) menu.classList.toggle('show');
        };

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(function (m) {
                    m.classList.remove('show');
                });
            }
        });

        // =============================================================
        //  MODAL SYSTEM
        // =============================================================
        DS.openModal = function (id) {
            var modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.add('d-flex');
            modal.classList.remove('hidden');
            setTimeout(function () {
                modal.classList.add('show');
            }, 10);
            document.body.classList.add('overflow-hidden');
        };

        DS.closeModal = function (id) {
            var modal = document.getElementById(id);
            if (!modal) return;

            modal.classList.remove('show');
            setTimeout(function () {
                modal.classList.add('hidden');
                modal.classList.remove('d-flex');
            }, 400);
            document.body.classList.remove('overflow-hidden');
        };

        // =============================================================
        //  TAB & LANGUAGE SWITCHERS
        // =============================================================
        DS.switchTab = function (tabId) {
            document.querySelectorAll('.tab-pane').forEach(function (pane) {
                pane.classList.add('hidden');
                pane.classList.remove('active');
            });
            document.querySelectorAll('.ds-tab-btn').forEach(function (btn) {
                btn.classList.remove('active');
            });

            var targetPane = document.getElementById(tabId + '-tab');
            if (targetPane) {
                targetPane.classList.remove('hidden');
                targetPane.classList.add('active');
            }

            var activeBtn = document.querySelector('[onclick="DS_UI.switchTab(\'' + tabId + '\')"]');
            if (activeBtn) activeBtn.classList.add('active');

            // Update URL if possible
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
                window.history.pushState({ path: newurl }, '', newurl);
            }
        };

        DS.switchLangEntry = function (group, langCode) {
            document.querySelectorAll('.lang-pane-' + group).forEach(function (pane) {
                pane.classList.add('hidden');
            });
            var targetPane = document.querySelector('.lang-pane-' + group + '[data-lang-pane="' + langCode + '"]');
            if (targetPane) targetPane.classList.remove('hidden');

            // Update tab buttons
            var btnContainer = targetPane ? targetPane.closest('.glass-card').querySelector('.ds-tabs-container') : null;
            if (btnContainer) {
                btnContainer.querySelectorAll('button').forEach(function (btn) {
                    btn.classList.remove('active');
                    var onclickAttr = btn.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.indexOf("'" + langCode + "'") !== -1) {
                        btn.classList.add('active');
                    }
                });
            }
        };

        // =============================================================
        //  USER MANAGEMENT HELPERS
        // =============================================================
        DS.updateUserAvatar = function (name) {
            var avatar = document.getElementById('edit_user_avatar');
            if (!avatar) return;
            if (name && name.length >= 1) {
                // Use UI Avatars logic or just first 2 chars
                avatar.textContent = name.substring(0, 2).toUpperCase();
            } else {
                avatar.innerHTML = '<i class="fas fa-user"></i>';
            }
        };

        /**
         * Edit User Modal Helper
         * Populates the Edit User modal with data.
         */
        DS.editUser = function (user, updateUrl) {
            var form = document.getElementById('editUserForm');
            if (!form) return;

            form.action = updateUrl;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_role_id').value = user.role_id;
            document.getElementById('edit_is_active').value = user.is_active ? 1 : 0;

            DS.updateUserAvatar(user.name);
            DS.openModal('editUserModal');
        };

        // =============================================================
        //  CONFIRMATION MODAL
        // =============================================================
        DS.confirmAction = function (url, message, method, btnClass) {
            method = method || 'POST';
            btnClass = btnClass || 'bg-danger';

            var form = document.getElementById('confirmForm');
            var submitBtn = document.getElementById('confirmBtn');
            var messageEl = document.getElementById('confirmMessage');

            if (!form || !submitBtn || !messageEl) return;

            form.action = url;
            messageEl.textContent = message;

            var premiumClass = 'btn-gradient';
            if (btnClass.indexOf('bg-success') !== -1) premiumClass = 'btn-confirm-success';
            if (btnClass.indexOf('bg-danger') !== -1) premiumClass = 'btn-confirm-danger';
            submitBtn.className = premiumClass;

            var placeholder = document.getElementById('confirmMethod');
            if (placeholder) {
                placeholder.innerHTML = '';
                if (method !== 'POST') {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_method';
                    input.value = method;
                    placeholder.appendChild(input);
                }
            }

            DS.openModal('confirmationModal');
        };

        // =============================================================
        //  LANGUAGE MANAGEMENT HELPERS
        // =============================================================
        DS.updateEditAvatar = function (code) {
            var avatar = document.getElementById('edit_avatar');
            if (!avatar) return;
            if (code && code.length >= 2) {
                avatar.textContent = code.substring(0, 2).toUpperCase();
            } else {
                avatar.innerHTML = '<i class="fas fa-language"></i>';
            }
        };

        DS.editLanguage = function (lang, actionUrl) {
            var form = document.getElementById('editLanguageForm');
            if (!form) return;

            form.action = actionUrl;
            document.getElementById('edit_name').value = lang.name;
            document.getElementById('edit_code').value = lang.code;
            document.getElementById('edit_direction').value = lang.direction;

            DS.updateEditAvatar(lang.code);
            DS.openModal('editLanguageModal');
        };

        // =============================================================
        //  TOAST NOTIFICATION SYSTEM (XSS-safe: uses textContent)
        // =============================================================
        DS.showToast = function (type, message) {
            var container = document.getElementById('toast-container');
            if (!container) return;

            var toast = document.createElement('div');
            toast.className = 'toast-premium toast-' + type;

            var iconMap = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
            var icon = iconMap[type] || 'fa-info-circle';
            var title = type.charAt(0).toUpperCase() + type.slice(1);

            // Build DOM safely (no innerHTML for user content)
            var iconDiv = document.createElement('div');
            iconDiv.className = 'toast-icon';
            iconDiv.innerHTML = '<i class="fas ' + icon + '"></i>';

            var contentDiv = document.createElement('div');
            contentDiv.className = 'toast-content';

            var titleSpan = document.createElement('span');
            titleSpan.className = 'toast-title';
            titleSpan.textContent = title;

            var msgSpan = document.createElement('span');
            msgSpan.className = 'toast-message';
            msgSpan.textContent = message; // XSS-safe

            contentDiv.appendChild(titleSpan);
            contentDiv.appendChild(msgSpan);

            var closeBtn = document.createElement('button');
            closeBtn.className = 'toast-close';
            closeBtn.innerHTML = '<i class="fas fa-times"></i>';
            closeBtn.addEventListener('click', function () {
                toast.classList.add('hide');
                setTimeout(function () { toast.remove(); }, 400);
            });

            toast.appendChild(iconDiv);
            toast.appendChild(contentDiv);
            toast.appendChild(closeBtn);
            container.appendChild(toast);

            setTimeout(function () {
                if (toast && toast.parentElement) {
                    toast.classList.add('hide');
                    setTimeout(function () { toast.remove(); }, 400);
                }
            }, 5000);
        };

        /**
         * Alias for showToast with argument swapping for module compatibility
         * @param {string} message 
         * @param {string} type 
         */
        DS.toast = function (message, type) {
            DS.showToast(type || 'info', message);
        };

        /**
         * Centralized Error Handler
         * Parses various error formats (Axios, Fetch, Text) and shows a toast.
         * @param {mixed} error 
         */
        DS.handleError = function (error) {
            console.error('DS Error:', error);
            let message = 'An unexpected error occurred.';

            if (error.response && error.response.data) {
                // Axios/Laravel JSON response
                message = error.response.data.message || error.response.data.error || message;
            } else if (error.message) {
                // JS Error object
                message = error.message;
            } else if (typeof error === 'string') {
                message = error;
            }

            DS.showToast('error', message);
        };

        // Auto-trigger toasts from data attributes
        var toastContainer = document.getElementById('toast-container');
        if (toastContainer) {
            var success = toastContainer.getAttribute('data-success');
            var error = toastContainer.getAttribute('data-error');
            var info = toastContainer.getAttribute('data-info');
            var errors = toastContainer.getAttribute('data-errors');

            if (success) DS.showToast('success', success);
            if (error) DS.showToast('error', error);
            if (info) DS.showToast('info', info);

            if (errors && errors.trim().length > 0) {
                try {
                    var errorList = JSON.parse(errors);
                    errorList.forEach(function (err) { DS.showToast('error', err); });
                } catch (e) {
                    console.error('DS_UI: Failed to parse validation errors', e);
                }
            }
        }

        // =============================================================
        //  TABLE SEARCH (data-ds-search="tableId")
        // =============================================================
        document.querySelectorAll('[data-ds-search]').forEach(function (input) {
            input.addEventListener('input', function () {
                var tableId = this.getAttribute('data-ds-search');
                var container = document.getElementById(tableId);
                if (!container) return;

                var query = this.value.toLowerCase().trim();
                var rows = container.querySelectorAll('tbody tr[data-searchable]');
                var visibleCount = 0;

                rows.forEach(function (row) {
                    var text = row.getAttribute('data-searchable') || '';
                    var match = !query || text.indexOf(query) !== -1;
                    if (match) {
                        row.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        row.classList.add('hidden');
                    }
                });

                var noResults = container.querySelector('.ds-no-results');
                if (noResults) {
                    if (visibleCount === 0) {
                        noResults.classList.remove('hidden');
                        noResults.classList.add('d-flex');
                    } else {
                        noResults.classList.add('hidden');
                        noResults.classList.remove('d-flex');
                    }
                }
            });
        });

        // =============================================================
        //  FILTER DROPDOWN (data-ds-filter="tableId" data-filter-col="col")
        // =============================================================
        document.querySelectorAll('[data-ds-filter]').forEach(function (dropdown) {
            var trigger = dropdown.querySelector('.filter-item');
            var menu = dropdown.querySelector('.ds-filter-menu');
            var label = dropdown.querySelector('.ds-filter-label');
            var tableId = dropdown.getAttribute('data-ds-filter');
            var colName = dropdown.getAttribute('data-filter-col');

            if (!trigger || !menu) return;

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                document.querySelectorAll('.ds-filter-menu.show').forEach(function (m) {
                    if (m !== menu) m.classList.remove('show');
                });
                menu.classList.toggle('show');
            });

            menu.querySelectorAll('.ds-filter-option').forEach(function (option) {
                option.addEventListener('click', function () {
                    var value = this.getAttribute('data-value');

                    menu.querySelectorAll('.ds-filter-option').forEach(function (o) {
                        o.classList.remove('active');
                    });
                    this.classList.add('active');
                    if (label) label.textContent = this.textContent.trim();

                    var container = document.getElementById(tableId);
                    if (!container) return;
                    var rows = container.querySelectorAll('tbody tr[data-filter-' + colName + ']');
                    var visibleCount = 0;

                    rows.forEach(function (row) {
                        var rowVal = row.getAttribute('data-filter-' + colName);
                        var match = value === 'all' || rowVal === value;
                        if (match) {
                            row.classList.remove('hidden');
                            visibleCount++;
                        } else {
                            row.classList.add('hidden');
                        }
                    });

                    var noResults = container.querySelector('.ds-no-results');
                    if (noResults) {
                        if (visibleCount === 0) {
                            noResults.classList.remove('hidden');
                            noResults.classList.add('d-flex');
                        } else {
                            noResults.classList.add('hidden');
                            noResults.classList.remove('d-flex');
                        }
                    }
                    menu.classList.remove('show');
                });
            });
        });

        // =============================================================
        //  SORT DROPDOWN (data-ds-sort="tableId" data-sort-col="col")
        // =============================================================
        document.querySelectorAll('[data-ds-sort]').forEach(function (dropdown) {
            var trigger = dropdown.querySelector('.filter-item');
            var menu = dropdown.querySelector('.ds-filter-menu');
            var tableId = dropdown.getAttribute('data-ds-sort');
            var colName = dropdown.getAttribute('data-sort-col');
            var sortIcon = dropdown.querySelector('.ds-sort-icon');

            if (!trigger || !menu) return;

            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                document.querySelectorAll('.ds-filter-menu.show').forEach(function (m) {
                    if (m !== menu) m.classList.remove('show');
                });
                menu.classList.toggle('show');
            });

            menu.querySelectorAll('.ds-sort-option').forEach(function (option) {
                option.addEventListener('click', function () {
                    var dir = this.getAttribute('data-dir');

                    menu.querySelectorAll('.ds-sort-option').forEach(function (o) {
                        o.classList.remove('active');
                    });
                    this.classList.add('active');

                    if (sortIcon) {
                        sortIcon.className = dir === 'asc'
                            ? 'fas fa-sort-alpha-down fs-xs ds-sort-icon'
                            : 'fas fa-sort-alpha-up fs-xs ds-sort-icon';
                    }

                    var container = document.getElementById(tableId);
                    if (!container) return;
                    var tbody = container.querySelector('tbody');
                    if (!tbody) return;
                    var rows = Array.from(tbody.querySelectorAll('tr[data-sort-' + colName + ']'));

                    rows.sort(function (a, b) {
                        var aVal = a.getAttribute('data-sort-' + colName) || '';
                        var bVal = b.getAttribute('data-sort-' + colName) || '';
                        var cmp = aVal.localeCompare(bVal);
                        return dir === 'asc' ? cmp : -cmp;
                    });

                    rows.forEach(function (row) { tbody.appendChild(row); });
                    menu.classList.remove('show');
                });
            });
        });

        // Close all filter menus on outside click
        // =============================================================
        //  GLOBAL TOGGLES & ACTIONS (Event Delegation)
        // =============================================================
        document.addEventListener('click', function (e) {
            // Sidebar Toggle
            var sidebarToggle = e.target.closest('[data-ds-toggle="sidebar"]');
            if (sidebarToggle) {
                DS_UI.toggleSidebar();
                return;
            }

            // Dropdown Toggles
            var toggleBtn = e.target.closest('[data-ds-toggle]');
            if (toggleBtn) {
                var targetId = toggleBtn.getAttribute('data-ds-toggle');
                DS_UI.toggleDropdown(targetId);
                return;
            }

            // Confirmation Actions
            var actionBtn = e.target.closest('[data-ds-confirm]');
            if (actionBtn) {
                var url = actionBtn.getAttribute('data-ds-confirm');
                var msg = actionBtn.getAttribute('data-ds-message') || 'Are you sure?';
                var method = actionBtn.getAttribute('data-ds-method') || 'POST';
                var btnClass = actionBtn.getAttribute('data-ds-btn-class') || 'bg-danger';
                DS_UI.confirmAction(url, msg, method, btnClass);
                return;
            }

            // Logout Handler
            if (e.target.closest('[data-ds-logout]')) {
                e.preventDefault();
                var form = document.getElementById('logout-form');
                if (form) form.submit();
                return;
            }

            // Modal Open Toggles
            var modalOpenBtn = e.target.closest('[data-ds-modal-open]');
            if (modalOpenBtn) {
                var modalId = modalOpenBtn.getAttribute('data-ds-modal-open');
                DS_UI.openModal(modalId);
                return;
            }

            // Modal Close Toggles
            var modalCloseBtn = e.target.closest('[data-ds-modal-close]');
            if (modalCloseBtn) {
                var modalId = modalCloseBtn.getAttribute('data-ds-modal-close');
                DS_UI.closeModal(modalId);
                return;
            }

            // Language Edit Handler

            // Language Edit Handler
            var editLangBtn = e.target.closest('.ds-edit-language');
            if (editLangBtn) {
                var langData = JSON.parse(editLangBtn.getAttribute('data-lang'));
                var updateUrl = editLangBtn.getAttribute('data-url');
                DS_UI.editLanguage(langData, updateUrl);
                return;
            }

            // User Edit Handler
            var editUserBtn = e.target.closest('.ds-edit-user');
            if (editUserBtn) {
                var userData = JSON.parse(editUserBtn.getAttribute('data-user'));
                var updateUrl = editUserBtn.getAttribute('data-url');
                DS_UI.editUser(userData, updateUrl);
                return;
            }
        });


        function updateBulkBar(tableId) {
            var container = document.getElementById(tableId);
            if (!container) return;

            var checks = container.querySelectorAll('[data-ds-row-check="' + tableId + '"]:checked');
            var bar = document.getElementById('bulkBar-' + tableId);
            var count = document.getElementById('bulkCount-' + tableId);

            if (bar) {
                if (checks.length > 0) {
                    bar.classList.remove('hidden');
                    bar.classList.add('d-flex');
                } else {
                    bar.classList.add('hidden');
                    bar.classList.remove('d-flex');
                }
            }
            if (count) count.textContent = checks.length;

            var selectAll = document.querySelector('[data-ds-select-all="' + tableId + '"]');
            var total = container.querySelectorAll('[data-ds-row-check="' + tableId + '"]');
            if (selectAll) {
                selectAll.checked = total.length > 0 && checks.length === total.length;
                selectAll.indeterminate = checks.length > 0 && checks.length < total.length;
            }
        }


        document.querySelectorAll('[data-ds-select-all]').forEach(function (selectAll) {
            selectAll.addEventListener('change', function () {
                var tableId = this.getAttribute('data-ds-select-all');
                var container = document.getElementById(tableId);
                if (!container) return;

                var checks = container.querySelectorAll('[data-ds-row-check="' + tableId + '"]');
                var isChecked = selectAll.checked;
                checks.forEach(function (cb) {
                    cb.checked = isChecked;
                    var row = cb.closest('tr');
                    if (row) row.classList.toggle('ds-row-selected', isChecked);
                });
                updateBulkBar(tableId);
            });
        });

        document.querySelectorAll('[data-ds-row-check]').forEach(function (cb) {
            cb.addEventListener('change', function () {
                var tableId = this.getAttribute('data-ds-row-check');
                var row = this.closest('tr');
                if (row) row.classList.toggle('ds-row-selected', this.checked);
                updateBulkBar(tableId);
            });
        });

        document.querySelectorAll('.ds-bulk-clear').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tableId = this.getAttribute('data-table');
                var container = document.getElementById(tableId);
                if (!container) return;

                container.querySelectorAll('[data-ds-row-check="' + tableId + '"]').forEach(function (cb) {
                    cb.checked = false;
                    var row = cb.closest('tr');
                    if (row) row.classList.remove('ds-row-selected');
                });

                var selectAll = document.querySelector('[data-ds-select-all="' + tableId + '"]');
                if (selectAll) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                }
                updateBulkBar(tableId);
            });
        });

        // =============================================================
        //  BULK DELETE ACTION (With Confirmation)
        // =============================================================
        document.querySelectorAll('[data-ds-bulk-action="delete"]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tableId = this.getAttribute('data-table');
                var bulkUrl = this.getAttribute('data-bulk-url');
                var ids = DS.getSelectedIds(tableId);

                if (ids.length === 0) return;
                if (!bulkUrl) return;

                // Open Confirmation Modal via DS.confirmAction
                // We use confirmAction to set up the basics, then append our IDs
                DS.confirmAction(
                    bulkUrl,
                    window.DS_TRANSLATIONS ? window.DS_TRANSLATIONS.confirm_delete : 'Are you sure you want to delete selected items?',
                    'POST',
                    'bg-danger'
                );

                // Add IDs to the confirmation form
                var confirmForm = document.getElementById('confirmForm');
                var placeholder = document.getElementById('confirmMethod');
                if (confirmForm && placeholder) {
                    ids.forEach(function (id) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids[]';
                        input.value = id;
                        placeholder.appendChild(input);
                    });
                }
            });
        });

        /**
         * Helper: Get selected IDs from a table
         */
        DS.getSelectedIds = function (tableId) {
            var container = document.getElementById(tableId);
            if (!container) return [];
            var checks = container.querySelectorAll('[data-ds-row-check="' + tableId + '"]:checked');
            return Array.from(checks).map(function (cb) { return cb.value; });
        };

        // =============================================================
        //  MODAL EVENT LISTENERS (Overlay Click & Escape Key)
        // =============================================================
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('ds-modal-overlay') && e.target.classList.contains('show')) {
                var modalId = e.target.id;
                if (modalId) DS.closeModal(modalId);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var openModals = document.querySelectorAll('.ds-modal-overlay.show');
                if (openModals.length > 0) {
                    var topModal = openModals[openModals.length - 1];
                    if (topModal.id) DS.closeModal(topModal.id);
                }
            }
        });
    }); // DOMContentLoaded

    /**
     * Standard Multilingual Tab Handler (Enhanced for Global Delegation)
     */
    DS_UI.switchLangEntry = function (group, langCode, target) {
        const container = (target ? target.closest('.tab-pane') : null) || (target ? target.closest('.glass-card') : null) || document;
        container.querySelectorAll('.lang-pane-' + group).forEach(el => el.classList.add('hidden'));

        const targetPane = container.querySelector('.lang-pane-' + group + '[data-lang-pane="' + langCode + '"]');
        if (targetPane) targetPane.classList.remove('hidden');

        // Update tab buttons state
        container.querySelectorAll('.lang-switch-btn').forEach(el => {
            if (el.dataset.group === group) el.classList.remove('active');
        });
        if (target) target.classList.add('active');
    };

    // Add global delegation for elements converted from inline to class-based
    document.addEventListener('click', function (e) {
        // Handle Lang Switcher
        const langBtn = e.target.closest('.lang-switch-btn');
        if (langBtn) {
            DS_UI.switchLangEntry(langBtn.dataset.group, langBtn.dataset.lang, langBtn);
        }

        // Tab Switcher (Settings etc.)
        const tabBtn = e.target.closest('.tab-switch-btn');
        if (tabBtn) {
            DS_UI.switchTab(tabBtn.dataset.tab, tabBtn);
        }

        // AI Key Test
        const testAiBtn = e.target.closest('.test-ai-key-btn');
        if (testAiBtn && window.DS_AI) {
            window.DS_AI.testKey(testAiBtn.dataset.url);
        }

        // Global Form Submission Loader
        const form = e.target.closest('form');
        if (form && !form.dataset.noLoader) {
            const submitBtn = e.target.closest('[type="submit"]') || e.target.closest('button:not([type="button"])');
            if (submitBtn) {
                // We show loader on actual submission
                form.addEventListener('submit', function () {
                    DS_UI.loading(true);
                }, { once: true });
            }
        }
    });

    /**
     * Updated switchTab for global delegation
     */
    DS_UI.switchTab = function (tabId, target) {
        document.querySelectorAll('.tab-pane').forEach(function (pane) {
            pane.classList.add('hidden');
            pane.classList.remove('active');
        });
        document.querySelectorAll('.ds-tab-btn').forEach(function (btn) {
            btn.classList.remove('active');
        });

        var targetPane = document.getElementById(tabId + '-tab');
        if (targetPane) {
            targetPane.classList.remove('hidden');
            targetPane.classList.add('active');
        }

        if (target) {
            target.classList.add('active');
        } else {
            var activeBtn = document.querySelector('.tab-switch-btn[data-tab="' + tabId + '"]');
            if (activeBtn) activeBtn.classList.add('active');
        }

        // Update URL if possible
        if (history.pushState) {
            var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + tabId;
            window.history.pushState({ path: newurl }, '', newurl);
        }
    };

})(window.DS_UI);
