<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Connexion - TAXE</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            overflow: hidden;
            background: #f8f9fa;
        }

        /* Section gauche - Informations */
        .login-left {
            width: 50%;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #c084fc 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: move 20s linear infinite;
        }

        @keyframes move {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .login-left-content {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
            max-width: 500px;
        }

        .logo-container {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .logo-container i {
            font-size: 3rem;
            color: white;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
        }

        .login-left h1 {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: 3px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-left p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 3rem;
            opacity: 0.95;
        }

        .feature-cards {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            opacity: 0;
            animation: slideInLeft 0.8s ease forwards;
        }

        .feature-card:nth-child(1) { animation-delay: 0.2s; }
        .feature-card:nth-child(2) { animation-delay: 0.4s; }
        .feature-card:nth-child(3) { animation-delay: 0.6s; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(5deg) scale(1.1);
        }

        .feature-icon i {
            font-size: 1.5rem;
            color: white;
        }

        .feature-text h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .feature-text p {
            font-size: 0.9rem;
            opacity: 0.95;
            margin: 0;
            line-height: 1.4;
        }

        /* Section droite - Formulaire */
        .login-right {
            width: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            overflow-y: auto;
            box-shadow: -10px 0 50px rgba(124, 58, 237, 0.05);
        }

        .login-form-container {
            width: 100%;
            max-width: 450px;
            animation: fadeIn 1s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .login-form-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .login-form-subtitle {
            color: #6c757d;
            margin-bottom: 2.5rem;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            animation: slideUp 0.5s ease;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
            display: block;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper.focused .form-label {
            color: #7c3aed;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #7c3aed;
            z-index: 10;
        }

        .form-control {
            width: 100%;
            height: 55px;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-control:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.25);
            outline: none;
        }

        .form-control:focus + .input-icon {
            color: #a855f7;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
            padding: 0.5rem;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #7c3aed;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #7c3aed;
        }

        .remember-me label {
            color: #6c757d;
            font-size: 0.95rem;
            cursor: pointer;
            margin: 0;
        }

        .login-button {
            width: 100%;
            height: 55px;
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(124, 58, 237, 0.5);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button.loading {
            pointer-events: none;
        }

        .login-button.loading span {
            opacity: 0;
        }

        .login-button.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 2px solid #f87171;
            color: #dc2626;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s, fadeInDown 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-message i {
            font-size: 1.25rem;
        }

        .copyright {
            text-align: center;
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 2.5rem;
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 968px) {
            body {
                flex-direction: column;
                overflow-y: auto;
            }

            .login-left {
                width: 100%;
                min-height: 45vh;
                padding: 2rem 1.5rem;
            }

            .logo-container {
                width: 70px;
                height: 70px;
                margin-bottom: 1.5rem;
            }

            .logo-container i {
                font-size: 2rem;
            }

            .login-left h1 {
                font-size: 2.5rem;
                letter-spacing: 1px;
            }

            .login-left p {
                font-size: 0.95rem;
                margin-bottom: 2rem;
            }

            .login-right {
                width: 100%;
                padding: 2rem 1.5rem;
            }

            .login-form-title {
                font-size: 2rem;
            }

            .feature-cards {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .feature-card {
                flex: 1;
                min-width: 200px;
                padding: 1rem;
            }

            .feature-icon {
                width: 45px;
                height: 45px;
            }

            .feature-icon i {
                font-size: 1.25rem;
            }

            .feature-text h3 {
                font-size: 1rem;
            }

            .feature-text p {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 576px) {
            .login-left h1 {
                font-size: 2rem;
            }

            .login-form-title {
                font-size: 1.75rem;
            }

            .form-control, .login-button {
                height: 50px;
            }

            .feature-cards {
                flex-direction: column;
            }

            .feature-card {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Section gauche - Informations -->
    <div class="login-left">
        <div class="login-left-content">
            <div class="logo-container">
                <i class="fas fa-building"></i>
            </div>
            <h1>TAXE</h1>
            <p>Plateforme de gestion des taxes et contributions locales. Simplifiez la gestion de vos contribuables et optimisez vos processus.</p>
            
            <div class="feature-cards">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Gestion des Contribuables</h3>
                        <p>Gérez facilement tous vos contribuables</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Tableau de Bord</h3>
                        <p>Suivez vos statistiques en temps réel</p>
                    </div>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="feature-text">
                        <h3>Sécurité Renforcée</h3>
                        <p>Vos données sont protégées</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section droite - Formulaire -->
    <div class="login-right">
        <div class="login-form-container">
            <h1 class="login-form-title">Connexion</h1>
            <p class="login-form-subtitle">Entrez vos identifiants pour accéder à votre espace</p>

            <form id="loginForm" method="POST" action="{{ route('auth.connexion') }}">
                @csrf
                
                <div class="form-group">
                    <label for="identifiant" class="form-label">Identifiant</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               class="form-control" 
                               id="identifiant" 
                               name="identifiant" 
                               value="{{ old('identifiant') }}" 
                               placeholder="Email, téléphone ou Nº compte"
                               required
                               autocomplete="username">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <div class="input-wrapper password-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Entrez votre mot de passe"
                               required
                               autocomplete="current-password">
                        <button type="button" 
                                class="password-toggle" 
                                onclick="togglePassword()"
                                aria-label="Afficher/Masquer le mot de passe">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <div id="login-error" class="mb-3"></div>
                @error('form')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                <button type="submit" class="login-button" id="loginBtn">
                    <span>Se connecter</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <p class="copyright">© 2025 TAXE. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Gestion du formulaire avec token
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const button = document.getElementById('loginBtn');
            const buttonText = button.querySelector('span');
            const originalText = buttonText.textContent;
            
            button.classList.add('loading');
            buttonText.textContent = 'Connexion...';

            const formData = new FormData(this);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                let data;
                try {
                    data = await response.json();
                } catch (err) {
                    throw new Error('Réponse inattendue du serveur');
                }

                if (data.success && data.token) {
                    // Stocker le token dans localStorage
                    localStorage.setItem('auth_token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Rediriger vers le dashboard
                    window.location.href = data.redirect || '/dashboard';
                } else {
                    // Afficher l'erreur
                    const message = data.message || (response.status === 401 ? 'Identifiants incorrects' : 'Erreur de connexion');
                    showError(message);
                    button.classList.remove('loading');
                    buttonText.textContent = originalText;
                }
            } catch (error) {
                console.error('Erreur:', error);
                showError('Une erreur est survenue lors de la connexion');
                button.classList.remove('loading');
                buttonText.textContent = originalText;
            }
        });

        function showError(message) {
            const container = document.getElementById('login-error');
            if (!container) return;

            container.innerHTML = '';
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i><span>${message}</span>`;
            container.appendChild(errorDiv);
        }

        // Animation des champs
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    </script>
</body>
</html>
