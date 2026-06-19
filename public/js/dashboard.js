function animateSidebar() {
    if (sessionStorage.getItem('sidebar_animated')) return;
    sessionStorage.setItem('sidebar_animated', '1');

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

    if (document.querySelector('.stat-card')) {
        animateStats();
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

    function setupTaskCheckboxes() {
        document.querySelectorAll('.task-check input[type="checkbox"]').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const card = this.closest('.task-card');

                gsap.to(card, {
                    opacity: this.checked ? 0.6 : 1,
                    duration: 0.3,
                    ease: 'power2.out'
                });

                card.classList.toggle('completed', this.checked);
            });
        });
    }

    window.toggleTasksSubmenu = function () {
        const submenu = document.getElementById('tasks-submenu');
        const chevron = submenu.previousElementSibling.querySelector('.chevron');
        const isOpen = submenu.classList.contains('open');

        if (isOpen) {
            gsap.to(submenu, {
                maxHeight: 0,
                duration: 0.3,
                ease: 'power2.out',
                onComplete: function () {
                    submenu.classList.remove('open');
                }
            });
        } else {
            submenu.classList.add('open');
            gsap.fromTo(submenu,
                { maxHeight: 0 },
                { maxHeight: submenu.scrollHeight, duration: 0.3, ease: 'power2.out' }
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
});
