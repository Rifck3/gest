<?php
// Charger la configuration si elle n'est pas déjà chargée
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/config.php';
}

// Variables globales pour être utilisées dans d'autres fichiers
$host = DB_HOST;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PASSWORD;

try {
    $db = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Utiliser les constantes définies dans config.php
        $this->host = DB_HOST;
        $this->db_name = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASSWORD;
    }

    // Méthode pour se connecter à la base de données
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            
            // Test de la connexion
            $this->testConnection();
            
        } catch(PDOException $exception) {
            error_log("Erreur de connexion à la base de données: " . $exception->getMessage());
            throw new Exception("Erreur de connexion à la base de données. Veuillez vérifier la configuration.");
        }

        return $this->conn;
    }

    // Méthode pour tester la connexion
    private function testConnection() {
        try {
            $test = $this->conn->query("SELECT 1");
            if (!$test) {
                throw new Exception("La connexion à la base de données a échoué");
            }
        } catch (Exception $e) {
            error_log("Test de connexion échoué: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données");
        }
    }

    // Méthode pour vérifier si une table existe
    public function tableExists($tableName) {
        try {
            $result = $this->conn->query("SHOW TABLES LIKE '$tableName'");
            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de la table: " . $e->getMessage());
            return false;
        }
    }

    // Méthode pour vérifier la structure d'une table
    public function checkTableStructure($tableName) {
        try {
            $result = $this->conn->query("DESCRIBE $tableName");
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de la structure: " . $e->getMessage());
            return false;
        }
    }
}
?>
