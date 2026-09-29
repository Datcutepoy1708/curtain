/**
 * CurtainLux Custom Notification & Confirmation System
 * Thay thế hoàn toàn "localhost says" của trình duyệt bằng Toast và Modal Xác Nhận sang trọng, hiện đại
 */

(function () {
    'use strict';

    // 1. Khởi tạo Toast Container
    let toastContainer = null;
    function getToastContainer() {
        if (!toastContainer || !document.body.contains(toastContainer)) {
            toastContainer = document.getElementById('curtain-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'curtain-toast-container';
                document.body.appendChild(toastContainer);
            }
        }
        return toastContainer;
    }

    // 2. Định nghĩa Toast Controller
    const icons = {
        success: 'fa-solid fa-circle-check',
        error: 'fa-solid fa-circle-xmark',
        warning: 'fa-solid fa-triangle-exclamation',
        info: 'fa-solid fa-circle-info'
    };

    const defaultTitles = {
        success: 'Thành công',
        error: 'Đã có lỗi xảy ra',
        warning: 'Lưu ý',
        info: 'Thông báo hệ thống'
    };

    function showToast(type = 'info', message = '', title = '', duration = 4200) {
        if (!message) return;
        const container = getToastContainer();

        const toast = document.createElement('div');
        toast.className = `curtain-toast toast-${type}`;
        
        const finalTitle = title || defaultTitles[type] || 'Thông báo';
        const iconClass = icons[type] || icons.info;

        toast.innerHTML = `
            <div class="curtain-toast-icon">
                <i class="${iconClass}"></i>
            </div>
            <div class="curtain-toast-body">
                <div class="curtain-toast-title">${escapeHtml(finalTitle)}</div>
                <div class="curtain-toast-message">${escapeHtml(message)}</div>
            </div>
            <button type="button" class="curtain-toast-close" title="Đóng">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="curtain-toast-progress">
                <div class="curtain-toast-progress-bar"></div>
            </div>
        `;

        container.appendChild(toast);

        // Hiển thị mượt mà
        requestAnimationFrame(() => {
            toast.classList.add('is-visible');
        });

        // Thanh tiến trình tự tắt
        const progressBar = toast.querySelector('.curtain-toast-progress-bar');
        if (progressBar && duration > 0) {
            progressBar.style.transitionDuration = `${duration}ms`;
            requestAnimationFrame(() => {
                progressBar.style.transform = 'scaleX(0)';
            });
        }

        let hideTimeout;
        const closeToast = () => {
            clearTimeout(hideTimeout);
            toast.classList.remove('is-visible');
            toast.classList.add('is-hiding');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 350);
        };

        toast.querySelector('.curtain-toast-close').addEventListener('click', closeToast);

        if (duration > 0) {
            hideTimeout = setTimeout(closeToast, duration);
        }

        return toast;
    }

    function escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // 3. Khởi tạo Custom Confirm / Dialog (Thay thế window.confirm & alert)
    let dialogBackdrop = null;
    let activeConfirmResolve = null;

    function initDialogBackdrop() {
        if (dialogBackdrop && document.body.contains(dialogBackdrop)) return dialogBackdrop;
        dialogBackdrop = document.createElement('div');
        dialogBackdrop.className = 'curtain-dialog-backdrop';
        dialogBackdrop.id = 'curtainDialogBackdrop';
        dialogBackdrop.innerHTML = `
            <div class="curtain-dialog-box" role="dialog" aria-modal="true">
                <div class="curtain-dialog-header">
                    <div class="curtain-dialog-icon" id="curtainDialogIcon">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <div class="curtain-dialog-content">
                        <div class="curtain-dialog-title" id="curtainDialogTitle">Xác nhận thao tác</div>
                        <div class="curtain-dialog-message" id="curtainDialogMessage">Bạn có chắc muốn thực hiện hành động này?</div>
                    </div>
                </div>
                <div class="curtain-dialog-footer">
                    <button type="button" class="curtain-dialog-btn curtain-dialog-btn-cancel" id="curtainDialogCancelBtn">Hủy bỏ</button>
                    <button type="button" class="curtain-dialog-btn curtain-dialog-btn-confirm" id="curtainDialogConfirmBtn">Đồng ý</button>
                </div>
            </div>
        `;
        document.body.appendChild(dialogBackdrop);

        const cancelBtn = dialogBackdrop.querySelector('#curtainDialogCancelBtn');
        const confirmBtn = dialogBackdrop.querySelector('#curtainDialogConfirmBtn');

        cancelBtn.addEventListener('click', () => {
            closeDialog(false);
        });

        confirmBtn.addEventListener('click', () => {
            closeDialog(true);
        });

        dialogBackdrop.addEventListener('click', (e) => {
            if (e.target === dialogBackdrop) {
                closeDialog(false);
            }
        });

        document.addEventListener('keydown', (e) => {
            if (!dialogBackdrop.classList.contains('is-open')) return;
            if (e.key === 'Escape') {
                closeDialog(false);
            } else if (e.key === 'Enter') {
                confirmBtn.focus();
            }
        });

        return dialogBackdrop;
    }

    function closeDialog(result) {
        if (!dialogBackdrop) return;
        dialogBackdrop.classList.remove('is-open');
        if (activeConfirmResolve) {
            activeConfirmResolve(result);
            activeConfirmResolve = null;
        }
    }

    function customConfirm(options = {}) {
        return new Promise((resolve) => {
            initDialogBackdrop();
            activeConfirmResolve = resolve;

            const isString = typeof options === 'string';
            const message = isString ? options : (options.message || 'Bạn có chắc chắn muốn thực hiện thao tác này?');
            const title = (!isString && options.title) ? options.title : 'Xác nhận thao tác';
            const confirmText = (!isString && options.confirmText) ? options.confirmText : 'Đồng ý';
            const cancelText = (!isString && options.cancelText) ? options.cancelText : 'Hủy bỏ';
            const isDanger = (!isString && typeof options.isDanger !== 'undefined') 
                ? options.isDanger 
                : (message.toLowerCase().includes('xóa') || message.toLowerCase().includes('hủy') || message.toLowerCase().includes('delete') || message.toLowerCase().includes('bỏ'));

            const titleEl = dialogBackdrop.querySelector('#curtainDialogTitle');
            const msgEl = dialogBackdrop.querySelector('#curtainDialogMessage');
            const iconEl = dialogBackdrop.querySelector('#curtainDialogIcon');
            const cancelBtn = dialogBackdrop.querySelector('#curtainDialogCancelBtn');
            const confirmBtn = dialogBackdrop.querySelector('#curtainDialogConfirmBtn');

            titleEl.textContent = title;
            msgEl.textContent = message;
            cancelBtn.textContent = cancelText;
            confirmBtn.textContent = confirmText;

            // Chế độ nút cảnh báo nguy hiểm / thao tác xóa
            if (isDanger) {
                iconEl.className = 'curtain-dialog-icon icon-danger';
                iconEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
                confirmBtn.className = 'curtain-dialog-btn curtain-dialog-btn-confirm is-danger';
            } else {
                iconEl.className = 'curtain-dialog-icon icon-warning';
                iconEl.innerHTML = '<i class="fa-solid fa-circle-question"></i>';
                confirmBtn.className = 'curtain-dialog-btn curtain-dialog-btn-confirm';
            }

            dialogBackdrop.classList.add('is-open');
            confirmBtn.focus();
        });
    }

    // 4. Nâng cấp tự động tất cả các form & button có onsubmit / onclick chứa confirm(...)
    function upgradeConfirmHandlers() {
        // Form chứa onsubmit="return confirm('...')"
        document.querySelectorAll('form[onsubmit*="confirm("]').forEach((form) => {
            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            const match = onsubmitAttr.match(/confirm\s*\(\s*(['"`])([\s\S]*?)\1\s*\)/);
            if (match && match[2]) {
                form.dataset.confirm = match[2];
                form.removeAttribute('onsubmit');
            }
        });

        // Button/link chứa onclick="return confirm('...')"
        document.querySelectorAll('button[onclick*="confirm("], a[onclick*="confirm("], input[type="submit"][onclick*="confirm("]').forEach((el) => {
            const onclickAttr = el.getAttribute('onclick') || '';
            const match = onclickAttr.match(/confirm\s*\(\s*(['"`])([\s\S]*?)\1\s*\)/);
            if (match && match[2]) {
                el.dataset.confirm = match[2];
                el.removeAttribute('onclick');
            }
        });
    }

    // 5. Interceptor toàn cục Form Submit & Click
    function initInterceptors() {
        upgradeConfirmHandlers();

        // Chặn Form Submit có data-confirm
        document.addEventListener('submit', async function (e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            const confirmMsg = form.dataset.confirm;
            if (!confirmMsg) return;

            if (form._isConfirmed) {
                form._isConfirmed = false;
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();

            const isDanger = confirmMsg.toLowerCase().includes('xóa') || confirmMsg.toLowerCase().includes('hủy') || confirmMsg.toLowerCase().includes('bỏ');
            const ok = await customConfirm({
                message: confirmMsg,
                isDanger: isDanger
            });

            if (ok) {
                form._isConfirmed = true;
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        }, true);

        // Chặn Click trên các nút / thẻ A có data-confirm
        document.addEventListener('click', async function (e) {
            const target = e.target.closest('button[data-confirm], a[data-confirm], input[type="submit"][data-confirm]');
            if (!target) return;

            if (target._isConfirmed) {
                target._isConfirmed = false;
                return;
            }

            // Nếu là button submit của form
            const form = target.closest('form');
            if (form && (target.type === 'submit' || !target.type)) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const confirmMsg = target.dataset.confirm;
                const isDanger = confirmMsg.toLowerCase().includes('xóa') || confirmMsg.toLowerCase().includes('hủy') || confirmMsg.toLowerCase().includes('bỏ');
                const ok = await customConfirm({
                    message: confirmMsg,
                    isDanger: isDanger
                });

                if (ok) {
                    target._isConfirmed = true;
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit(target);
                    } else {
                        form.submit();
                    }
                }
                return;
            }

            // Nếu là thẻ liên kết <a>
            if (target.tagName === 'A' && target.href && !target.href.startsWith('javascript:')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const confirmMsg = target.dataset.confirm;
                const isDanger = confirmMsg.toLowerCase().includes('xóa') || confirmMsg.toLowerCase().includes('hủy') || confirmMsg.toLowerCase().includes('bỏ');
                const ok = await customConfirm({
                    message: confirmMsg,
                    isDanger: isDanger
                });

                if (ok) {
                    target._isConfirmed = true;
                    window.location.href = target.href;
                }
            }
        }, true);
    }

    // 6. Quét Flash Message của Laravel
    function triggerFlashMessages() {
        // Quét các alert-success, alert-danger xuất hiện từ Blade
        document.querySelectorAll('.alert-success, [data-toast-flash="success"]').forEach(el => {
            const text = el.dataset.toastMessage || el.textContent.trim();
            if (text && !el.dataset.toastTriggered) {
                el.dataset.toastTriggered = 'true';
                showToast('success', text, 'Thành công');
            }
        });

        document.querySelectorAll('.alert-danger, [data-toast-flash="error"]').forEach(el => {
            const text = el.dataset.toastMessage || el.textContent.trim();
            if (text && !el.dataset.toastTriggered) {
                el.dataset.toastTriggered = 'true';
                showToast('error', text, 'Lỗi thao tác');
            }
        });
    }

    // 7. Ghi đè window.alert mặc định để hoàn toàn biến mất "localhost says"
    window.alert = function (message) {
        showToast('warning', String(message), 'Lưu ý từ hệ thống');
    };

    // 8. Đăng ký API công khai
    window.CurtainNotify = {
        show: showToast,
        success: (msg, title) => showToast('success', msg, title),
        error: (msg, title) => showToast('error', msg, title),
        warning: (msg, title) => showToast('warning', msg, title),
        info: (msg, title) => showToast('info', msg, title),
        confirm: customConfirm,
        alert: function (msg, title = 'Thông báo') {
            return new Promise((resolve) => {
                showToast('info', msg, title);
                resolve();
            });
        },
        upgradeConfirmHandlers: upgradeConfirmHandlers
    };

    window.notify = window.CurtainNotify;
    window.toast = window.CurtainNotify;

    // Tự động khởi chạy
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initInterceptors();
            triggerFlashMessages();
        });
    } else {
        initInterceptors();
        triggerFlashMessages();
    }

    // Theo dõi DOM thay đổi nếu có nội dung load động
    if (window.MutationObserver) {
        const observer = new MutationObserver(() => {
            upgradeConfirmHandlers();
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }
})();
