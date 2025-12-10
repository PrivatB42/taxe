<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #1e1b4b;
            --dark-lighter: #312e81;
            --light: #f8fafc;
            --gray: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-lighter) 50%, #4c1d95 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background shapes */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.3), rgba(139, 92, 246, 0.1));
            animation: float 20s infinite ease-in-out;
        }

        .shape:nth-child(1) {
            width: 600px;
            height: 600px;
            top: -200px;
            right: -100px;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(251, 191, 36, 0.1));
        }

        .shape:nth-child(3) {
            width: 300px;
            height: 300px;
            top: 50%;
            left: 30%;
            animation-delay: -10s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg) scale(1);
            }
            25% {
                transform: translate(30px, -30px) rotate(5deg) scale(1.05);
            }
            50% {
                transform: translate(-20px, 20px) rotate(-5deg) scale(0.95);
            }
            75% {
                transform: translate(20px, 30px) rotate(3deg) scale(1.02);
            }
        }

        /* Left Panel - Branding */
        .brand-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            z-index: 1;
        }

        .brand-content {
            text-align: center;
            color: white;
            max-width: 500px;
        }

        .brand-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.4);
            transform: rotate(-5deg);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: rotate(-5deg) translateY(0); }
            50% { transform: rotate(-5deg) translateY(-10px); }
        }

        .brand-logo i {
            font-size: 48px;
            color: white;
        }

        .brand-title {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #fff 0%, #c4b5fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .features {
            display: flex;
            flex-direction: column;
            gap: 20px;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(10px);
        }

        .feature-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feature-icon i {
            font-size: 18px;
            color: white;
        }

        .feature-text h4 {
            font-size: 16px;
            font-weight: 600;
            color: white;
            margin-bottom: 4px;
        }

        .feature-text p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
        }

        /* Right Panel - Login Form */
        .login-panel {
            width: 520px;
            min-height: 100vh;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            z-index: 2;
            box-shadow: -30px 0 60px rgba(0, 0, 0, 0.3);
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--gray);
            font-size: 16px;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        .alert i {
            font-size: 18px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 16px 18px 16px 52px;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.3s ease;
            color: var(--dark);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-control:focus + i,
        .input-wrapper:focus-within i {
            color: var(--primary);
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            background: #fef2f2;
        }

        .invalid-feedback {
            display: block;
            color: var(--danger);
            font-size: 13px;
            margin-top: 8px;
            padding-left: 4px;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray);
            cursor: pointer;
            padding: 4px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary);
        }

        /* Remember & Forgot */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-wrapper input {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .checkbox-wrapper span {
            font-size: 14px;
            color: var(--gray);
        }

        .forgot-link {
            font-size: 14px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
        }

        /* Submit Button */
        .btn-login {
            width: 100%;
            padding: 18px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-login .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            display: none;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Footer */
        .login-footer {
            margin-top: 40px;
            text-align: center;
            color: var(--gray);
            font-size: 14px;
        }

        .login-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .brand-panel {
                display: none;
            }

            .login-panel {
                width: 100%;
                max-width: 100%;
                min-height: 100vh;
            }
        }

        @media (max-width: 480px) {
            .login-panel {
                padding: 40px 24px;
            }

            .login-header h2 {
                font-size: 26px;
            }

            .form-options {
                flex-direction: column;
                gap: 16px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <!-- Background Shapes -->
    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Brand Panel -->
    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">
                <i class="fas fa-landmark"></i>
            </div>
            <h1 class="brand-title">{{ config('app.name', 'Gestion Taxes') }}</h1>
            <p class="brand-subtitle">
                Plateforme de gestion des taxes et contributions locales. 
                Simplifiez la gestion de vos contribuables et optimisez vos processus.
            </p>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Gestion des Contribuables</h4>
                        <p>Gérez facilement tous vos contribuables</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Tableau de Bord</h4>
                        <p>Suivez vos statistiques en temps réel</p>
                    </div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h4>Sécurité Renforcée</h4>
                        <p>Vos données sont protégées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Panel -->
    <div class="login-panel">
        <div class="login-header">
            <h2>Connexion</h2>
            <p>Entrez vos identifiants pour accéder à votre espace</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf

            <div class="form-group">
                <label class="form-label">Identifiant</label>
                <div class="input-wrapper">
                    <input type="text" 
                           name="identifiant" 
                           class="form-control @error('identifiant') is-invalid @enderror" 
                           placeholder="Email, téléphone ou N° compte"
                           value="{{ old('identifiant') }}"
                           autocomplete="username"
                           autofocus>
                    <i class="fas fa-user"></i>
                </div>
                @error('identifiant')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <div class="input-wrapper">
                    <input type="password" 
                           name="password" 
                           id="password"
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Entrez votre mot de passe"
                           autocomplete="current-password">
                    <i class="fas fa-lock"></i>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-options">
                <label class="checkbox-wrapper">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit" class="btn-login" id="submitBtn">
                <span class="spinner" id="spinner"></span>
                <span id="btnText">Se connecter</span>
                <i class="fas fa-arrow-right" id="btnIcon"></i>
            </button>
        </form>

        <div class="login-footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Form submission animation
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            
            btn.disabled = true;
            spinner.style.display = 'block';
            btnText.textContent = 'Connexion...';
            btnIcon.style.display = 'none';
        });
    </script>
</body>

</html>


