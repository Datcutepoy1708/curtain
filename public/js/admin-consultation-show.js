/**
 * ==========================================================================
 * ADMIN CONSULTATION SHOW - DEDICATED JAVASCRIPT
 * Handles Window Measurements Modal, Quotation Tabs, and Quick Copy Actions
 * ==========================================================================
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        // Tab switching for Quotation versions
        const quoteTabs = document.querySelectorAll('.quote-tab-btn');
        if (quoteTabs.length > 0) {
            quoteTabs.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    quoteTabs.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const targetId = this.getAttribute('href').substring(1);
                    document.querySelectorAll('[id^="quotation-"]').forEach(div => {
                        div.style.display = 'none';
                    });
                    const targetDiv = document.getElementById(targetId);
                    if (targetDiv) {
                        targetDiv.style.display = 'block';
                    }
                });
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('addWindowModal');
                if (modal && modal.style.display === 'flex') {
                    modal.style.display = 'none';
                }
            }
        });

        // Close modal when clicking outside content
        const addWindowModal = document.getElementById('addWindowModal');
        if (addWindowModal) {
            addWindowModal.addEventListener('click', function (e) {
                if (e.target === this) {
                    this.style.display = 'none';
                }
            });
        }
    });

    // Global Modal & Helper Functions (exposed to window for inline button onclick handlers)
    window.openNewSurveyWindow = function () {
        const cfg = window.consultationConfig || {};
        const windowForm = document.getElementById('windowForm');
        const modal = document.getElementById('addWindowModal');
        const methodInput = document.getElementById('windowMethod');

        if (!windowForm || !modal) return;

        windowForm.reset();
        windowForm.action = cfg.createWindowUrl || '';
        if (methodInput) methodInput.value = 'POST';
        modal.style.display = 'flex';
    };

    window.editSurveyWindow = function (id) {
        const cfg = window.consultationConfig || {};
        const surveyWindows = cfg.surveyWindows || {};
        const item = surveyWindows[id];
        if (!item) return;

        const windowForm = document.getElementById('windowForm');
        const modal = document.getElementById('addWindowModal');
        const methodInput = document.getElementById('windowMethod');

        if (!windowForm || !modal) return;

        windowForm.reset();
        const fieldNames = [
            'room_name', 'product_id', 'width', 'height', 'quantity',
            'install_type', 'fabric_color', 'sewing_style', 'motor_type', 'notes'
        ];

        fieldNames.forEach(name => {
            if (windowForm.elements[name]) {
                windowForm.elements[name].value = item[name] ?? '';
            }
        });

        if (windowForm.elements['has_sheer']) {
            windowForm.elements['has_sheer'].checked = Boolean(item.has_sheer);
        }

        if (cfg.updateWindowUrl) {
            windowForm.action = cfg.updateWindowUrl.replace('__ID__', id);
        }
        if (methodInput) methodInput.value = 'PUT';
        modal.style.display = 'flex';
    };

    window.copyShareLink = function (inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        input.select();
        input.setSelectionRange(0, 99999);

        const showSuccessFeedback = () => {
            const origHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã chép!';
            btn.style.background = '#059669';
            setTimeout(() => {
                btn.innerHTML = origHtml;
                btn.style.background = '';
            }, 2000);
        };

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(input.value).then(showSuccessFeedback).catch(() => {
                document.execCommand('copy');
                showSuccessFeedback();
            });
        } else {
            document.execCommand('copy');
            showSuccessFeedback();
        }
    };
})();
