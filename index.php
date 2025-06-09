<?php
session_start();

// Vérifier si c'est une requête API
$is_api = isset($_GET['api']) && $_GET['api'] === '1';

// Si c'est une requête API, ne pas inclure le header/footer
if ($is_api) {
    // Déterminer le contrôleur et l'action à partir des paramètres GET
    $controleur = isset($_GET['controleur']) ? $_GET['controleur'] : 'tableau';
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';

    // Instancier le contrôleur approprié
    switch($controleur) {
        case 'produit':
            require_once 'controleurs/ProduitControleur.php';
            $controleur = new ProduitControleur();
            break;
        case 'fournisseur':
            require_once 'controleurs/FournisseurControleur.php';
            $controleur = new FournisseurControleur();
            break;
        case 'mouvement':
            require_once 'controleurs/MouvementStockControleur.php';
            $controleur = new MouvementStockControleur();
            break;
        case 'utilisateur':
            require_once 'controleurs/UtilisateurControleur.php';
            $controleur = new UtilisateurControleur();
            break;
        case 'auth':
            require_once 'controleurs/AuthControleur.php';
            $controleur = new AuthControleur();
            break;
        case 'categorie':
            require_once 'controleurs/CategorieControleur.php';
            $controleur = new CategorieControleur();
            break;
        case 'rapport':
            require_once 'controleurs/RapportControleur.php';
            $controleur = new RapportControleur();
            break;
        case 'chatbot':
            require_once 'controleurs/ChatbotControleur.php';
            $controleur = new ChatbotControleur();
            break;
        case 'commande':
            require_once 'controleurs/CommandeControleur.php';
            $controleur = new CommandeControleur();
            break;
        case 'tableau':
        default:
            require_once 'controleurs/TableauControleur.php';
            $controleur = new TableauControleur();
            break;
    }

    // Appeler l'action appropriée
    if(method_exists($controleur, $action)) {
        $controleur->$action();
    } else {
        echo json_encode(['erreur' => 'Action non trouvée']);
    }
    exit;
}

// Pour les requêtes non API
// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['user_id']) && !in_array($_GET['controleur'] ?? '', ['auth']) && !in_array($_GET['action'] ?? '', ['connexion', 'authentifier'])) {
    header("Location: index.php?controleur=auth&action=connexion");
    exit;
}

// Inclure les fichiers de configuration
require_once 'config/database.php';

// Créer une instance de la base de données
$database = new Database();
$db = $database->getConnection();

// Inclure les contrôleurs
require_once 'controleurs/ProduitControleur.php';
require_once 'controleurs/FournisseurControleur.php';
require_once 'controleurs/MouvementStockControleur.php';
require_once 'controleurs/UtilisateurControleur.php';
require_once 'controleurs/TableauControleur.php';
require_once 'controleurs/AuthControleur.php';
require_once 'controleurs/CategorieControleur.php';
require_once 'controleurs/RapportControleur.php';
require_once 'controleurs/ChatbotControleur.php';
require_once 'controleurs/CommandeControleur.php';

// Déterminer le contrôleur et l'action à partir des paramètres GET
$controleur = isset($_GET['controleur']) ? $_GET['controleur'] : 'tableau';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Exécuter l'action du contrôleur avant d'inclure le header (pour permettre les redirections)
$controller_instance = null;

// Instancier le contrôleur approprié
switch($controleur) {
    case 'produit':
        $controller_instance = new ProduitControleur();
        break;
    case 'fournisseur':
        $controller_instance = new FournisseurControleur();
        break;
    case 'mouvement':
        $controller_instance = new MouvementStockControleur();
        break;
    case 'utilisateur':
        $controller_instance = new UtilisateurControleur();
        break;
    case 'auth':
        $controller_instance = new AuthControleur();
        break;
    case 'categorie':
        $controller_instance = new CategorieControleur();
        break;
    case 'rapport':
        $controller_instance = new RapportControleur();
        break;
    case 'chatbot':
        $controller_instance = new ChatbotControleur();
        break;
    case 'commande':
        $controller_instance = new CommandeControleur();
        break;
    case 'tableau':
    default:
        $controller_instance = new TableauControleur();
        break;
}

// Appeler l'action appropriée
if($controller_instance && method_exists($controller_instance, $action)) {
    // Exécuter l'action avant d'inclure le header (pour permettre les redirections)
    $controller_instance->$action();
} else {
    // Rediriger vers la page d'accueil si l'action n'existe pas
    header("Location: index.php?controleur=tableau&action=index");
    exit;
}

// Inclure le header seulement après que toutes les redirections potentielles aient été traitées
require_once 'vues/includes/header.php';

// Inclure la vue correspondante
$vue = "vues/{$controleur}s/{$action}.php";
if (file_exists($vue)) {
    // Rendre les variables du contrôleur disponibles pour la vue
    if (isset($commandes)) {
        $commandes = $commandes;
    }
    if (isset($fournisseurs)) {
        $fournisseurs = $fournisseurs;
    }
    if (isset($produits)) {
        $produits = $produits;
    }
    if (isset($commande)) {
        $commande = $commande;
    }
    if (isset($details)) {
        $details = $details;
    }
    
    require_once $vue;
}

// Inclure le footer
require_once 'vues/includes/footer.php';

// Au début du fichier, après les includes
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Fonction de débogage
function debug($data, $die = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if ($die) die();
}

// Fonction pour vérifier la connexion à la base de données
function verifierConnexionDB() {
    global $db;
    try {
        $test = $db->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        error_log("Erreur de connexion DB: " . $e->getMessage());
        return false;
    }
}

// Vérifier la connexion avant chaque opération importante
if (!verifierConnexionDB()) {
    die("Erreur de connexion à la base de données. Veuillez vérifier la configuration.");
}

// Ajouter un gestionnaire d'erreurs personnalisé
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Erreur PHP [$errno] $errstr dans $errfile à la ligne $errline");
    return false;
});

// Ajouter un gestionnaire d'exceptions
set_exception_handler(function($e) {
    error_log("Exception non gérée: " . $e->getMessage());
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['erreur' => 'Une erreur est survenue']);
    } else {
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
});
?>