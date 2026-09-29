/**
 * ============================================================================
 * COMPLEXUS NOTIFICATION SYSTEM (TOASTS & CONFIRM MODAL)
 * Modern, Lightweight, Reusable Toast & Async Confirmation System
 * ============================================================================
 */

(function () {
    'use strict';

    // SVG Icons
    const ICONS = {
        success: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>`,
        error: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>`,
        warning: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>`,
        info: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>`,
        close: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>`
    };

    const DEFAULT_TITLES = {
        success: 'Thành công',
        error: 'Đã xảy ra lỗi',
        warning: 'Cảnh báo',
        info: 'Thông báo'
    };

    // State
    let toastContainer = null;
    let confirmBackdrop = null;
    let currentConfirmResolve = null;

    /**
     * Get or create Toast Container
     */
    function getToastContainer() {
        if (!toastContainer || !document.body.contains(toastContainer)) {
            toastContainer = document.getElementById('cpx-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'cpx-toast-container';
                toastContainer.setAttribute('aria-live', 'polite');
                toastContainer.setAttribute('role', 'status');
                document.body.appendChild(toastContainer);
            }
        }
        return toastContainer;
    }

    /**
     * Show Toast Notification
     * @param {string} message 
     * @param {'success'|'error'|'warning'|'info'} type 
     * @param {number} duration (ms)
     * @param {string|null} title 
     */
    function showToast(message, type = 'info', duration = 3800, title = null) {
        if (!message) return;
        const validTypes = ['success', 'error', 'warning', 'info'];
        const toastType = validTypes.includes(type) ? type : 'info';
        const displayTitle = title !== null ? title : DEFAULT_TITLES[toastType];

        const container = getToastContainer();
        const toast = document.createElement('div');
        toast.className = `cpx-toast cpx-toast--${toastType}`;
        toast.setAttribute('role', 'alert');

        toast.innerHTML = `
            <div class="cpx-toast__icon-badge">
                ${ICONS[toastType]}
            </div>
            <div class="cpx-toast__content">
                ${displayTitle ? `<div class="cpx-toast__title">${escapeHtml(displayTitle)}</div>` : ''}
                <p class="cpx-toast__message">${escapeHtml(message)}</p>
            </div>
            <button type="button" class="cpx-toast__close-btn" aria-label="Đóng thông báo">
                ${ICONS.close}
            </button>
            <div class="cpx-toast__progress-wrapper">
                <div class="cpx-toast__progress" style="animation-duration: ${duration}ms;"></div>
            </div>
        `;

        container.appendChild(toast);

        // Trigger entrance animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 20);

        let timer = null;
        let remaining = duration;
        let startTime = Date.now();

        function dismiss() {
            if (toast.classList.contains('hide')) return;
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 350);
        }

        function startTimer(time) {
            timer = setTimeout(dismiss, time);
            startTime = Date.now();
        }

        // Close button click
        const closeBtn = toast.querySelector('.cpx-toast__close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                clearTimeout(timer);
                dismiss();
            });
        }

        // Hover pause logic
        toast.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            remaining -= (Date.now() - startTime);
        });

        toast.addEventListener('mouseleave', () => {
            if (remaining > 0) {
                startTimer(remaining);
            } else {
                dismiss();
            }
        });

        startTimer(duration);

        return {
            element: toast,
            dismiss: dismiss
        };
    }

    /**
     * Get or create Confirmation Modal DOM
     */
    function getConfirmModal() {
        if (!confirmBackdrop || !document.body.contains(confirmBackdrop)) {
            confirmBackdrop = document.getElementById('cpx-confirm-backdrop');
            if (!confirmBackdrop) {
                confirmBackdrop = document.createElement('div');
                confirmBackdrop.id = 'cpx-confirm-backdrop';
                confirmBackdrop.innerHTML = `
                    <div class="cpx-confirm-modal" role="dialog" aria-modal="true">
                        <div class="cpx-confirm__icon-wrap" id="cpxConfirmIcon"></div>
                        <h3 class="cpx-confirm__title" id="cpxConfirmTitle">Xác nhận</h3>
                        <p class="cpx-confirm__message" id="cpxConfirmMessage">Bạn có chắc chắn muốn thực hiện thao tác này?</p>
                        <div class="cpx-confirm__actions">
                            <button type="button" class="cpx-confirm__btn cpx-confirm__btn--cancel" id="cpxConfirmCancel">Hủy bỏ</button>
                            <button type="button" class="cpx-confirm__btn cpx-confirm__btn--confirm" id="cpxConfirmOk">Xác nhận</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(confirmBackdrop);

                // Backdrop click to cancel
                confirmBackdrop.addEventListener('click', (e) => {
                    if (e.target === confirmBackdrop) {
                        closeConfirm(false);
                    }
                });

                // Button listeners
                document.getElementById('cpxConfirmCancel').addEventListener('click', () => closeConfirm(false));
                document.getElementById('cpxConfirmOk').addEventListener('click', () => closeConfirm(true));

                // Keyboard handling (Esc to cancel, Enter to confirm)
                document.addEventListener('keydown', (e) => {
                    if (confirmBackdrop && confirmBackdrop.classList.contains('active')) {
                        if (e.key === 'Escape') {
                            e.preventDefault();
                            closeConfirm(false);
                        } else if (e.key === 'Enter') {
                            const okBtn = document.getElementById('cpxConfirmOk');
                            if (document.activeElement !== document.getElementById('cpxConfirmCancel')) {
                                e.preventDefault();
                                closeConfirm(true);
                            }
                        }
                    }
                });
            }
        }
        return confirmBackdrop;
    }

    function closeConfirm(result) {
        if (!confirmBackdrop || !confirmBackdrop.classList.contains('active')) return;
        confirmBackdrop.classList.remove('active');
        if (typeof currentConfirmResolve === 'function') {
            const resolve = currentConfirmResolve;
            currentConfirmResolve = null;
            resolve(result);
        }
    }

    /**
     * Show Modern Confirmation Dialog
     * Options:
     * - title (string)
     * - message (string)
     * - type ('danger' | 'warning' | 'primary')
     * - confirmText (string)
     * - cancelText (string)
     * 
     * Returns a Promise<boolean>
     */
    function showConfirm(titleOrOptions, message = '', optionsOrCallback = null) {
        let options = {};

        if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
            options = { ...titleOrOptions };
        } else {
            options.title = titleOrOptions || 'Xác nhận thao tác';
            options.message = message || '';
            if (typeof optionsOrCallback === 'function') {
                options.callback = optionsOrCallback;
            } else if (typeof optionsOrCallback === 'object' && optionsOrCallback !== null) {
                options = { ...options, ...optionsOrCallback };
            }
        }

        const backdrop = getConfirmModal();
        const modal = backdrop.querySelector('.cpx-confirm-modal');
        const iconWrap = document.getElementById('cpxConfirmIcon');
        const titleEl = document.getElementById('cpxConfirmTitle');
        const messageEl = document.getElementById('cpxConfirmMessage');
        const cancelBtn = document.getElementById('cpxConfirmCancel');
        const okBtn = document.getElementById('cpxConfirmOk');

        // Detect type
        let type = options.type;
        if (!type) {
            const lowerMsg = (options.message + ' ' + options.title).toLowerCase();
            if (lowerMsg.includes('xóa') || lowerMsg.includes('hủy') || lowerMsg.includes('delete') || lowerMsg.includes('destroy') || lowerMsg.includes('vĩnh viễn') || lowerMsg.includes('làm trống') || lowerMsg.includes('clear') || lowerMsg.includes('gỡ')) {
                type = 'danger';
            } else if (lowerMsg.includes('cảnh báo') || lowerMsg.includes('khôi phục') || lowerMsg.includes('thay đổi')) {
                type = 'warning';
            } else {
                type = 'primary';
            }
        }

        modal.className = `cpx-confirm-modal cpx-confirm--${type}`;
        iconWrap.innerHTML = ICONS[type === 'danger' ? 'error' : (type === 'warning' ? 'warning' : 'info')];
        titleEl.textContent = options.title || 'Xác nhận';
        messageEl.textContent = options.message || 'Bạn có chắc chắn muốn thực hiện thao tác này?';
        cancelBtn.textContent = options.cancelText || 'Hủy bỏ';
        okBtn.textContent = options.confirmText || (type === 'danger' ? 'Xác nhận xóa' : 'Xác nhận');

        backdrop.classList.add('active');
        setTimeout(() => okBtn.focus(), 50);

        return new Promise((resolve) => {
            currentConfirmResolve = (result) => {
                if (typeof options.callback === 'function') {
                    options.callback(result);
                }
                resolve(result);
            };
        });
    }

    /**
     * Helper to escape HTML characters
     */
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /**
     * Auto convert legacy inline confirm handlers to custom modal
     */
    function upgradeConfirmHandlers() {
        // Upgrade forms with onsubmit="return confirm('...')"
        const forms = document.querySelectorAll('form[onsubmit*="confirm("]');
        forms.forEach((form) => {
            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            const match = onsubmitAttr.match(/confirm\s*\(\s*['"`](.*?)['"`]\s*\)/);
            if (match && match[1]) {
                const message = match[1];
                form.removeAttribute('onsubmit');
                form.dataset.cpxConfirm = message;
            }
        });

        // Upgrade buttons with onclick="return confirm('...')"
        const buttons = document.querySelectorAll('button[onclick*="confirm("], input[type="submit"][onclick*="confirm("], a[onclick*="confirm("]');
        buttons.forEach((btn) => {
            const onclickAttr = btn.getAttribute('onclick') || '';
            const match = onclickAttr.match(/confirm\s*\(\s*['"`](.*?)['"`]\s*\)/);
            if (match && match[1]) {
                const message = match[1];
                btn.removeAttribute('onclick');
                btn.dataset.cpxConfirm = message;
            }
        });
    }

    /**
     * Global Interceptors for [data-confirm] and [data-cpx-confirm]
     */
    function initInterceptors() {
        upgradeConfirmHandlers();

        // Form Submit Interceptor
        document.addEventListener('submit', async function (e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            const confirmMsg = form.dataset.confirm || form.dataset.cpxConfirm;
            if (!confirmMsg) return;

            // If already verified by us, proceed with native submission
            if (form._cpxVerified) {
                form._cpxVerified = false;
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            const title = form.dataset.confirmTitle || 'Xác nhận thao tác';
            const type = form.dataset.confirmType || undefined;
            const confirmBtnText = form.dataset.confirmBtn || undefined;

            const confirmed = await showConfirm({
                title: title,
                message: confirmMsg,
                type: type,
                confirmText: confirmBtnText
            });

            if (confirmed) {
                form._cpxVerified = true;
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        }, true);

        // Click Interceptor for Links with data-confirm
        document.addEventListener('click', async function (e) {
            const trigger = e.target.closest('a[data-confirm], a[data-cpx-confirm], button[data-confirm], button[data-cpx-confirm]');
            if (!trigger) return;

            // If inside a form and is submit button, let form submit handler handle it
            if (trigger.tagName === 'BUTTON' && (trigger.type === 'submit' || !trigger.type) && trigger.closest('form')) {
                const form = trigger.closest('form');
                if (!form.dataset.confirm && !form.dataset.cpxConfirm) {
                    form.dataset.cpxConfirm = trigger.dataset.confirm || trigger.dataset.cpxConfirm;
                    if (trigger.dataset.confirmTitle) form.dataset.confirmTitle = trigger.dataset.confirmTitle;
                    if (trigger.dataset.confirmType) form.dataset.confirmType = trigger.dataset.confirmType;
                }
                return;
            }

            if (trigger.tagName === 'A' && trigger.href) {
                const confirmMsg = trigger.dataset.confirm || trigger.dataset.cpxConfirm;
                if (!confirmMsg) return;

                if (trigger._cpxVerified) {
                    trigger._cpxVerified = false;
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const confirmed = await showConfirm({
                    title: trigger.dataset.confirmTitle || 'Xác nhận chuyển tiếp',
                    message: confirmMsg,
                    type: trigger.dataset.confirmType || undefined
                });

                if (confirmed) {
                    trigger._cpxVerified = true;
                    window.location.href = trigger.href;
                }
            }
        }, true);
    }

    /**
     * Trigger Flash Messages from Server (Blade Session)
     */
    function triggerFlashMessages() {
        const flashElements = document.querySelectorAll('[data-toast-flash]');
        flashElements.forEach((el) => {
            const type = el.dataset.toastFlash;
            const message = el.dataset.toastMessage || el.textContent.trim();
            const title = el.dataset.toastTitle || null;
            if (message) {
                showToast(message, type, 4000, title);
            }
            // Hide element from view if it was visible
            el.style.display = 'none';
        });
    }

    // Auto initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initInterceptors();
            triggerFlashMessages();
        });
    } else {
        initInterceptors();
        triggerFlashMessages();
    }

    // Modern replacement for window.alert (safe, non-blocking toast warning)
    const nativeAlert = window.alert;
    window.alert = function (message, title = null) {
        showToast(message, 'warning', 4200, title);
    };

    // Public API Exports
    window.showToast = showToast;
    window.showConfirm = showConfirm;
    window.toast = {
        success: (msg, title = null, duration = 3800) => showToast(msg, 'success', duration, title),
        error: (msg, title = null, duration = 4500) => showToast(msg, 'error', duration, title),
        warning: (msg, title = null, duration = 4000) => showToast(msg, 'warning', duration, title),
        info: (msg, title = null, duration = 3800) => showToast(msg, 'info', duration, title)
    };
    window.upgradeConfirmHandlers = upgradeConfirmHandlers;

})();
