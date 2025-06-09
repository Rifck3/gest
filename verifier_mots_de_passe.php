<?php
require_once 'config/database.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>État des mots de passe dans la base de données</h1>";
    
    // Récupérer tous les utilisateurs
    $query = "SELECT id, nom_utilisateur, mot_de_passe, role, actif FROM utilisateurs ORDER BY id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 20px;'>";
    echo "<tr style='background-color: #f8f9fa;'>";
    echo "<th>ID</th>";
    echo "<th>Nom d'utilisateur</th>";
    echo "<th>Mot de passe</th>";
    echo "<th>Rôle</th>";
    echo "<th>Statut</th>";
    echo "</tr>";
    
    foreach ($utilisateurs as $utilisateur) {
        $est_hash = strpos($utilisateur['mot_de_passe'], '$2y$') === 0;
        $style = $est_hash ? "color: red;" : "color: green;";
        
        echo "<tr>";
        echo "<td>" . $utilisateur['id'] . "</td>";
        echo "<td>" . $utilisateur['nom_utilisateur'] . "</td>";
        echo "<td style='" . $style . "'>" . $utilisateur['mot_de_passe'] . "</td>";
        echo "<td>" . $utilisateur['role'] . "</td>";
        echo "<td>" . ($utilisateur['actif'] ? 'Actif' : 'Inactif') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
    echo "<h3>Légende :</h3>";
    echo "<p style='color: red;'>Rouge : Mot de passe hashé (à modifier)</p>";
    echo "<p style='color: green;'>Vert : Mot de passe en clair (OK)</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;'>";
    echo "Erreur : " . $e->getMessage();
    echo "</div>";
}
?> 