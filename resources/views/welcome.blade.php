<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager — Organiza tu trabajo</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark');})();</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        body {
            font-family: -apple-system, 'Inter', BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
            display: block;
            padding: 0;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: var(--bg);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            box-shadow: 
                0px 10px 20px -10px var(--shadow-dark),
                0px -10px 20px -10px var(--shadow-light);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .auth-links {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .auth-links a {
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .nav-login {
            color: var(--text-secondary);
        }

        .nav-login:hover {
            color: var(--text-primary);
        }

        .btn-register-nav {
            padding: 10px 24px;
            border-radius: 12px;
            color: var(--text-primary);
            background: var(--bg);
            box-shadow: 
                4px 4px 10px var(--shadow-dark),
                -4px -4px 10px var(--shadow-light);
        }

        .btn-register-nav:hover {
            transform: translateY(-2px);
            box-shadow: 
                6px 6px 12px var(--shadow-dark),
                -6px -6px 12px var(--shadow-light);
        }

        .btn-register-nav:active {
            transform: translateY(0);
            box-shadow: 
                inset 4px 4px 10px var(--shadow-dark),
                inset -4px -4px 10px var(--shadow-light);
        }

        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
            padding-top: 100px;
        }

        .hero-card {
            background: var(--bg);
            border-radius: 30px;
            padding: 60px 40px;
            box-shadow: 
                20px 20px 60px var(--shadow-dark),
                -20px -20px 60px var(--shadow-light);
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }

        .neu-icon-large {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: var(--bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                10px 10px 25px var(--shadow-dark),
                -10px -10px 25px var(--shadow-light),
                inset 0 0 0 var(--shadow-dark),
                inset 0 0 0 var(--shadow-light);
            color: var(--text-primary);
        }

        .neu-icon-large svg {
            width: 50px;
            height: 50px;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.15rem);
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.8;
        }

        .hero-controls {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-large {
            padding: 18px 40px;
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            background: var(--bg);
            color: var(--text-primary);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .btn-primary {
            box-shadow: 
                8px 8px 20px var(--shadow-dark),
                -8px -8px 20px var(--shadow-light);
            color: var(--text-primary);
        }

        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 
                12px 12px 30px var(--shadow-dark),
                -12px -12px 30px var(--shadow-light);
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 
                inset 6px 6px 15px var(--shadow-dark),
                inset -6px -6px 15px var(--shadow-light);
        }

        .btn-secondary {
            box-shadow: 
                inset 6px 6px 15px var(--shadow-dark),
                inset -6px -6px 15px var(--shadow-light);
            color: var(--text-secondary);
        }

        .btn-secondary:hover {
            color: var(--text-primary);
            box-shadow: 
                inset 8px 8px 20px var(--shadow-dark),
                inset -8px -8px 20px var(--shadow-light);
        }

        .features {
            padding: 80px 5%;
            max-width: 1200px;
            margin: 0 auto 80px;
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 50px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: var(--bg);
            border-radius: 25px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 
                15px 15px 40px var(--shadow-dark),
                -15px -15px 40px var(--shadow-light);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                20px 20px 50px var(--shadow-dark),
                -20px -20px 50px var(--shadow-light);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 25px;
            background: var(--bg);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                inset 6px 6px 15px var(--shadow-dark),
                inset -6px -6px 15px var(--shadow-light);
            color: var(--text-primary);
        }

        .feature-icon svg {
            width: 30px;
            height: 30px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .theme-nav-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--bg);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            box-shadow: 3px 3px 8px var(--shadow-dark), -3px -3px 8px var(--shadow-light);
            transition: all 0.3s ease;
        }
        .theme-nav-btn:hover {
            color: var(--text-primary);
            box-shadow: 5px 5px 12px var(--shadow-dark), -5px -5px 12px var(--shadow-light);
        }
        .theme-nav-btn:active {
            box-shadow: inset 2px 2px 5px var(--shadow-dark), inset -2px -2px 5px var(--shadow-light);
        }

        @media (max-width: 768px) {
            .hero-card {
                padding: 40px 20px;
                border-radius: 20px;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .hero-controls {
                flex-direction: column;
                width: 100%;
            }
            .hero-controls .btn-large {
                width: 100%;
            }
            .auth-links a.nav-login {
                display: none;
            }
        }
    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <div class="neu-icon" style="width: 40px; height: 40px; margin: 0; box-shadow: 4px 4px 10px var(--shadow-dark), -4px -4px 10px var(--shadow-light);">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:22px;height:22px;object-fit:contain;border-radius:50%;">
            </div>
            TaskManager
        </div>
        <div class="auth-links">
            <button class="theme-nav-btn" onclick="toggleTheme(document.body.classList.contains('dark') ? 'light' : 'dark')">
                <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px">
                    <circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;display:none">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
            <a href="{{ route('login') }}" class="nav-login">Iniciar Sesión</a>
            <a href="{{ route('register') }}" class="btn-register-nav">Comenzar Gratis</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-card">
            <div class="neu-icon-large">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:60px;height:60px;object-fit:contain;border-radius:50%;">
            </div>
            <h1>
                La manera más <br>
                inteligente de organizar <br>
                tus proyectos.
            </h1>
            <p>
                Gestiona tu tiempo, colabora con tu equipo y alcanza tus objetivos más rápido con nuestro sistema intuitivo de creación y gestión de tareas cotidianas. Todo con una interfaz limpia y relajante.
            </p>
            <div class="hero-controls">
                <a href="{{ route('register') }}" class="btn-large btn-primary">Empieza Ahora</a>
                <a href="{{ route('login') }}" class="btn-large btn-secondary">Ingresar a tu Cuenta</a>
            </div>
        </div>
    </section>

    <section class="features">
        <h2 class="section-title">Todo lo que necesitas</h2>
        <div class="grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <h3>Gestión Eficiente</h3>
                <p>Crea, prioriza y organiza tus tareas en segundos. Mantén un control visual de todo tu flujo de trabajo sin complicaciones.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </div>
                <h3>Seguimiento Rápido</h3>
                <p>Supervisa el progreso de tus tareas en tiempo real. Nunca pierdas de vista la fecha límite o el estado de tus pendientes.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h3>Diseño Moderno</h3>
                <p>Disfruta de una interfaz neumorfista sin distracciones, con sombras suaves que reducen la fatiga visual al trabajar por horas.</p>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js" integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly" crossorigin="anonymous"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script>initTheme();</script>
</body>
</html>
