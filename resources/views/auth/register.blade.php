<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Task Manager</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark');})();</script>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
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
        .theme-toggle-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 999;
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
        .theme-toggle-btn:hover {
            color: var(--text-primary);
            box-shadow: 6px 6px 14px var(--shadow-dark), -6px -6px 14px var(--shadow-light);
        }
        .theme-toggle-btn:active {
            box-shadow: inset 2px 2px 5px var(--shadow-dark), inset -2px -2px 5px var(--shadow-light);
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

    <button class="theme-toggle-btn" onclick="toggleTheme(document.body.classList.contains('dark') ? 'light' : 'dark')">
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px">
            <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
            <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
            <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
            <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px;display:none">
            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js" integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly" crossorigin="anonymous"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script>
        initTheme();

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