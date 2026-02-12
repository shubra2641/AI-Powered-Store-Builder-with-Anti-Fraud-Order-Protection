const { createApp, ref, computed, watch, onMounted } = Vue;

const app = createApp({
    setup() {
        let config = window.builderConfig || {};
        const configEl = document.getElementById('builder-config');
        if (configEl && configEl.dataset.config) {
            try {
                config = JSON.parse(configEl.dataset.config);
            } catch (e) {
                console.error('Failed to parse builder config', e);
            }
        }
        const initialContent = config.initialContent || [];
        const activeSections = ref((Array.isArray(initialContent) ? initialContent : []).map(s => {
            let content = s.content || {};
            let style = s.style || { background: '#0f172a', color: '#ffffff', padding: 80 };

            if (content && typeof content === 'object' && content.content) {
                style = Object.assign({}, style, content.style || {});
                content = content.content;
            }

            return {
                ...s,
                id: s.id || (s.attributes ? s.attributes.id : 'sec-' + Math.random().toString(36).substr(2, 9)),
                style: style,
                content: content,
            };
        }));

        const lastSavedState = ref(JSON.stringify(activeSections.value));

        const activeIndex = ref(null);
        const activeTab = ref('content');
        const previewSize = ref('desktop');
        const zoomLevel = ref(1.0);
        const components = ref(config.components || []);
        const isSaving = ref(false);
        const lastSaved = ref(null);
        const showLibraryModal = ref(false);
        const selectedCategory = ref(null);
        const advancedStyles = ref({}); // Tracks per-key advanced state

        const elitePresets = [
            { name: 'Elite Deep', value: 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)' },
            { name: 'Royal Purple', value: 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)' },
            { name: 'Crystal Ocean', value: 'linear-gradient(135deg, #0ea5e9 0%, #2dd4bf 100%)' },
            { name: 'Sunset Glow', value: 'linear-gradient(135deg, #f43f5e 0%, #fb923c 100%)' },
            { name: 'Midnight Green', value: 'linear-gradient(135deg, #064e3b 0%, #059669 100%)' },
            { name: 'Premium Dark', value: '#0f172a' },
            { name: 'Pure White', value: '#ffffff' },
            { name: 'Soft Gray', value: '#f8fafc' }
        ];

        // Icon Picker State
        const showIconModal = ref(false);
        const iconSearch = ref('');
        const currentIconKey = ref(null);
        const currentIconIndex = ref(null);
        const currentIconSubKey = ref(null);

        // Use injected icons from Helper
        const iconLibrary = config.icons || [];

        const filteredIcons = computed(() => {
            if (!iconSearch.value) return iconLibrary;
            const term = iconSearch.value.toLowerCase();
            return iconLibrary.filter(icon => icon.toLowerCase().includes(term));
        });

        const currentIconValue = computed(() => {
            if (activeSection.value && currentIconKey.value) {
                if (currentIconIndex.value !== null && currentIconSubKey.value) {
                    const content = activeSection.value.content[currentIconKey.value];
                    if (content && content[currentIconIndex.value]) {
                        return content[currentIconIndex.value][currentIconSubKey.value];
                    }
                } else {
                    return activeSection.value.content[currentIconKey.value];
                }
            }
            return '';
        });

        const activeSidebar = ref(null); // 'left', 'right', or null
        const isNavigatingAway = ref(false);

        // Dynamic translations for JS use
        const translations = config.translations || {};

        // Watch for preview size changes and adjust zoom (Scale to fit if needed, but 1.0 is default for best fidelity)
        watch(previewSize, (newVal) => {
            zoomLevel.value = 1.0;
        });

        const activeSection = computed(() => activeIndex.value !== null ? activeSections.value[activeIndex.value] : null);

        const categorizedComponents = computed(() => {
            const order = ['header', 'hero', 'features', 'stats', 'pricing', 'cta', 'footer'];
            const groups = {};
            components.value.forEach(c => {
                const cat = (c.category || 'Other').toLowerCase();
                if (!groups[cat]) groups[cat] = [];
                groups[cat].push(c);
            });

            const sorted = {};
            order.forEach(o => {
                if (groups[o]) sorted[o] = groups[o];
            });

            Object.keys(groups).forEach(g => {
                if (!order.includes(g)) sorted[g] = groups[g];
            });

            return sorted;
        });

        // Computed properties
        const hasUnsavedChanges = computed(() => {
            return JSON.stringify(activeSections.value) !== lastSavedState.value;
        });

        // Methods
        const adjustZoom = (delta) => {
            zoomLevel.value = Math.max(0.1, Math.min(2, zoomLevel.value + delta));
        };

        const selectSection = (idx) => {
            activeIndex.value = idx;
            // Ensure right sidebar is visible when selecting a section
            if (window.innerWidth < 1200) {
                activeSidebar.value = 'right';
            }
        };

        const refreshPreview = () => {
            const iframe = document.getElementById('preview-frame');
            if (iframe && iframe.contentWindow) {
                if (window.DS_UI) {
                    setTimeout(() => {
                        DS_UI.loading(false);
                    }, 5000);
                }

                iframe.contentWindow.postMessage({
                    type: 'UPDATE_CONTENT',
                    sections: JSON.parse(JSON.stringify(activeSections.value))
                }, '*');
            }
        };

        const savePage = async () => {
            if (window.DS_UI) DS_UI.loading(true);
            isSaving.value = true;
            try {
                const response = await fetch(config.saveRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken
                    },
                    body: JSON.stringify({
                        builder_content: activeSections.value
                    })
                });

                if (response.ok) {
                    lastSaved.value = new Date().toLocaleTimeString();
                    lastSavedState.value = JSON.stringify(activeSections.value);
                    if (window.DS_UI && window.DS_UI.showToast) {
                        DS_UI.showToast('success', translations.saved_successfully);
                    }
                } else {
                    throw new Error(translations.error);
                }
            } catch (error) {
                if (window.DS_UI && window.DS_UI.showToast) {
                    DS_UI.showToast('error', translations.error);
                }
            } finally {
                isSaving.value = false;
                if (window.DS_UI) DS_UI.loading(false);
            }
        };

        const exportPage = () => {
            if (window.DS_UI) DS_UI.loading(true);
            window.location.href = config.exportRoute;
            // For downloads, the page doesn't refresh, so we hide loader after a delay
            setTimeout(() => {
                if (window.DS_UI) DS_UI.loading(false);
            }, 3000);
        };


        const onDragStart = () => {
        };

        const onDragEnd = () => {
            refreshPreview();
        };



        onMounted(() => {
            document.addEventListener('keydown', (e) => {
                // Ctrl/Cmd + S to save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    savePage();
                }

                // Intercept F5 or Ctrl+R (Refresh)
                if ((e.key === 'F5') || ((e.ctrlKey || e.metaKey) && e.key === 'r')) {
                    if (hasUnsavedChanges.value && !isNavigatingAway.value) {
                        e.preventDefault();
                        if (window.DS_UI && window.DS_UI.confirm) {
                            window.DS_UI.confirm({
                                title: translations.unsaved_changes_title || 'Unsaved Changes',
                                message: translations.unsaved_changes_msg || 'You have unsaved changes. Are you sure you want to leave?',
                                confirmText: translations.leave || 'Leave',
                                cancelText: translations.stay || 'Stay',
                                onConfirm: () => {
                                    isNavigatingAway.value = true;
                                    window.location.reload();
                                }
                            });
                        }
                        return false;
                    }
                }

                // ESC to deselect
                if (e.key === 'Escape') {
                    activeIndex.value = null;
                }
                // Arrow keys to navigate
                if (activeIndex.value !== null) {
                    if (e.key === 'ArrowUp' && activeIndex.value > 0) {
                        selectSection(activeIndex.value - 1);
                    } else if (e.key === 'ArrowDown' && activeIndex.value < activeSections.value.length - 1) {
                        selectSection(activeIndex.value + 1);
                    }
                }
            });

            // Intercept internal navigation clicks
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                // Skip if no link, target blank, or already navigating
                if (!link || !link.href || link.target || isNavigatingAway.value || !hasUnsavedChanges.value) return;

                // Allow specific internal anchors or hash links
                if (link.getAttribute('href').startsWith('#') || link.getAttribute('href').startsWith('javascript:')) return;

                // Check if it's an internal link (same domain)
                try {
                    const url = new URL(link.href);
                    if (url.origin === window.location.origin) {
                        e.preventDefault();
                        if (window.DS_UI && window.DS_UI.confirm) {
                            window.DS_UI.confirm({
                                title: translations.unsaved_changes_title || 'Unsaved Changes',
                                message: translations.unsaved_changes_msg || 'You have unsaved changes. Are you sure you want to leave?',
                                confirmText: translations.leave || 'Leave',
                                cancelText: translations.stay || 'Stay',
                                onConfirm: () => {
                                    isNavigatingAway.value = true;
                                    window.location.href = link.href;
                                }
                            });
                        } else if (confirm(translations.unsaved_changes_msg)) {
                            isNavigatingAway.value = true;
                            window.location.href = link.href;
                        }
                    }
                } catch (err) {
                    // Invalid URL or other issue
                }
            });

            // Intercept form submissions (e.g., Logout)
            document.addEventListener('submit', (e) => {
                if (hasUnsavedChanges.value && !isNavigatingAway.value && !e.target.closest('#no-code-builder')) {
                    e.preventDefault();
                    if (window.DS_UI && window.DS_UI.confirm) {
                        window.DS_UI.confirm({
                            title: translations.unsaved_changes_title,
                            message: translations.unsaved_changes_msg,
                            confirmText: translations.leave,
                            cancelText: translations.stay,
                            onConfirm: () => {
                                isNavigatingAway.value = true;
                                e.target.submit();
                            }
                        });
                    }
                }
            });

            window.addEventListener('beforeunload', (e) => {
                if (hasUnsavedChanges.value && !isNavigatingAway.value) {
                    e.preventDefault();
                    e.returnValue = '';
                    return '';
                }
            });
        });

        let refreshTimeout = null;
        watch(() => JSON.stringify(activeSections.value), (newVal, oldVal) => {
            if (newVal === oldVal) return;

            if (refreshTimeout) clearTimeout(refreshTimeout);
            refreshTimeout = setTimeout(() => {
                refreshPreview();
            }, 500); // 500ms debounce for balance between performance and "instant" feel
        });

        // Listen for messages from the preview iframe
        window.addEventListener('message', (event) => {
            if (event.data.type === 'PREVIEW_UPDATE_COMPLETE') {
                if (window.DS_UI) DS_UI.loading(false);
            }
            if (event.data.type === 'PREVIEW_UPDATE_START') {
                // Only show loader for heavy operations (like adding/removing sections)
                // For simple text editing, we don't show the full-page loader to avoid disruption
            }
        });

        return {
            activeSections,
            activeIndex,
            activeTab,
            previewSize,
            zoomLevel,
            components,
            isSaving,
            lastSaved,
            activeSection,
            hasUnsavedChanges,
            adjustZoom,
            selectSection,
            addSection: (comp) => {
                if (window.DS_UI) DS_UI.loading(true);
                const secId = comp.category + '-' + Date.now();
                const defaults = comp.config_schema || {};

                const newSection = {
                    id: secId,
                    type: comp.category,
                    name: comp.name,
                    thumbnail: comp.thumbnail_url || comp.thumbnail,
                    blade_template: comp.blade_template,
                    attributes: { id: secId },
                    style: Object.assign({
                        background: '#0f172a',
                        color: '#ffffff',
                        padding: 80
                    }, defaults.style || {}),
                    content: JSON.parse(JSON.stringify(defaults.content || defaults))
                };

                activeSections.value.push(newSection);
                activeIndex.value = activeSections.value.length - 1;
            },
            removeSection: (idx) => {
                const section = activeSections.value[idx];
                if (!section) return;

                if (window.DS_UI && window.DS_UI.confirm) {
                    window.DS_UI.confirm({
                        title: translations.confirm_delete || 'Confirm Delete',
                        message: `${translations.confirm_delete || 'Delete'} "${section.name || section.type}"?`,
                        confirmText: translations.delete || 'Delete',
                        cancelText: translations.stay || 'Stay',
                        onConfirm: () => {
                            if (window.DS_UI) DS_UI.loading(true);
                            activeSections.value.splice(idx, 1);
                            if (activeIndex.value === idx) activeIndex.value = null;
                        }
                    });
                } else if (confirm(`${translations.confirm_delete || 'Delete'} "${section.name || section.type}"?`)) {
                    if (window.DS_UI) DS_UI.loading(true);
                    activeSections.value.splice(idx, 1);
                    if (activeIndex.value === idx) activeIndex.value = null;
                }
            },
            refreshPreview,
            savePage,
            exportPage,
            translations,
            onDragStart,
            onDragEnd,
            categorizedComponents,
            selectedCategory,
            showLibraryModal,
            openLibraryModal: () => {
                showLibraryModal.value = true;
            },
            closeLibraryModal: () => {
                showLibraryModal.value = false;
                selectedCategory.value = null;
            },
            openCategoryModal: (cat) => {
                selectedCategory.value = cat;
            },
            getCategoryIcon: (cat) => {
                const icons = {
                    'header': 'fas fa-window-maximize',
                    'hero': 'fas fa-star',
                    'features': 'fas fa-list-ul',
                    'stats': 'fas fa-chart-bar',
                    'pricing': 'fas fa-tags',
                    'cta': 'fas fa-bullhorn',
                    'footer': 'fas fa-dock'
                };
                return icons[cat.toLowerCase()] || 'fas fa-cube';
            },
            activeSidebar,
            toggleSidebar: (side) => {
                if (activeSidebar.value === side) {
                    activeSidebar.value = null;
                } else {
                    activeSidebar.value = side;
                }
            },
            formatStyleKey: (key) => {
                // Try translation first (bg_color, color, etc)
                if (translations[key]) return translations[key];
                if (translations[key + '_color']) return translations[key + '_color'];

                return key
                    .split('_')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ')
                    .replace('Bg', 'Background')
                    .replace('Btn', 'Button');
            },
            isHexColor: (val) => {
                return typeof val === 'string' && /^#([0-9A-F]{3}){1,2}$/i.test(val);
            },
            elitePresets,
            advancedStyles,
            toggleAdvanced: (key) => {
                advancedStyles.value[key] = !advancedStyles.value[key];
            },
            getFriendlyValue: (val) => {
                if (!val) return 'None';
                const preset = elitePresets.find(p => p.value === val);
                if (preset) return preset.name;
                if (val.length > 20) return 'Custom Style...';
                return val;
            },
            applyPreset: (key, val) => {
                if (activeIndex.value !== null) {
                    activeSections.value[activeIndex.value].style[key] = val;
                }
            },

            // Media Upload Methods
            triggerUpload: (key) => {
                document.getElementById('file-upload-' + key).click();
            },
            triggerArrayUpload: (key, index, subKey) => {
                document.getElementById('file-upload-' + key + '-' + index + '-' + subKey).click();
            },
            handleFileUpload: async (event, key) => {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                try {
                    if (window.DS_UI) DS_UI.loading(true);
                    const response = await fetch(config.uploadRoute, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrfToken
                        },
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        if (activeIndex.value !== null) {
                            activeSections.value[activeIndex.value].content[key] = data.url;
                        }
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert(config.translations.generic_error);
                } finally {
                    if (window.DS_UI) DS_UI.loading(false);
                }
            },
            handleArrayFileUpload: async (event, key, index, subKey) => {
                const file = event.target.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                try {
                    if (window.DS_UI) DS_UI.loading(true);
                    const response = await fetch(config.uploadRoute, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrfToken
                        },
                        body: formData
                    });
                    const data = await response.json();

                    if (data.success) {
                        if (activeIndex.value !== null) {
                            activeSections.value[activeIndex.value].content[key][index][subKey] = data.url;
                        }
                    } else {
                        alert('Upload failed: ' + data.message);
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert(config.translations.generic_error);
                } finally {
                    if (window.DS_UI) DS_UI.loading(false);
                }
            },

            // Icon Picker Methods
            showIconModal,
            iconSearch,
            filteredIcons,
            currentIconValue,
            openIconModal: (key) => {
                currentIconKey.value = key;
                currentIconIndex.value = null;
                currentIconSubKey.value = null;
                showIconModal.value = true;
                iconSearch.value = '';
            },
            openArrayIconModal: (key, index, subKey) => {
                currentIconKey.value = key;
                currentIconIndex.value = index;
                currentIconSubKey.value = subKey;
                showIconModal.value = true;
                iconSearch.value = '';
            },
            closeIconModal: () => {
                showIconModal.value = false;
                currentIconKey.value = null;
            },
            selectIcon: (iconClass) => {
                if (activeIndex.value !== null && currentIconKey.value) {
                    if (currentIconIndex.value !== null && currentIconSubKey.value) {
                        // Update array item icon
                        activeSections.value[activeIndex.value].content[currentIconKey.value][currentIconIndex.value][currentIconSubKey.value] = iconClass;
                    } else {
                        // Update main content icon
                        activeSections.value[activeIndex.value].content[currentIconKey.value] = iconClass;
                    }
                }
                showIconModal.value = false;
            }
        };
    }
});

// Fix for vuedraggable in UMD mode with Vue 3
const draggableComponent = window.vuedraggable?.default || window.vuedraggable;
if (draggableComponent) {
    app.component('draggable', draggableComponent);
}

app.mount('#no-code-builder');
