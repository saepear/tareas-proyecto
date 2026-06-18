function initTheme() {
    var saved = localStorage.getItem('theme');
    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (saved === 'dark' || (!saved && prefersDark)) {
        document.documentElement.classList.add('dark');
        if (document.body) document.body.classList.add('dark');
    }
    updateThemeIcons();
}

function updateThemeIcons() {
    var isDark = document.documentElement.classList.contains('dark');
    document.querySelectorAll('.theme-option').forEach(function (opt) {
        var mode = opt.getAttribute('data-mode');
        if (mode === (isDark ? 'dark' : 'light')) {
            opt.classList.add('active');
        } else {
            opt.classList.remove('active');
        }
    });
}

window.toggleTheme = function (mode) {
    var isDark = mode === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    document.body.classList.toggle('dark', isDark);
    localStorage.setItem('theme', isDark ? 'dark' : 'light');

    var toggleBtn = event && event.currentTarget;

    if (typeof gsap !== 'undefined') {
        var x = toggleBtn
            ? toggleBtn.getBoundingClientRect().left + toggleBtn.offsetWidth / 2
            : window.innerWidth / 2;
        var y = toggleBtn
            ? toggleBtn.getBoundingClientRect().top + toggleBtn.offsetHeight / 2
            : window.innerHeight / 2;

        var overlay = document.createElement('div');
        overlay.className = 'theme-overlay';
        document.body.appendChild(overlay);

        gsap.fromTo(overlay,
            { clipPath: 'circle(0% at ' + x + 'px ' + y + 'px)' },
            {
                clipPath: 'circle(150% at ' + x + 'px ' + y + 'px)',
                duration: 0.8,
                ease: 'power4.inOut',
                onComplete: function () {
                    overlay.remove();
                }
            }
        );

        var mainContent = document.querySelector('.main-content');
        if (mainContent) {
            gsap.fromTo(mainContent,
                { filter: 'blur(4px)', scale: 0.98 },
                { filter: 'blur(0px)', scale: 1, duration: 0.5, ease: 'power2.out', delay: 0.3 }
            );
        }
    }

    updateThemeIcons();

    if (typeof updateChartTheme === 'function') {
        updateChartTheme();
    }
};
