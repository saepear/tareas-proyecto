(function () {
  var ACTIVE_TAB_KEY = 'settingsActiveTab';
  var SELECTED_COLOR_KEY = 'settingsSelectedColor';
  var SELECTED_FONT_KEY = 'settingsSelectedFont';
  var SELECTED_NAV_STYLE_KEY = 'settingsSelectedNavStyle';

  var colorThemeMap = {
    '1': 'ocean', '2': 'sunset', '3': 'forest', '4': 'lavender',
    '5': 'rose', '6': 'amber', '7': 'slate', '8': 'teal',
    '9': 'berry', '10': 'sky'
  };

  var fontFamilyMap = {
    'moderna': 'Inter, -apple-system, BlinkMacSystemFont, sans-serif',
    'tech': 'JetBrains Mono, Cascadia Code, Fira Code, monospace',
    'elegante': 'Playfair Display, Georgia, serif',
    'clasica': 'Georgia, Times New Roman, serif'
  };

  var _inited = false;

  /* Snapshot captured when modal opens, for revert on cancel */
  var _snapshot = {};

  function loadString(key) {
    try { return localStorage.getItem(key); } catch (e) { return null; }
  }

  function saveString(key, val) {
    try {
      if (val === null || val === undefined) {
        localStorage.removeItem(key);
      } else {
        localStorage.setItem(key, val);
      }
    } catch (e) {}
  }

  /* ------- html[data-theme] helpers ------- */
  function setColorTheme(palette) {
    var name = colorThemeMap[palette] || palette;
    document.documentElement.setAttribute('data-theme', name);
    document.body.classList.add('theme-' + name);
    if (typeof applyBubbleColor === 'function') applyBubbleColor(palette);
  }

  function clearColorTheme() {
    document.documentElement.removeAttribute('data-theme');
    Object.values(colorThemeMap).forEach(function (c) {
      document.body.classList.remove('theme-' + c);
    });
  }

  /* ------- html[data-nav-style] helpers ------- */
  function setNavStyle(style) {
    if (style && style !== 'none') {
      document.documentElement.setAttribute('data-nav-style', style);
    } else {
      document.documentElement.removeAttribute('data-nav-style');
    }
  }

  /* ------- Font helpers ------- */
  function applySavedFont(font) {
    if (font && fontFamilyMap[font]) {
      document.body.style.fontFamily = fontFamilyMap[font];
    } else {
      document.body.style.fontFamily = '';
    }
  }

  /* ------- Snapshot / Revert (for unsaved-changes) ------- */
  window.snapshotSettings = function () {
    _snapshot = {
      color: loadString(SELECTED_COLOR_KEY),
      font: loadString(SELECTED_FONT_KEY),
      navStyle: loadString(SELECTED_NAV_STYLE_KEY)
    };
  };

  window.revertSettings = function () {
    /* Revert color */
    if (_snapshot.color) {
      setColorTheme(_snapshot.color);
      saveString(SELECTED_COLOR_KEY, _snapshot.color);
    } else {
      clearColorTheme();
      saveString(SELECTED_COLOR_KEY, null);
    }
    /* Revert font */
    applySavedFont(_snapshot.font);
    saveString(SELECTED_FONT_KEY, _snapshot.font);
    /* Revert nav style */
    setNavStyle(_snapshot.navStyle);
    saveString(SELECTED_NAV_STYLE_KEY, _snapshot.navStyle);
  };

  /* ------- State restoration on modal open ------- */
  function restoreState() {
    var savedTab = loadString(ACTIVE_TAB_KEY) || 'apariencia';
    var savedColor = loadString(SELECTED_COLOR_KEY);
    var savedFont = loadString(SELECTED_FONT_KEY);
    var savedNavStyle = loadString(SELECTED_NAV_STYLE_KEY);

    applySavedFont(savedFont);
    setNavStyle(savedNavStyle);

    var nav = document.getElementById('settings-nav');
    if (!nav) return;
    var tabBtns = nav.querySelectorAll('.settings-nav-btn');
    var panels = document.querySelectorAll('.settings-tab-panel');

    tabBtns.forEach(function (b) {
      b.classList.toggle('active', b.getAttribute('data-tab') === savedTab);
    });
    panels.forEach(function (p) {
      p.classList.toggle('active', p.getAttribute('data-panel') === savedTab);
    });

    /* Restore saved color swatch */
    if (savedColor) {
      var sw = document.querySelector('.color-swatch[data-palette="' + savedColor + '"]');
      if (sw) {
        sw.classList.add('active');
        gsap.set(sw, { borderRadius: '12px', scale: 1.12 });
        setColorTheme(savedColor);
      }
    }

    /* Restore saved font button */
    if (savedFont) {
      var fb = document.querySelector('.font-option-btn[data-font="' + savedFont + '"]');
      if (fb) fb.classList.add('active');
    }

    /* Restore saved nav style button */
    if (savedNavStyle) {
      var nsb = document.querySelector('.nav-style-btn[data-nav-style="' + savedNavStyle + '"]');
      if (nsb) nsb.classList.add('active');
    }
  }

  /* ------- One-time event listeners ------- */
  function setupListeners() {
    var nav = document.getElementById('settings-nav');
    if (!nav) return;

    var content = document.getElementById('settings-content');
    if (!content) return;

    /* Tab clicks (delegated) */
    nav.addEventListener('click', function (e) {
      var btn = e.target.closest('.settings-nav-btn');
      if (!btn) return;
      var tab = btn.getAttribute('data-tab');
      var currentTabEl = nav.querySelector('.settings-nav-btn.active');
      if (currentTabEl && currentTabEl.getAttribute('data-tab') === tab) return;

      var tabBtns = nav.querySelectorAll('.settings-nav-btn');
      tabBtns.forEach(function (b) {
        b.classList.toggle('active', b.getAttribute('data-tab') === tab);
      });
      saveString(ACTIVE_TAB_KEY, tab);

      var prevPanel = content.querySelector('.settings-tab-panel.active');
      var nextPanel = content.querySelector('[data-panel="' + tab + '"]');
      if (!nextPanel) return;

      if (prevPanel) {
        gsap.to(prevPanel, {
          opacity: 0, y: -6, duration: 0.12, ease: 'power2.in',
          onComplete: function () {
            prevPanel.classList.remove('active');
            prevPanel.style.opacity = '';
            prevPanel.style.transform = '';
            nextPanel.classList.add('active');
            gsap.fromTo(nextPanel,
              { opacity: 0, y: 10 },
              { opacity: 1, y: 0, duration: 0.3, ease: 'power2.out' }
            );
          }
        });
      } else {
        nextPanel.classList.add('active');
        gsap.fromTo(nextPanel,
          { opacity: 0, y: 10 },
          { opacity: 1, y: 0, duration: 0.3, ease: 'power2.out' }
        );
      }
    });

    /* Color swatches */
    document.querySelectorAll('.color-swatch').forEach(function (sw) {
      sw.addEventListener('click', function () {
        var palette = this.getAttribute('data-palette');

        if (this.classList.contains('active')) {
          this.classList.remove('active');
          gsap.to(this, { borderRadius: '50%', scale: 1, duration: 0.25, ease: 'power2.out' });
          clearColorTheme();
          saveString(SELECTED_COLOR_KEY, null);
        } else {
          document.querySelectorAll('.color-swatch').forEach(function (s) {
            if (s.classList.contains('active')) {
              s.classList.remove('active');
              gsap.to(s, { borderRadius: '50%', scale: 1, duration: 0.2, ease: 'power2.out' });
            }
          });
          this.classList.add('active');
          gsap.to(this, { borderRadius: '12px', scale: 1.12, duration: 0.35, ease: 'back.out(1.7)' });
          clearColorTheme();
          setColorTheme(palette);
          saveString(SELECTED_COLOR_KEY, palette);
        }

        if (typeof window.markSettingsDirty === 'function') {
          window.markSettingsDirty();
        }
      });
    });

    /* Font buttons */
    document.querySelectorAll('.font-option-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var font = this.getAttribute('data-font');
        document.querySelectorAll('.font-option-btn').forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        applySavedFont(font);
        saveString(SELECTED_FONT_KEY, font);

        if (typeof window.markSettingsDirty === 'function') {
          window.markSettingsDirty();
        }
      });
    });

    /* Nav style buttons */
    document.querySelectorAll('.nav-style-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var style = this.getAttribute('data-nav-style');
        document.querySelectorAll('.nav-style-btn').forEach(function (b) { b.classList.remove('active'); });
        this.classList.add('active');
        setNavStyle(style);
        saveString(SELECTED_NAV_STYLE_KEY, style);

        if (typeof window.markSettingsDirty === 'function') {
          window.markSettingsDirty();
        }
      });
    });
  }

  window.initSettingsTabs = function () {
    if (!_inited) {
      setupListeners();
      _inited = true;
    }
    restoreState();
  };
})();
