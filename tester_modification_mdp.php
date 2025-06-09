<?php
require_once 'config/database.php';
session_start();

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Test de modification de mot de passe</h1>";
    
    // 1. Vérifier l'état initial
    echo "<h2>État initial des mots de passe :</h2>";
    $query = "SELECT id, nom_utilisateur, mot_de_passe, role FROM utilisateurs ORDER BY id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $utilisateurs_avant = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 20px;'>";
    echo "<tr style='background-color: #f8f9fa;'>";
    echo "<th>ID</th>";
    echo "<th>Nom d'utilisateur</th>";
    echo "<th>Mot de passe (avant)</th>";
    echo "<th>Rôle</th>";
    echo "</tr>";
    
    foreach ($utilisateurs_avant as $utilisateur) {
        echo "<tr>";
        echo "<td>" . $utilisateur['id'] . "</td>";
        echo "<td>" . $utilisateur['nom_utilisateur'] . "</td>";
        echo "<td>" . $utilisateur['mot_de_passe'] . "</td>";
        echo "<td>" . $utilisateur['role'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 2. Instructions pour tester
    echo "<div style='margin: 20px; padding: 15px; background-color: #e9ecef; border-radius: 4px;'>";
    echo "<h3>Instructions pour tester :</h3>";
    echo "<ol>";
    echo "<li>Connectez-vous en tant qu'administrateur</li>";
    echo "<li>Allez dans la gestion des utilisateurs</li>";
    echo "<li>Modifiez le mot de passe d'un utilisateur</li>";
    echo "<li>Revenez sur cette page et rafraîchissez pour voir les changements</li>";
    echo "</ol>";
    echo "</div>";
    
    // 3. Formulaire pour vérifier après modification
    echo "<form method='post' style='margin: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
    echo "<h3>Vérifier après modification :</h3>";
    echo "<input type='submit' name='verifier' value='Vérifier les changements' style='padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;'>";
    echo "</form>";
    
    // 4. Afficher les changements si le formulaire est soumis
    if (isset($_POST['verifier'])) {
        echo "<h2>État après modification :</h2>";
        $stmt->execute();
        $utilisateurs_apres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-top: 20px;'>";
        echo "<tr style='background-color: #f8f9fa;'>";
        echo "<th>ID</th>";
        echo "<th>Nom d'utilisateur</th>";
        echo "<th>Mot de passe (après)</th>";
        echo "<th>Rôle</th>";
        echo "<th>Changement détecté</th>";
        echo "</tr>";
        
        foreach ($utilisateurs_apres as $utilisateur) {
            $utilisateur_avant = array_filter($utilisateurs_avant, function($u) use ($utilisateur) {
                return $u['id'] == $utilisateur['id'];
            });
            $utilisateur_avant = reset($utilisateur_avant);
            $a_change = $utilisateur_avant['mot_de_passe'] !== $utilisateur['mot_de_passe'];
            $style = $a_change ? "background-color: #d4edda;" : "";
            
            echo "<tr style='" . $style . "'>";
            echo "<td>" . $utilisateur['id'] . "</td>";
            echo "<td>" . $utilisateur['nom_utilisateur'] . "</td>";
            echo "<td>" . $utilisateur['mot_de_passe'] . "</td>";
            echo "<td>" . $utilisateur['role'] . "</td>";
            echo "<td>" . ($a_change ? "Oui" : "Non") . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Vérifier si les mots de passe sont en clair
        $tous_en_clair = true;
        foreach ($utilisateurs_apres as $utilisateur) {
            if (strpos($utilisateur['mot_de_passe'], '$2y$') === 0) {
                $tous_en_clair = false;
                break;
            }
        }
        
        echo "<div style='margin-top: 20px; padding: 15px; background-color: " . ($tous_en_clair ? "#d4edda" : "#f8d7da") . "; border-radius: 4px;'>";
        echo "<h3>Résultat de la vérification :</h3>";
        if ($tous_en_clair) {
            echo "<p style='color: #155724;'>✅ Tous les mots de passe sont en clair (non hashés)</p>";
        } else {
            echo "<p style='color: #721c24;'>❌ Certains mots de passe sont encore hashés</p>";
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;'>";
    echo "Erreur : " . $e->getMessage();
    echo "</div>";
}
?> 