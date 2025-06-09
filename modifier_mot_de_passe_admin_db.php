<?php
require_once 'config/database.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Modification du mot de passe administrateur</h1>";
    
    // Traiter le formulaire si soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Vérifications
        if ($new_password !== $confirm_password) {
            throw new Exception("Les mots de passe ne correspondent pas.");
        }
        
        if (strlen($new_password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères.");
        }
        
        // Hasher le nouveau mot de passe
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Mettre à jour le mot de passe de l'administrateur
        $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE nom_utilisateur = 'admin'";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$password_hash])) {
            echo "<div style='color: green; padding: 10px; background-color: #e8f5e9; border: 1px solid #c8e6c9; border-radius: 4px;'>";
            echo "✓ Mot de passe administrateur modifié avec succès!";
            echo "</div>";
            
            // Afficher les détails de l'utilisateur admin
            $query = "SELECT id, nom_utilisateur, nom_complet, role FROM utilisateurs WHERE nom_utilisateur = 'admin'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
            echo "<h3>Détails de l'administrateur :</h3>";
            echo "<p><strong>ID :</strong> " . $admin['id'] . "</p>";
            echo "<p><strong>Nom d'utilisateur :</strong> " . $admin['nom_utilisateur'] . "</p>";
            echo "<p><strong>Nom complet :</strong> " . $admin['nom_complet'] . "</p>";
            echo "<p><strong>Rôle :</strong> " . $admin['role'] . "</p>";
            echo "</div>";
            
            echo "<p style='margin-top: 20px;'><a href='index.php?controleur=auth&action=connexion' style='color: #4e73df; text-decoration: none;'>← Retour à la page de connexion</a></p>";
        } else {
            throw new Exception("Erreur lors de la modification du mot de passe.");
        }
    } else {
        // Afficher le formulaire
        echo "<form method='post' action='' style='max-width: 400px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>";
        echo "<div style='margin-bottom: 15px;'>";
        echo "<label for='new_password' style='display: block; margin-bottom: 5px;'>Nouveau mot de passe :</label>";
        echo "<input type='password' id='new_password' name='new_password' required minlength='8' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
        echo "<small style='color: #666;'>Le mot de passe doit contenir au moins 8 caractères.</small>";
        echo "</div>";
        
        echo "<div style='margin-bottom: 20px;'>";
        echo "<label for='confirm_password' style='display: block; margin-bottom: 5px;'>Confirmer le mot de passe :</label>";
        echo "<input type='password' id='confirm_password' name='confirm_password' required minlength='8' style='width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;'>";
        echo "</div>";
        
        echo "<button type='submit' style='width: 100%; padding: 10px; background-color: #4e73df; color: white; border: none; border-radius: 4px; cursor: pointer;'>Modifier le mot de passe</button>";
        echo "</form>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: #721c24; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;'>";
    echo "Erreur : " . $e->getMessage();
    echo "</div>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo "<p><a href='modifier_mot_de_passe_admin_db.php' style='color: #4e73df; text-decoration: none;'>← Réessayer</a></p>";
    }
}
?> 