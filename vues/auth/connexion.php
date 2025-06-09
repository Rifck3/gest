<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion de Stock</title>
    
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
        .form-group {
            margin-bottom: 20px;
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
        .text-center.mt-3 {
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
    <?php
                // Affichage des messages d'alerte à l'intérieur de la carte
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    ?>
                        <form method="POST" action="index.php?controleur=auth&action=authentifier">
                    <div class="form-group">
                                <label for="identifiant">Identifiant</label>
                                <input type="text" class="form-control" id="identifiant" name="identifiant" required>
                            </div>
                    <div class="form-group">
                                <label for="mot_de_passe">Mot de passe</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                            </div>
                            <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="index.php?controleur=auth&action=motDePasseOublie">Mot de passe oublié ?</a>
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
</body>
</html>
