<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Task Manager</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <script>(function(){var p=window.matchMedia('(prefers-color-scheme:dark)').matches;if(p)document.documentElement.classList.add('dark');var c=localStorage.getItem('settingsSelectedColor');if(c){var m={1:'ocean',2:'sunset',3:'forest',4:'lavender',5:'rose',6:'amber',7:'slate',8:'teal',9:'berry',10:'sky'};document.documentElement.setAttribute('data-theme',m[c]||c)}var n=localStorage.getItem('settingsSelectedNavStyle');if(n&&n!=='none')document.documentElement.setAttribute('data-nav-style',n);})();</script>
    <style>body{background:#e0e5ec;margin:0}html.dark body{background:#1c1e28}</style>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom-themes.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .error-message {
            color: var(--danger);
            font-size: 13px;
            font-weight: 500;
            margin-top: 8px;
            margin-left: 20px;
            display: block;
        }
        .invalid-input {
            box-shadow: 
                inset 8px 8px 16px var(--shadow-danger),
                inset -8px -8px 16px var(--shadow-light),
                0 0 0 2px var(--danger) !important;
        }
        .palette-wrapper-float {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
        }
        .palette-float-trigger {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--bg);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            box-shadow: 4px 4px 10px var(--shadow-dark), -4px -4px 10px var(--shadow-light);
            transition: all 0.3s ease;
        }
        .palette-float-trigger:hover {
            color: var(--text-primary);
            box-shadow: 6px 6px 14px var(--shadow-dark), -6px -6px 14px var(--shadow-light);
        }
        .palette-float-trigger:active {
            box-shadow: inset 2px 2px 5px var(--shadow-dark), inset -2px -2px 5px var(--shadow-light);
        }
        .palette-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            padding: 12px;
            background: var(--bg);
            border-radius: 16px;
            box-shadow: 8px 8px 24px var(--shadow-dark), -8px -8px 24px var(--shadow-light);
            z-index: 1000;
            max-width: 196px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-4px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
        }
        .palette-dropdown.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .palette-dropdown .palette-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid transparent;
            cursor: pointer;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .palette-dropdown .palette-dot:hover {
            transform: scale(1.25);
            z-index: 2;
        }
        .palette-dropdown .palette-dot.active {
            border-color: var(--text-primary);
            border-radius: 8px;
            transform: scale(1.15);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="neu-icon">
                    <div class="icon-inner">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;border-radius:50%;">
                    </div>
                </div>
                <h2>Crear Cuenta</h2>
                <p>Regístrate para empezar a usar Task Manager</p>
            </div>
            
            <form class="login-form" method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="form-group">
                    <div class="input-group neu-input @error('first_name') invalid-input @enderror">
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required placeholder=" ">
                        <label for="first_name">Nombres</label>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>
                    @error('first_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group neu-input @error('last_name') invalid-input @enderror">
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required placeholder=" ">
                        <label for="last_name">Apellidos</label>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    </div>
                    @error('last_name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group neu-input @error('email') invalid-input @enderror">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder=" ">
                        <label for="email">Correo Electrónico</label>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group neu-input password-group @error('password') invalid-input @enderror">
                        <input type="password" id="password" name="password" required autocomplete="new-password" placeholder=" ">
                        <label for="password">Contraseña</label>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </div>
                        <button type="button" class="password-toggle neu-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                            <svg class="eye-open" style="display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-closed" style="display: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="input-group neu-input password-group">
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder=" ">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <div class="input-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </div>
                        <button type="button" class="password-toggle neu-toggle" id="passwordConfirmToggle" aria-label="Toggle password visibility">
                            <svg class="eye-open-confirm" style="display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-closed-confirm" style="display: none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="neu-button login-btn">
                    <span class="btn-text">Registrarse</span>
                </button>
            </form>

            <div class="signup-link" style="margin-top: 20px;">
                <p>¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
                <p style="margin-top: 10px;"><a href="{{ url('/') }}">Volver al Inicio</a></p>
            </div>
        </div>
    </div>

    <div class="bg-bubbles">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
    </div>

    <div class="palette-wrapper-float">
        <button class="palette-float-trigger" id="paletteTrigger" aria-label="Seleccionar paleta de colores">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:22px;height:22px">
                <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="23"/><line x1="1" y1="12" x2="23" y2="12"/>
                <path d="M5.64 18.36a9 9 0 1 0 12.72-12.72"/>
            </svg>
        </button>
        <div class="palette-dropdown" id="paletteDropdown">
            <button class="palette-dot" data-idx="1" style="background:var(--palette-1)" aria-label="Océano"></button>
            <button class="palette-dot" data-idx="2" style="background:var(--palette-2)" aria-label="Atardecer"></button>
            <button class="palette-dot" data-idx="3" style="background:var(--palette-3)" aria-label="Bosque"></button>
            <button class="palette-dot" data-idx="4" style="background:var(--palette-4)" aria-label="Lavanda"></button>
            <button class="palette-dot" data-idx="5" style="background:var(--palette-5)" aria-label="Rosa"></button>
            <button class="palette-dot" data-idx="6" style="background:var(--palette-6)" aria-label="Ámbar"></button>
            <button class="palette-dot" data-idx="7" style="background:var(--palette-7)" aria-label="Pizarra"></button>
            <button class="palette-dot" data-idx="8" style="background:var(--palette-8)" aria-label="Verde Agua"></button>
            <button class="palette-dot" data-idx="9" style="background:var(--palette-9)" aria-label="Baya"></button>
            <button class="palette-dot" data-idx="10" style="background:var(--palette-10)" aria-label="Cielo"></button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js" integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly" crossorigin="anonymous"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script>
        (function() {
            var lightColors = {1:'#4a8fe0',2:'#e8b830',3:'#00c896',4:'#9b6bff',5:'#ff6b81',6:'#ff9f43',7:'#6c7293',8:'#2ed8a0',9:'#e84393',10:'#54a0ff'};
            var darkColors  = {1:'#6aafe8',2:'#f0c850',3:'#2ed8a0',4:'#b084ff',5:'#ff6b81',6:'#ffb347',7:'#a8abb8',8:'#4ae0b0',9:'#f06292',10:'#74b9ff'};
            var map = {1:'ocean',2:'sunset',3:'forest',4:'lavender',5:'rose',6:'amber',7:'slate',8:'teal',9:'berry',10:'sky'};

            function applyBubbleColor(idx) {
                var isDark = document.documentElement.classList.contains('dark');
                var color = (isDark ? darkColors : lightColors)[idx] || '#4a8fe0';
                document.querySelectorAll('.bubble').forEach(function(b) { b.style.background = color; });
            }

            var current = localStorage.getItem('settingsSelectedColor');
            var trigger = document.getElementById('paletteTrigger');
            var dropdown = document.getElementById('paletteDropdown');

            if (current) applyBubbleColor(current);

            function selectPalette(idx) {
                localStorage.setItem('settingsSelectedColor', idx);
                document.documentElement.setAttribute('data-theme', map[idx] || idx);
                applyBubbleColor(idx);
                dropdown.querySelectorAll('.palette-dot').forEach(function(d) { d.classList.remove('active'); });
                var active = dropdown.querySelector('.palette-dot[data-idx="' + idx + '"]');
                if (active) active.classList.add('active');
                dropdown.classList.remove('open');
            }

            dropdown.querySelectorAll('.palette-dot').forEach(function(dot) {
                if (dot.dataset.idx === current) dot.classList.add('active');
                dot.addEventListener('click', function() { selectPalette(this.dataset.idx); });
            });

            if (trigger) {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('open');
                });
            }

            document.addEventListener('click', function(e) {
                if (dropdown && dropdown.classList.contains('open') && !dropdown.contains(e.target) && e.target !== trigger) {
                    dropdown.classList.remove('open');
                }
            });
        })();

        // Password fields toggles
        const setupToggle = (toggleBtnId, inputId, eyeOpenCls, eyeClosedCls) => {
            const toggleBtn = document.querySelector(toggleBtnId);
            const input = document.querySelector(inputId);
            const eyeOpen = document.querySelector(eyeOpenCls);
            const eyeClosed = document.querySelector(eyeClosedCls);
            
            if(toggleBtn) {
                toggleBtn.addEventListener('click', function (e) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    if (type === 'password') {
                        eyeOpen.style.display = 'block';
                        eyeClosed.style.display = 'none';
                    } else {
                        eyeOpen.style.display = 'none';
                        eyeClosed.style.display = 'block';
                    }
                });
            }
        };

        setupToggle('#passwordToggle', '#password', '.eye-open', '.eye-closed');
        setupToggle('#passwordConfirmToggle', '#password_confirmation', '.eye-open-confirm', '.eye-closed-confirm');
    </script>
</body>
</html>