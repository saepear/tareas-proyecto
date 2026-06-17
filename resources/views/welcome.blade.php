<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Organiza tu trabajo</title>
    <!-- Incluimos la fuente de login y sus utilidades básicas -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        /* Ajustes específicos de Neumorfismo para la Landing Page */
        body {
            font-family: -apple-system, 'Inter', BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #e0e5ec;
            min-height: 100vh;
            color: #3d4468;
            overflow-x: hidden;
            display: block; /* Sobrescribir el flex del login */
            padding: 0;
        }

        /* Navbar Neumorfista */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: #e0e5ec;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 100;
            box-shadow: 
                0px 10px 20px -10px #bec3cf,
                0px -10px 20px -10px #ffffff;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #3d4468;
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
            color: #6c7293;
        }

        .nav-login:hover {
            color: #3d4468;
        }

        .btn-register-nav {
            padding: 10px 24px;
            border-radius: 12px;
            color: #3d4468;
            background: #e0e5ec;
            box-shadow: 
                4px 4px 10px #bec3cf,
                -4px -4px 10px #ffffff;
        }

        .btn-register-nav:hover {
            transform: translateY(-2px);
            box-shadow: 
                6px 6px 12px #bec3cf,
                -6px -6px 12px #ffffff;
        }

        .btn-register-nav:active {
            transform: translateY(0);
            box-shadow: 
                inset 4px 4px 10px #bec3cf,
                inset -4px -4px 10px #ffffff;
        }

        /* Hero Section */
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
            background: #e0e5ec;
            border-radius: 30px;
            padding: 60px 40px;
            box-shadow: 
                20px 20px 60px #bec3cf,
                -20px -20px 60px #ffffff;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }

        .neu-icon-large {
            width: 100px;
            height: 100px;
            margin: 0 auto 30px;
            background: #e0e5ec;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                10px 10px 25px #bec3cf,
                -10px -10px 25px #ffffff,
                inset 0 0 0 #bec3cf,
                inset 0 0 0 #ffffff;
            color: #3d4468;
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
            color: #3d4468;
        }

        .hero p {
            font-size: clamp(1rem, 2vw, 1.15rem);
            color: #6c7293;
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
            background: #e0e5ec;
            color: #3d4468;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .btn-primary {
            box-shadow: 
                8px 8px 20px #bec3cf,
                -8px -8px 20px #ffffff;
            color: #3d4468;
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
                12px 12px 30px #bec3cf,
                -12px -12px 30px #ffffff;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 
                inset 6px 6px 15px #bec3cf,
                inset -6px -6px 15px #ffffff;
        }

        .btn-secondary {
            box-shadow: 
                inset 6px 6px 15px #bec3cf,
                inset -6px -6px 15px #ffffff;
            color: #6c7293;
        }

        .btn-secondary:hover {
            color: #3d4468;
            box-shadow: 
                inset 8px 8px 20px #bec3cf,
                inset -8px -8px 20px #ffffff;
        }

        /* Features Section */
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
            color: #3d4468;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: #e0e5ec;
            border-radius: 25px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 
                15px 15px 40px #bec3cf,
                -15px -15px 40px #ffffff;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                20px 20px 50px #bec3cf,
                -20px -20px 50px #ffffff;
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 25px;
            background: #e0e5ec;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 
                inset 6px 6px 15px #bec3cf,
                inset -6px -6px 15px #ffffff;
            color: #3d4468;
        }

        .feature-icon svg {
            width: 30px;
            height: 30px;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: #3d4468;
        }

        .feature-card p {
            color: #6c7293;
            line-height: 1.6;
            font-size: 0.95rem;
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
            <div class="neu-icon" style="width: 40px; height: 40px; margin: 0; box-shadow: 4px 4px 10px #bec3cf, -4px -4px 10px #ffffff;">
                <svg style="width: 20px; height: 20px; color: #3d4468;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
            </div>
            TaskManager
        </div>
        <div class="auth-links">
            <a href="{{ route('login') }}" class="nav-login">Iniciar Sesión</a>
            <a href="{{ route('register') }}" class="btn-register-nav">Comenzar Gratis</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-card">
            <div class="neu-icon-large">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
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

</body>
</html>
