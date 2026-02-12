/**
 * Admin Email Management Scripts
 * Handles Live Previews, Template Selection, and Copy-to-Clipboard
 */
document.addEventListener('DOMContentLoaded', function () {
    // Live Preview Logic
    const subjectInput = document.getElementById('template_subject') || document.getElementById('message_subject');
    const contentInput = document.getElementById('template_content') || document.getElementById('message_content');
    const previewSubject = document.getElementById('preview_subject');
    const previewContent = document.getElementById('preview_content');

    if (subjectInput && contentInput && previewSubject && previewContent) {
        const updatePreview = () => {
            previewSubject.textContent = subjectInput.value || 'Subject Preview';
            previewContent.innerHTML = contentInput.value || '<p class="text-muted">Content Preview</p>';
        };

        subjectInput.addEventListener('input', updatePreview);
        contentInput.addEventListener('input', updatePreview);

        // Initial call
        updatePreview();
    }

    // Template Selection Logic
    const templateSelect = document.getElementById('template_select');
    if (templateSelect && subjectInput && contentInput) {
        templateSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            if (selected.value) {
                subjectInput.value = selected.getAttribute('data-subject') || '';
                contentInput.value = selected.getAttribute('data-content') || '';

                // Trigger input event to update preview
                subjectInput.dispatchEvent(new Event('input'));
                contentInput.dispatchEvent(new Event('input'));
            }
        });
    }

    // Clipboard Copy Utility
    window.copyToClipboard = function (text) {
        if (!navigator.clipboard) {
            alert('Clipboard API not available');
            return;
        }

        navigator.clipboard.writeText(text).then(() => {
            if (window.DS_UI && window.DS_UI.showToast) {
                window.DS_UI.showToast('Copied: ' + text, 'info');
            } else {
                // Fallback if global UI helper is missing
                console.log('Copied to clipboard: ' + text);
            }
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    };
});
