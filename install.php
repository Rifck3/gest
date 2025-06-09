<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'installation a déjà été effectuée
if (file_exists('config/installed.php')) {
    die('L\'application est déjà installée. Supprimez le fichier config/installed.php pour réinstaller.');
}

// Fonction pour tester la connexion à la base de données
function testDatabaseConnection($host, $dbname, $username, $password) {
    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? '';
    $dbname = $_POST['dbname'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (testDatabaseConnection($host, $dbname, $username, $password)) {
        // Créer le fichier de configuration
        $config = "<?php
// Configuration de la base de données
\$db_config = [
    'host' => '$host',
    'dbname' => '$dbname',
    'username' => '$username',
    'password' => '$password',
    'charset' => 'utf8mb4'
];

try {
    \$db = new PDO(
        \"mysql:host={\$db_config['host']};dbname={\$db_config['dbname']};charset={\$db_config['charset']}\",
        \$db_config['username'],
        \$db_config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException \$e) {
    die(\"Erreur de connexion : \" . \$e->getMessage());
}";
        
        // Écrire la configuration
        file_put_contents('config/database.php', $config);
        
        // Créer le fichier d'installation
        file_put_contents('config/installed.php', '<?php return true;');
        
        // Rediriger vers la page d'accueil
        header('Location: index.php');
        exit;
    } else {
        $error = "Impossible de se connecter à la base de données. Vérifiez vos informations.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Système de Gestion de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Installation du Système de Gestion de Stock</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="host" class="form-label">Hôte de la base de données</label>
                                <input type="text" class="form-control" id="host" name="host" value="localhost" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="dbname" class="form-label">Nom de la base de données</label>
                                <input type="text" class="form-control" id="dbname" name="dbname" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Installer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 