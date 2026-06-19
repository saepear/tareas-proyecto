function animateSidebar() {
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

document.addEventListener('DOMContentLoaded', function () {

    gsap.registerPlugin(Flip, CustomEase, SplitText);

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

    function initSettings() {
        initAvatar();
        initAvatarTransform();
        setupAvatarControls();
        initDisplayName();
    }

    function initAvatar() {
        const saved = localStorage.getItem('avatar');
        const controls = document.getElementById('avatar-controls');
        if (saved) {
            document.querySelectorAll('.user-avatar').forEach(function (el) {
                el.innerHTML = '<img src="' + saved + '" alt="avatar">';
            });
            document.querySelectorAll('.avatar-preview').forEach(function (el) {
                el.innerHTML = '<img src="' + saved + '" alt="avatar">';
            });
            if (controls) controls.style.display = 'block';
        }
    }

    function initDisplayName() {
        const saved = localStorage.getItem('display_name');
        if (saved) {
            document.querySelectorAll('.user-name').forEach(function (el) {
                el.textContent = saved;
            });
        }

        const input = document.getElementById('settings-display-name');
        if (input) {
            input.value = saved || '';
        }
    }

    function initAvatarTransform() {
        const zoom = localStorage.getItem('avatar_zoom');
        const x = localStorage.getItem('avatar_x');
        const y = localStorage.getItem('avatar_y');
        if (zoom || x || y) {
            applyAvatarTransform(zoom || 100, x || 0, y || 0);
            if (document.getElementById('avatar-zoom')) document.getElementById('avatar-zoom').value = zoom || 100;
            if (document.getElementById('avatar-x')) document.getElementById('avatar-x').value = x || 0;
            if (document.getElementById('avatar-y')) document.getElementById('avatar-y').value = y || 0;
            updateZoomDisplay(zoom || 100);
        }
    }

    function applyAvatarTransform(zoom, x, y) {
        const val = 'scale(' + (zoom / 100) + ') translate(' + x + 'px, ' + y + 'px)';
        document.querySelectorAll('.user-avatar img').forEach(function (img) {
            img.style.transform = val;
        });
        document.querySelectorAll('.avatar-preview img').forEach(function (img) {
            img.style.transform = val;
        });
    }

    function setupAvatarControls() {
        const zoomInput = document.getElementById('avatar-zoom');
        const xInput = document.getElementById('avatar-x');
        const yInput = document.getElementById('avatar-y');
        if (!zoomInput) return;

        function update() {
            const z = Number.parseInt(zoomInput.value, 10);
            const x = Number.parseInt(xInput.value, 10);
            const y = Number.parseInt(yInput.value, 10);
            localStorage.setItem('avatar_zoom', z);
            localStorage.setItem('avatar_x', x);
            localStorage.setItem('avatar_y', y);
            applyAvatarTransform(z, x, y);
            updateZoomDisplay(z);
        }

        zoomInput.addEventListener('input', update);
        xInput.addEventListener('input', update);
        yInput.addEventListener('input', update);
    }

    function updateZoomDisplay(val) {
        const el = document.getElementById('zoom-value');
        if (el) el.textContent = val + '%';
    }

    window.previewAvatar = function (input) {
        if (input.files && input.files[0]) {
            const controls = document.getElementById('avatar-controls');
            if (controls) controls.style.display = 'block';

            localStorage.removeItem('avatar_zoom');
            localStorage.removeItem('avatar_x');
            localStorage.removeItem('avatar_y');
            if (document.getElementById('avatar-zoom')) document.getElementById('avatar-zoom').value = 100;
            if (document.getElementById('avatar-x')) document.getElementById('avatar-x').value = 0;
            if (document.getElementById('avatar-y')) document.getElementById('avatar-y').value = 0;
            updateZoomDisplay(100);
            applyAvatarTransform(100, 0, 0);

            const reader = new FileReader();
            reader.onload = function (e) {
                const dataUrl = e.target.result;
                localStorage.setItem('avatar', dataUrl);
                document.querySelectorAll('.user-avatar').forEach(function (el) {
                    el.innerHTML = '<img src="' + dataUrl + '" alt="avatar">';
                });
                document.querySelectorAll('.avatar-preview img').forEach(function (img) {
                    img.src = dataUrl;
                });
                document.querySelectorAll('.avatar-preview').forEach(function (el) {
                    el.innerHTML = '<img src="' + dataUrl + '" alt="avatar">';
                });
                applyAvatarTransform(100, 0, 0);
            };
            reader.readAsDataURL(input.files[0]);
        }
    };

    window.saveSettings = function () {
        const displayName = document.getElementById('settings-display-name').value.trim();

        if (displayName) {
            localStorage.setItem('display_name', displayName);
            document.querySelectorAll('.user-name').forEach(function (el) {
                el.textContent = displayName;
            });
        }

        const token = document.querySelector('meta[name="csrf-token"]');
        fetch('/settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token ? token.getAttribute('content') : ''
            },
            body: JSON.stringify({ display_name: displayName })
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            prettyModal.close('modal-settings');
        })
        .catch(function () {
            prettyModal.close('modal-settings');
        });
    };

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
            var title = card.querySelector('.task-card-title');
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

            gsap.to(card, {
                opacity: isChecked ? 0.6 : 1,
                duration: 0.3,
                ease: 'power2.out'
            });

            title.classList.toggle('completed', isChecked);

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
                    title.classList.toggle('completed', !isChecked);
                    gsap.to(card, { opacity: 1, duration: 0.3 });
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

        if (isOpen) {
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            gsap.to(submenu, {
                maxHeight: 0,
                duration: 0.3,
                ease: 'power2.out',
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
                    maxHeight: submenu.scrollHeight,
                    duration: 0.3,
                    ease: 'power2.out',
                    onComplete: function () {
                        submenu.style.maxHeight = '';
                    }
                }
            );
        }

        if (chevron) chevron.classList.toggle('open');
    };

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
    }

    function initAjaxFilters() {
        document.querySelectorAll('[data-filter="true"]').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var url = this.href;

                document.querySelectorAll('.nav-sub-priority').forEach(function (l) {
                    l.classList.remove('active');
                });
                this.classList.add('active');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) { return r.text(); })
                    .then(function (html) {
                        var container = document.getElementById('tasks-container');
                        if (container) container.innerHTML = html;
                        history.pushState({}, '', url);
                    });
            });
        });
    }

    initAjaxFilters();
});
