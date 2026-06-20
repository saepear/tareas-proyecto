function animateSidebar() {
    if (window.__sidebarRestored) return;

    gsap.from('.sidebar', {
        scale: 0.98,
        duration: 0.4,
        ease: 'power2.out',
        transformOrigin: 'center center'
    });

    gsap.from('.nav-item', {
        y: -8,
        duration: 0.3,
        stagger: 0.04,
        ease: 'power2.out',
        delay: 0.15
    });

    gsap.from('.user-info', {
        opacity: 0,
        y: 8,
        duration: 0.3,
        delay: 0.3
    });
}

function saveSidebarState() {
    var submenu = document.getElementById('tasks-submenu');
    if (!submenu) return;
    var state = {
        tasksOpen: submenu.classList.contains('open'),
        statuses: [],
        scrollTop: document.querySelector('.sidebar')?.scrollTop || 0
    };
    document.querySelectorAll('.nav-sub-status.open').forEach(function (el) {
        var label = el.querySelector('.nav-sub-header span');
        if (label) state.statuses.push(label.textContent);
    });
    try { sessionStorage.setItem('sidebarState', JSON.stringify(state)); } catch (e) {}
}

window.showToast = function (message, type) {
    if (!type) type = 'success';

    var container = document.getElementById('toast-container');
    if (!container) return;

    var icons = {
        success: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>',
        error: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        warning: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
        info: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
    };

    if (!icons[type]) type = 'success';

    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type;
    toast.innerHTML =
        '<div class="toast-icon">' + icons[type] + '</div>' +
        '<div class="toast-message">' + message + '</div>' +
        '<button class="toast-close" aria-label="Cerrar">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
        '</button>';

    container.appendChild(toast);

    var dismissTimer = setTimeout(function () {
        dismissToast(toast);
    }, 4000);
    var timerPaused = false;

    toast.addEventListener('mouseenter', function () {
        timerPaused = true;
        clearTimeout(dismissTimer);
    });

    toast.addEventListener('mouseleave', function () {
        timerPaused = false;
        dismissTimer = setTimeout(function () {
            dismissToast(toast);
        }, 4000);
    });

    toast.querySelector('.toast-close').addEventListener('click', function () {
        clearTimeout(dismissTimer);
        dismissToast(toast);
    });

    gsap.fromTo(toast,
        { opacity: 0, x: 100 },
        { opacity: 1, x: 0, duration: 0.4, ease: 'back.out(1.5)' }
    );

    function dismissToast(el) {
        gsap.to(el, {
            x: 80,
            opacity: 0,
            duration: 0.3,
            ease: 'power2.in',
            onComplete: function () {
                if (el.parentNode) el.parentNode.removeChild(el);
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', function () {

    gsap.registerPlugin(Flip, CustomEase, SplitText);

    window.addEventListener('beforeunload', saveSidebarState);

    initSettings();
    animateSidebar();
    animatePageTitle();
    preventSamePageNav();
    initChartTransitions();
    initDelegatedCheckboxes();

    if (document.querySelector('.stat-card')) {
        animateStats();
    }

    function initChartTransitions() {
        var chartCards = document.querySelectorAll('.chart-card');
        if (!chartCards.length) return;

        setTimeout(function () {
            chartCards.forEach(function (card) {
                card.classList.add('chart-ready');
            });

            document.querySelectorAll('.chart-empty-content').forEach(function (el) {
                gsap.from(el, {
                    y: 16,
                    opacity: 0,
                    duration: 0.5,
                    ease: 'power2.out',
                    delay: 0.15,
                    clearProps: 'all'
                });
            });
        }, 400);
    }

    var cropperInstance = null;

    function initSettings() {
        initAvatar();
        initDisplayName();
        initCropTrigger();
    }

    function initAvatar() {
        var saved = localStorage.getItem('avatar');
        if (saved) {
            document.querySelectorAll('.user-avatar, .settings-avatar').forEach(function (el) {
                el.innerHTML = '<img src="' + saved + '" alt="avatar">';
            });
        }
    }

    function initDisplayName() {
        var input = document.getElementById('settings-display-name');
        if (!input) return;
        var currentName = document.querySelector('.user-name');
        input.value = currentName ? currentName.textContent.trim() : '';
    }

    function initCropTrigger() {
        var trigger = document.getElementById('settings-avatar-trigger');
        var input = document.getElementById('avatar-input');
        if (!trigger || !input) return;
        trigger.addEventListener('click', function () { input.click(); });
        input.addEventListener('change', function () {
            if (input.files && input.files[0]) openCropModal(input.files[0]);
            input.value = '';
        });
    }

    function openCropModal(file) {
        var modal = document.getElementById('avatar-pretty-modal');
        if (!modal) return;

        modal.style.display = 'flex';

        var panel = modal.querySelector('.pretty-panel');
        gsap.set(panel, { opacity: 0, scale: 0.2 });
        gsap.to(panel, { opacity: 1, scale: 1, duration: 0.4, ease: 'back.out(1.7)' });

        var reader = new FileReader();
        reader.onload = function (e) {
            var img = document.getElementById('crop-image');
            img.src = e.target.result;

            img.onload = function () {
                if (cropperInstance) cropperInstance.destroy();
                cropperInstance = new Cropper(img, {
                    viewMode: 1,
                    dragMode: 'move',
                    aspectRatio: 1,
                    autoCropArea: 1,
                    cropBoxMovable: false,
                    cropBoxResizable: false,
                    background: false,
                    modal: false,
                    guides: false,
                    center: false,
                    highlight: false,
                });

                img.addEventListener('wheel', function (e) {
                    e.preventDefault();
                    if (!cropperInstance) return;
                    var delta = e.deltaY > 0 ? -0.05 : 0.05;
                    cropperInstance.zoom(delta);
                }, { passive: false });
            };
        };
        reader.readAsDataURL(file);
    }

    window.closeCropModal = function () {
        var modal = document.getElementById('avatar-pretty-modal');
        if (!modal) return;
        var panel = modal.querySelector('.pretty-panel');
        gsap.to(panel, {
            opacity: 0, scale: 0.85, duration: 0.25, ease: 'power2.in',
            onComplete: function () {
                modal.style.display = 'none';
                if (cropperInstance) { cropperInstance.destroy(); cropperInstance = null; }
            }
        });
    };

    window.applyCrop = function () {
        if (!cropperInstance) return;
        var canvas = cropperInstance.getCroppedCanvas({ width: 256, height: 256 });
        var dataUrl = canvas.toDataURL('image/jpeg', 0.92);
        localStorage.setItem('avatar', dataUrl);
        document.querySelectorAll('.user-avatar, .settings-avatar').forEach(function (el) {
            el.innerHTML = '<img src="' + dataUrl + '" alt="avatar">';
        });
        closeCropModal();
    };

    var settingsDirty = false;
    var settingsInitialName = '';
    var settingsInputTracked = false;

    window.markSettingsDirty = function () {
        settingsDirty = true;
    };

    window.saveSettings = function () {
        var displayName = document.getElementById('settings-display-name').value.trim();
        var token = document.querySelector('meta[name="csrf-token"]');

        fetch('/settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
            },
            body: JSON.stringify({ display_name: displayName })
        })
        .then(function (r) { return r.json(); })
        .then(function () {
            document.querySelectorAll('.user-name').forEach(function (el) {
                el.textContent = displayName || el.textContent;
            });
            settingsDirty = false;
            closeSettingsModal();
        })
        .catch(function () {
            settingsDirty = false;
            closeSettingsModal();
        });
    };

    var settingsModalOpen = false;

    window.toggleSettingsModal = function () {
        if (settingsModalOpen) {
            closeSettingsModal(true);
        } else {
            openSettingsModal();
        }
    };

    function trackSettingsDirty() {
        var input = document.getElementById('settings-display-name');
        if (!input) return;
        settingsInitialName = input.value;
        settingsPreviousTheme = document.documentElement.classList.contains('dark');
        settingsDirty = false;
        if (!settingsInputTracked) {
            input.addEventListener('input', function () {
                settingsDirty = this.value !== settingsInitialName;
            });
            settingsInputTracked = true;
        }
    }

    function openSettingsModal() {
        if (settingsModalOpen) return;
        var modal = document.getElementById('settings-pretty-modal');
        var card = document.getElementById('settings-card');
        var backdrop = document.getElementById('settings-backdrop');
        if (!modal || !card) return;

        settingsModalOpen = true;
        modal.style.display = 'flex';
        trackSettingsDirty();
        if (typeof snapshotSettings === 'function') snapshotSettings();

        gsap.set(backdrop, { opacity: 0 });
        gsap.set(card, { opacity: 0, scale: 0.4, y: 60 });

        gsap.to(backdrop, { opacity: 1, duration: 0.35, ease: 'power2.out' });

        gsap.to(card, {
            opacity: 1,
            scale: 1,
            y: 0,
            duration: 0.5,
            ease: 'back.out(1.7)',
            onComplete: function () {
                if (typeof initSettingsTabs === 'function') {
                    initSettingsTabs();
                }
            }
        });
    }

    function dismissUnsavedToast() {
        var toast = document.getElementById('unsaved-toast');
        if (!toast) return;
        gsap.to(toast, {
            y: 20, opacity: 0, duration: 0.3, ease: 'power2.in',
            onComplete: function () { toast.remove(); }
        });
    }

    window.discardSettingsChanges = function () {
        dismissUnsavedToast();
        closeSettingsModal(true);
    };

    function showUnsavedToast() {
        var existing = document.getElementById('unsaved-toast');
        if (existing) existing.remove();

        var toast = document.createElement('div');
        toast.id = 'unsaved-toast';
        toast.className = 'unsaved-toast';
        toast.innerHTML =
            '<div class="unsaved-toast-content">' +
                '<span class="unsaved-toast-icon">!</span>' +
                '<span class="unsaved-toast-text">Tienes cambios sin guardar</span>' +
                '<button class="unsaved-toast-discard" onclick="discardSettingsChanges()">Descartar</button>' +
            '</div>';

        document.body.appendChild(toast);

        gsap.fromTo(toast,
            { y: 30, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.35, ease: 'power2.out' }
        );

        if (window.unsavedToastTimeout) clearTimeout(window.unsavedToastTimeout);
        window.unsavedToastTimeout = setTimeout(function () {
            dismissUnsavedToast();
        }, 4000);
    }

    window.closeSettingsModal = function (revert) {
        if (!settingsModalOpen) return;

        if (settingsDirty && !revert) {
            showUnsavedToast();
            return;
        }

        if (revert && settingsDirty) {
            var input = document.getElementById('settings-display-name');
            if (input) input.value = settingsInitialName;
            if (typeof revertSettings === 'function') revertSettings();
            settingsDirty = false;
        }

        var modal = document.getElementById('settings-pretty-modal');
        var card = document.getElementById('settings-card');
        var backdrop = document.getElementById('settings-backdrop');
        if (!modal || !card) return;

        settingsModalOpen = false;

        gsap.to(card, {
            opacity: 0,
            scale: 0.85,
            y: 40,
            duration: 0.25,
            ease: 'power2.in',
            onComplete: function () {
                modal.style.display = 'none';
            }
        });

        gsap.to(backdrop, {
            opacity: 0,
            duration: 0.25,
            ease: 'power2.in'
        });
    };

    document.addEventListener('click', function (e) {
        var modal = document.getElementById('settings-pretty-modal');
        if (!modal || modal.style.display === 'none') return;
        var card = document.getElementById('settings-card');
        if (card && !card.contains(e.target) && !e.target.closest('.settings-btn')) {
            closeSettingsModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && settingsModalOpen) {
            closeSettingsModal();
        }
    });

    function animatePageTitle() {
        const h1 = document.querySelector('.page-header-left h1');
        if (!h1) return;
        const split = SplitText.create(h1, { type: 'chars' });
        if (!split.chars || split.chars.length < 2) return;

        const animations = [
            function wave() {
                gsap.from(split.chars, {
                    y: 40,
                    opacity: 0,
                    duration: 0.6,
                    stagger: { each: 0.03, from: 'center' },
                    ease: 'sine.inOut'
                });
            },
            function scalePop() {
                gsap.from(split.chars, {
                    scale: 0,
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.04,
                    ease: 'back.out(2)'
                });
            },
            function letterSpacing() {
                gsap.from(split.chars, {
                    opacity: 0,
                    letterSpacing: '-0.25em',
                    duration: 0.6,
                    stagger: 0.03,
                    ease: 'power2.out'
                });
            },
            function fadeUp() {
                gsap.from(split.chars, {
                    y: 25,
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.05,
                    ease: 'power2.out'
                });
            }
        ];

        animations[Math.floor(Math.random() * animations.length)]();
    }

    function animateStats() {
        gsap.from('.stat-card', {
            scale: 0.7,
            opacity: 0,
            duration: 0.6,
            stagger: 0.1,
            ease: 'back.out(1.4)',
            delay: 0.4,
            transformOrigin: 'center center'
        });

        document.querySelectorAll('.stat-value').forEach(function (el) {
            const target = Number.parseInt(el.getAttribute('data-target')) || 0;
            const obj = { val: 0 };

            gsap.to(obj, {
                val: target,
                duration: 1.2,
                delay: 0.8,
                ease: 'power2.out',
                onUpdate: function () {
                    el.textContent = Math.round(obj.val);
                }
            });
        });
    }

    function animateTaskList() {
        gsap.from('.task-card', {
            y: 20,
            opacity: 0,
            duration: 0.5,
            stagger: 0.08,
            ease: 'power2.out',
            delay: 0.6
        });
    }

    function setupFilterTabs() {
        document.querySelectorAll('#dashboard-submenu .nav-sub-item').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#dashboard-submenu .nav-sub-item').forEach(function (b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');
                const tasks = document.querySelectorAll('.task-card');

                tasks.forEach(function (task) {
                    const status = task.getAttribute('data-status');

                    if (filter === 'all' || status === filter) {
                        gsap.to(task, {
                            opacity: 1,
                            y: 0,
                            scale: 1,
                            duration: 0.3,
                            display: 'flex',
                            ease: 'power2.out'
                        });
                    } else {
                        gsap.to(task, {
                            opacity: 0,
                            y: -10,
                            scale: 0.98,
                            duration: 0.2,
                            ease: 'power2.in',
                            onComplete: function () {
                                task.style.display = 'none';
                            }
                        });
                    }
                });
            });
        });
    }

    function initDelegatedCheckboxes() {
        var container = document.getElementById('tasks-container');
        if (!container) return;

        container.addEventListener('change', function (e) {
            var checkbox = e.target.closest('.task-check input[type="checkbox"]');
            if (!checkbox) return;

            var card = checkbox.closest('.task-card');
            var taskId = card.getAttribute('data-task-id');
            var badge = card.querySelector('.task-status');
            var due = card.querySelector('.task-due');
            var isChecked = checkbox.checked;
            var origBadgeClass = badge.className;
            var origBadgeText = badge.textContent;

            var oldStatus = card.getAttribute('data-status');
            var priority = '';
            var priorityEl = card.querySelector('.task-priority');
            if (priorityEl) {
                var classes = priorityEl.className.split(' ');
                for (var i = 0; i < classes.length; i++) {
                    if (classes[i].indexOf('priority-') === 0) {
                        priority = classes[i].replace('priority-', '');
                        break;
                    }
                }
            }

            card.classList.toggle('completed', isChecked);

            if (isChecked) {
                badge.classList.remove('pending', 'in-progress');
                badge.classList.add('completed');
                badge.textContent = 'Completada';
                if (due) due.classList.remove('overdue');
            } else {
                badge.classList.remove('completed');
                badge.classList.add('pending');
                badge.textContent = 'Pendiente';
            }

            axios.patch('/tasks/' + taskId + '/toggle')
                .then(function (response) {
                    var data = response.data;
                    badge.className = 'task-status ' + data.status;
                    badge.textContent = data.status_label;
                    card.setAttribute('data-status', data.status);
                    updateSidebarCounts(oldStatus, data.status, priority);
                })
                .catch(function () {
                    card.classList.toggle('completed', !isChecked);
                    checkbox.checked = !isChecked;
                    badge.className = origBadgeClass;
                    badge.textContent = origBadgeText;
                    var attemptedStatus = isChecked ? 'completed' : 'pending';
                    updateSidebarCounts(attemptedStatus, oldStatus, priority);
                });
        });
    }

    function updateSidebarCounts(fromStatus, toStatus, priority) {
        if (!fromStatus || !toStatus || fromStatus === toStatus) return;

        var totalEl = document.querySelector('[data-badge="total"]');
        var fromEl = document.querySelector('[data-badge="status"][data-status="' + fromStatus + '"]');
        var toEl = document.querySelector('[data-badge="status"][data-status="' + toStatus + '"]');
        var fromPEl = document.querySelector('[data-badge="priority"][data-status="' + fromStatus + '"][data-priority="' + priority + '"]');
        var toPEl = document.querySelector('[data-badge="priority"][data-status="' + toStatus + '"][data-priority="' + priority + '"]');

        if (fromEl) fromEl.textContent = Math.max(0, parseInt(fromEl.textContent, 10) - 1);
        if (toEl) toEl.textContent = parseInt(toEl.textContent, 10) + 1;
        if (fromPEl) fromPEl.textContent = Math.max(0, parseInt(fromPEl.textContent, 10) - 1);
        if (toPEl) toPEl.textContent = parseInt(toPEl.textContent, 10) + 1;
        if (totalEl) totalEl.textContent = parseInt(totalEl.textContent, 10);
    }

    window.toggleTasksSubmenu = function () {
        const submenu = document.getElementById('tasks-submenu');
        const chevron = submenu.previousElementSibling.querySelector('.chevron');
        const isOpen = submenu.classList.contains('open');
        const isDock = document.documentElement.getAttribute('data-nav-style') === 'dock';

        if (isDock) {
            if (isOpen) {
                gsap.to(submenu, {
                    scale: 0.85, opacity: 0,
                    duration: 0.2, ease: 'power2.in',
                    onComplete: function () {
                        submenu.classList.remove('open');
                        submenu.style.transform = '';
                        submenu.style.opacity = '';
                    }
                });
            } else {
                gsap.set(submenu, { scale: 0.85, opacity: 0 });
                submenu.classList.add('open');
                gsap.to(submenu, {
                    scale: 1, opacity: 1,
                    duration: 0.35, ease: 'back.out(1.7)',
                    onComplete: function () {
                        submenu.style.transform = '';
                        submenu.style.opacity = '';
                    }
                });
            }
        } else {
            if (isOpen) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
                gsap.to(submenu, {
                    maxHeight: 0, duration: 0.3, ease: 'power2.out',
                    onComplete: function () {
                        submenu.classList.remove('open');
                        submenu.style.maxHeight = '';
                    }
                });
            } else {
                submenu.classList.add('open');
                gsap.fromTo(submenu,
                    { maxHeight: 0 },
                    {
                        maxHeight: submenu.scrollHeight, duration: 0.3, ease: 'power2.out',
                        onComplete: function () {
                            submenu.style.maxHeight = '';
                        }
                    }
                );
            }
        }

        if (chevron) chevron.classList.toggle('open');
    };

    document.addEventListener('click', function (e) {
        var sub = document.getElementById('tasks-submenu');
        if (!sub || !sub.classList.contains('open')) return;
        var ns = document.documentElement.getAttribute('data-nav-style');
        if (ns !== 'dock') return;
        if (sub.contains(e.target)) return;
        if (e.target.closest('.nav-item-group')) return;
        window.toggleTasksSubmenu();
    });

    window.togglePrioritySubmenu = function (el) {
        var sub = el.nextElementSibling;
        var chevron = el.querySelector('.chevron');
        var parent = el.closest('.nav-sub-status');
        var isOpen = sub.classList.contains('open');

        if (isOpen) {
            sub.style.maxHeight = sub.scrollHeight + 'px';
            gsap.to(sub, {
                maxHeight: 0,
                duration: 0.2,
                ease: 'power2.out',
                onComplete: function () {
                    sub.classList.remove('open');
                    if (parent) parent.classList.remove('open');
                    sub.style.maxHeight = '';
                }
            });
        } else {
            sub.classList.add('open');
            if (parent) parent.classList.add('open');
            gsap.fromTo(sub,
                { maxHeight: 0 },
                {
                    maxHeight: sub.scrollHeight,
                    duration: 0.2,
                    ease: 'power2.out',
                    onComplete: function () {
                        sub.style.maxHeight = sub.scrollHeight + 'px';
                    }
                }
            );
        }

        if (chevron) chevron.classList.toggle('open');
    };

    function preventSamePageNav() {
        document.querySelectorAll('.nav-sub-item').forEach(function (el) {
            el.addEventListener('click', function (e) {
                var linkUrl = new URL(this.href);
                var currentUrl = window.location.pathname + window.location.search;
                if (linkUrl.pathname + linkUrl.search === currentUrl) {
                    e.preventDefault();
                }
            });
        });
        document.querySelector('.nav-item-dashboard').addEventListener('click', function (e) {
            if (window.location.pathname === '/dashboard' || window.location.pathname === '/dashboard/') {
                e.preventDefault();
            }
        });
    }



    function initAjaxFilters() {
        document.querySelectorAll('[data-filter="true"]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                var url = this.href;
                var container = document.getElementById('tasks-container');
                if (!container) return;

                e.preventDefault();

                document.querySelectorAll('[data-filter="true"]').forEach(function (l) {
                    l.classList.remove('active');
                });
                this.classList.add('active');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.text(); })
                    .then(function (html) {
                        container.innerHTML = html;
                        history.pushState({}, '', url);
                    });
            });
        });
    }

    initAjaxFilters();
});
