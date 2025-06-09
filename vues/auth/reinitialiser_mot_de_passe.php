<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Gestion de Stock</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(120deg, #5c7cfa 0%, #a5b4fc 100%);
            margin: 0;
        }
        .main-center {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0 10px;
        }
        .login-wrapper {
            display: flex;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
            min-height: 520px;
            animation: fadeIn 0.7s cubic-bezier(.39,.575,.56,1) both;
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: none; }
        }
        .login-form-side {
            flex: 1.1;
            padding: 56px 44px 56px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .login-form-side .logo {
            font-size: 2.7rem;
            color: #5c7cfa;
            text-align: center;
            margin-bottom: 14px;
        }
        .login-form-side h1 {
            text-align: center;
            font-size: 2.1rem;
            margin-bottom: 28px;
            color: #222;
            font-weight: 700;
        }
        .login-form-side form {
            width: 100%;
            max-width: 340px;
        }
        .form-group, .mb-3, .mb-4 {
            margin-bottom: 20px !important;
        }
        .btn-primary {
            background-color: #5c7cfa;
            border-color: #5c7cfa;
            font-size: 1.1rem;
            font-weight: 600;
            padding: 12px 0;
        }
        .btn-primary i {
            margin-right: 7px;
        }
        .btn-primary:hover {
            background-color: #4263eb;
            border-color: #4263eb;
        }
        .form-control:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.18);
        }
        .login-illustration-side {
            flex: 1;
            background: #f4f6fb;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 0;
        }
        .login-illustration-side svg {
            width: 90%;
            max-width: 370px;
            height: auto;
            display: block;
        }
        .text-center.mt-3, .back-to-login {
            margin-top: 18px !important;
        }
        @media (max-width: 1100px) {
            .login-wrapper { max-width: 98vw; }
        }
        @media (max-width: 900px) {
            .login-wrapper { flex-direction: column; min-height: unset; max-width: 98vw; }
            .login-illustration-side { display: none; }
            .login-form-side { padding: 44px 18px; }
        }
        @media (max-width: 500px) {
            .login-form-side form { max-width: 100%; }
            .login-form-side { padding: 28px 4vw; }
        }
    </style>
</head>
<body>
    <div class="main-center">
        <div class="login-wrapper">
            <div class="login-form-side">
                <div class="logo"><i class="fas fa-cubes"></i></div>
                <h1>Gestion de Stock</h1>
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <h5 class="text-center mb-4">Réinitialiser votre mot de passe</h5>
                <form action="index.php?controleur=auth&action=mettreAJourMotDePasse" method="post" id="resetPasswordForm">
                    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
                    <div class="form-group">
                        <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" placeholder="Votre nouveau mot de passe" required autofocus>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength"></div>
                        <div class="password-feedback text-muted" id="passwordFeedback">
                            Le mot de passe doit contenir au moins 8 caractères
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmer_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" placeholder="Confirmez votre mot de passe" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordMatchFeedback">
                            Les mots de passe ne correspondent pas
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save me-2"></i> Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
            <div class="login-illustration-side">
                <!-- Illustration SVG libre de droits -->
                <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="40" y="60" width="320" height="180" rx="20" fill="#e0e7ff"/>
                    <rect x="70" y="90" width="90" height="30" rx="6" fill="#5c7cfa"/>
                    <rect x="70" y="130" width="260" height="20" rx="5" fill="#c7d2fe"/>
                    <rect x="70" y="160" width="200" height="20" rx="5" fill="#c7d2fe"/>
                    <rect x="70" y="190" width="150" height="20" rx="5" fill="#c7d2fe"/>
                    <circle cx="320" cy="100" r="18" fill="#a5b4fc"/>
                    <rect x="270" y="90" width="40" height="20" rx="5" fill="#818cf8"/>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('mot_de_passe');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Toggle confirm password visibility
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('confirmer_mot_de_passe');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Password strength meter
        document.getElementById('mot_de_passe').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const feedback = document.getElementById('passwordFeedback');
            
            // Check password strength
            let strength = 0;
            let feedbackText = '';
            
            if (password.length >= 8) {
                strength += 25;
                feedbackText = 'Mot de passe acceptable. ';
            } else {
                feedbackText = 'Le mot de passe doit contenir au moins 8 caractères. ';
            }
            
            if (password.match(/[A-Z]/)) {
                strength += 25;
                feedbackText += 'Bon: contient une majuscule. ';
            }
            
            if (password.match(/[0-9]/)) {
                strength += 25;
                feedbackText += 'Bon: contient un chiffre. ';
            }
            
            if (password.match(/[^A-Za-z0-9]/)) {
                strength += 25;
                feedbackText += 'Excellent: contient un caractère spécial.';
            }
            
            // Update the strength bar
            strengthBar.style.width = strength + '%';
            
            // Set the color based on strength
            if (strength < 25) {
                strengthBar.style.backgroundColor = '#dc3545'; // red
                feedbackText = 'Mot de passe trop court';
            } else if (strength < 50) {
                strengthBar.style.backgroundColor = '#ffc107'; // yellow
            } else if (strength < 75) {
                strengthBar.style.backgroundColor = '#fd7e14'; // orange
            } else {
                strengthBar.style.backgroundColor = '#28a745'; // green
                feedbackText = 'Mot de passe fort';
            }
            
            feedback.textContent = feedbackText;
        });
        
        // Check if passwords match
        document.getElementById('confirmer_mot_de_passe').addEventListener('input', function() {
            const password = document.getElementById('mot_de_passe').value;
            const confirmPassword = this.value;
            const feedback = document.getElementById('passwordMatchFeedback');
            
            if (password !== confirmPassword) {
                this.classList.add('is-invalid');
                feedback.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                feedback.style.display = 'none';
            }
        });
        
        // Form validation
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('mot_de_passe').value;
            const confirmPassword = document.getElementById('confirmer_mot_de_passe').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères.');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
            
            return true;
        });
    </script>
</body>
</html>
