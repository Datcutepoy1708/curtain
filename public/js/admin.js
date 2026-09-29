/**
 * CurtainLux Admin Core JavaScript (Matching Complexus Admin Interactive Shell)
 * Full interactive support: Sidebar, Theme Toggle, Modals, Tabs, Bulk actions, Flash notices.
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Sidebar Elements & State Handlers
    const adminLayout = document.getElementById('adminLayout') || document.querySelector('.admin-layout');
    const adminSidebar = document.getElementById('adminSidebar');
    const adminSidebarBackdrop = document.querySelector('.admin-sidebar-backdrop');
    const adminSidebarToggle = document.querySelector('[data-admin-sidebar-toggle]') || document.querySelector('[data-admin-menu-toggle]');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');

    const setAdminSidebarCollapsed = (collapsed) => {
        if (!adminLayout) return;
        adminLayout.classList.toggle('sidebar-collapsed', collapsed);
        document.documentElement.classList.toggle('admin-collapsed-preload', collapsed);
        try {
            localStorage.setItem('admin_sidebar_collapsed', String(collapsed));
        } catch (e) {}

        const collapseIcon = document.querySelector('#sidebarCollapseBtn .collapse-icon');
        const collapseText = document.querySelector('#sidebarCollapseBtn .collapse-text');
        const collapseBtn = document.getElementById('sidebarCollapseBtn');

        if (collapseIcon) {
            collapseIcon.classList.toggle('fa-angles-left', !collapsed);
            collapseIcon.classList.toggle('fa-angles-right', collapsed);
        }
        if (collapseText) {
            collapseText.textContent = collapsed ? '' : 'Thu gọn sidebar';
        }
        if (collapseBtn) {
            collapseBtn.setAttribute('title', collapsed ? 'Mở rộng sidebar' : 'Thu gọn menu');
        }
    };

    // Initialize collapsed state from localStorage
    try {
        const savedCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
        if (savedCollapsed && window.innerWidth > 1024) {
            setAdminSidebarCollapsed(true);
        }
    } catch (e) {}

    // 2. Sidebar Accordion Navigation
    const groupButtons = document.querySelectorAll('.group-header-btn');
    groupButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            // If sidebar is collapsed on desktop, clicking an icon smoothly expands the sidebar & activates group
            if (adminLayout && adminLayout.classList.contains('sidebar-collapsed') && window.innerWidth > 1024) {
                setAdminSidebarCollapsed(false);
                this.classList.add('expanded');
                this.setAttribute('aria-expanded', 'true');
                const subMenu = this.nextElementSibling;
                if (subMenu && subMenu.classList.contains('sub-menu-list')) {
                    subMenu.classList.add('expanded');
                }
                return;
            }

            const isExpanded = this.classList.contains('expanded');
            const subMenu = this.nextElementSibling;
            
            if (isExpanded) {
                this.classList.remove('expanded');
                this.setAttribute('aria-expanded', 'false');
                if (subMenu && subMenu.classList.contains('sub-menu-list')) {
                    subMenu.classList.remove('expanded');
                }
            } else {
                this.classList.add('expanded');
                this.setAttribute('aria-expanded', 'true');
                if (subMenu && subMenu.classList.contains('sub-menu-list')) {
                    subMenu.classList.add('expanded');
                }
            }
        });
    });

    // 3. Toggle Trigger (Desktop: Collapse / Expand; Mobile: Drawer open / close)
    const setAdminSidebarOpen = (open) => {
        if (!adminSidebar) return;
        adminSidebar.classList.toggle('is-open', open);
        if (adminSidebarBackdrop) {
            adminSidebarBackdrop.classList.toggle('is-open', open);
        }
        if (adminSidebarToggle) {
            adminSidebarToggle.setAttribute('aria-expanded', String(open));
        }
        document.body.classList.toggle('admin-nav-open', open);
    };

    if (adminSidebarToggle) {
        adminSidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.innerWidth <= 1024) {
                const isOpen = adminSidebar && adminSidebar.classList.contains('is-open');
                setAdminSidebarOpen(!isOpen);
            } else {
                const isCollapsed = adminLayout && adminLayout.classList.contains('sidebar-collapsed');
                setAdminSidebarCollapsed(!isCollapsed);
            }
        });
    }

    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const isCollapsed = adminLayout && adminLayout.classList.contains('sidebar-collapsed');
            setAdminSidebarCollapsed(!isCollapsed);
        });
    }

    if (adminSidebarBackdrop) {
        adminSidebarBackdrop.addEventListener('click', () => setAdminSidebarOpen(false));
    }

    if (adminSidebar) {
        adminSidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    setAdminSidebarOpen(false);
                }
            });
        });
    }

    // 3. Theme Toggle (Light / Dark mode)
    const themeToggleBtns = document.querySelectorAll('.admin-theme-toggle, #adminThemeToggle');
    // Load initial theme from localStorage
    const savedTheme = localStorage.getItem('complexus_theme') || localStorage.getItem('admin_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-mode');
    }

    themeToggleBtns.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            if (newTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }
            localStorage.setItem('admin_theme', newTheme);
            localStorage.setItem('complexus_theme', newTheme);
        });
    });

    // 4. Modal Dialogs (open via data-modal-target="#modalId", close via [data-modal-close])
    document.querySelectorAll('[data-modal-target]').forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-modal-target');
            const targetModal = document.querySelector(targetId);
            if (targetModal) {
                targetModal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach(closeBtn => {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const modal = this.closest('.admin-modal-backdrop') || this.closest('.modal-backdrop');
            if (modal) {
                modal.classList.remove('is-open');
                document.body.style.overflow = '';
            }
        });
    });

    // Close modal when clicking on backdrop
    document.querySelectorAll('.admin-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('is-open');
                document.body.style.overflow = '';
            }
        });
    });

    // 5. Tabs Switching (data-tab-btn="tabKey", data-tab-pane="tabKey")
    document.querySelectorAll('[data-tab-btn]').forEach(tabBtn => {
        tabBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const tabGroup = this.closest('.tabs-container') || document;
            const tabKey = this.getAttribute('data-tab-btn');

            // Deactivate all sibling buttons
            tabGroup.querySelectorAll('[data-tab-btn]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Deactivate all sibling panes
            tabGroup.querySelectorAll('[data-tab-pane]').forEach(p => {
                p.classList.remove('active');
                p.style.display = 'none';
            });

            // Activate targeted pane
            const activePane = tabGroup.querySelector(`[data-tab-pane="${tabKey}"]`);
            if (activePane) {
                activePane.classList.add('active');
                activePane.style.display = 'block';
            }
        });
    });

    // 6. Bulk Checkboxes & Synchronized Bulk Action Bar
    const selectAllCheckboxes = document.querySelectorAll('#selectAll, [data-select-all], #selectAllRules');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox, .order-checkbox, .rule-item-checkbox');
    const bulkBar = document.querySelector('.bulk-action-bar');
    const bulkCountBadges = document.querySelectorAll('.bulk-count-badge, #selectedCount, #selectedRulesCount');

    const updateBulkState = () => {
        const checked = document.querySelectorAll('.item-checkbox:checked, .order-checkbox:checked');
        const count = checked.length;

        bulkCountBadges.forEach(badge => badge.textContent = count);

        if (bulkBar) {
            if (count > 0) {
                bulkBar.classList.add('is-visible');
            } else {
                bulkBar.classList.remove('is-visible');
            }
        }
    };

    selectAllCheckboxes.forEach(selectAll => {
        selectAll.addEventListener('change', function () {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkState();
        });
    });

    // Group-specific select-all (e.g. in option groups)
    const groupSelectAllCheckboxes = document.querySelectorAll('[data-select-group]');
    groupSelectAllCheckboxes.forEach(gSelect => {
        gSelect.addEventListener('change', function () {
            const groupId = this.getAttribute('data-select-group');
            const gItems = document.querySelectorAll(`.item-checkbox[data-group-id="${groupId}"]`);
            gItems.forEach(cb => cb.checked = gSelect.checked);
            updateBulkState();
        });
    });

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked) {
                selectAllCheckboxes.forEach(sa => sa.checked = false);
                const gId = this.getAttribute('data-group-id');
                if (gId) {
                    const gSelect = document.querySelector(`[data-select-group="${gId}"]`);
                    if (gSelect) gSelect.checked = false;
                }
            } else {
                const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
                if (allChecked) selectAllCheckboxes.forEach(sa => sa.checked = true);

                const gId = this.getAttribute('data-group-id');
                if (gId) {
                    const gItems = document.querySelectorAll(`.item-checkbox[data-group-id="${gId}"]`);
                    const allGChecked = Array.from(gItems).every(c => c.checked);
                    const gSelect = document.querySelector(`[data-select-group="${gId}"]`);
                    if (gSelect) gSelect.checked = allGChecked;
                }
            }
            updateBulkState();
        });
    });

    // Handle Bulk Action Buttons click (e.g. data-bulk-action="activate|deactivate|delete")
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-bulk-action]');
        if (btn) {
            e.preventDefault();
            const action = btn.getAttribute('data-bulk-action');
            const url = btn.getAttribute('data-bulk-url') || btn.closest('.bulk-action-bar')?.getAttribute('data-bulk-url');
            const checked = document.querySelectorAll('.item-checkbox:checked, .order-checkbox:checked');

            if (checked.length === 0) {
                alert('Vui lòng chọn ít nhất một mục để thực hiện thao tác.');
                return;
            }

            if (!url) {
                console.error('No bulk action URL specified.');
                return;
            }

            if (action === 'delete') {
                if (!confirm(`Bạn có chắc chắn muốn xóa ${checked.length} mục đã chọn? Thao tác này không thể khôi phục.`)) {
                    return;
                }
            }

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const token = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = token;
            form.appendChild(tokenInput);

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);

            checked.forEach(cb => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'ids[]';
                idInput.value = cb.value;
                form.appendChild(idInput);
            });

            document.body.appendChild(form);
            form.submit();
        }
    });

    // Quick One-Click Status Toggle via data-toggle-url
    document.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('[data-toggle-url]');
        if (toggleBtn) {
            e.preventDefault();
            const url = toggleBtn.getAttribute('data-toggle-url');
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const token = csrfMeta ? csrfMeta.getAttribute('content') : '';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = token;
            form.appendChild(tokenInput);

            document.body.appendChild(form);
            form.submit();
        }
    });

    // 7. Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success, .alert-info, .alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // 8. Auto Trigger Flash Toast Messages from HTML data attributes
    const flashMessages = document.querySelectorAll('[data-toast-flash]');
    flashMessages.forEach(el => {
        const type = el.getAttribute('data-toast-flash') || 'info';
        const msg = el.getAttribute('data-toast-message');
        if (msg && typeof window.showToast === 'function') {
            window.showToast(msg, type);
        }
    });

    // 9. Generic Delete Action for Data-delete-url (e.g. Gallery images, quick items)
    document.addEventListener('click', function (e) {
        const delBtn = e.target.closest('[data-delete-url]');
        if (delBtn) {
            e.preventDefault();
            const confirmMsg = delBtn.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn xóa mục này?';
            if (confirm(confirmMsg)) {
                const url = delBtn.getAttribute('data-delete-url');
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                const token = csrfMeta ? csrfMeta.getAttribute('content') : '';

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = token;
                form.appendChild(tokenInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        }
    });

    // 10. Multi-image file input live preview
    const multiFileInputs = document.querySelectorAll('input[type="file"][name="image_files[]"]');
    multiFileInputs.forEach(input => {
        input.addEventListener('change', function () {
            const previewGrid = document.getElementById('dropzonePreviewGrid');
            if (!previewGrid) return;

            previewGrid.innerHTML = '';
            if (this.files && this.files.length > 0) {
                previewGrid.style.display = 'grid';
                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const item = document.createElement('div');
                            item.className = 'dropzone-preview-item';

                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = file.name;

                            const sizeTag = document.createElement('span');
                            sizeTag.className = 'file-size-tag';
                            sizeTag.textContent = (file.size / 1024).toFixed(0) + ' KB';

                            item.appendChild(img);
                            item.appendChild(sizeTag);
                            previewGrid.appendChild(item);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                previewGrid.style.display = 'none';
            }
        });
    });

    // 11. Role Permissions Matrix Handlers
    document.querySelectorAll('[data-role-perm-select-all]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const selectAll = this.getAttribute('data-role-perm-select-all') === 'true';
            document.querySelectorAll('.role-perm-item').forEach(cb => cb.checked = selectAll);
            document.querySelectorAll('[data-module-toggle]').forEach(cb => cb.checked = selectAll);
        });
    });

    document.querySelectorAll('[data-module-toggle]').forEach(modCb => {
        modCb.addEventListener('change', function () {
            const modKey = this.getAttribute('data-module-toggle');
            const modItems = document.querySelectorAll(`.perm-mod-${modKey}`);
            modItems.forEach(cb => cb.checked = this.checked);
        });
    });
});

