<?php
require_once 'config/database.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Réinitialisation de Mot de Passe</h1>";
    
    // Formulaire de réinitialisation
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        // Afficher la liste des utilisateurs
        $query = "SELECT id, nom_utilisateur, nom_complet, role FROM utilisateurs";
        $stmt = $db->query($query);
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<form method='post' action=''>";
        echo "<div style='margin-bottom: 10px;'>";
        echo "<label for='user_id'>Sélectionner l'utilisateur:</label><br>";
        echo "<select name='user_id' id='user_id' required>";
        foreach ($utilisateurs as $utilisateur) {
            echo "<option value='" . $utilisateur['id'] . "'>" . 
                 $utilisateur['nom_utilisateur'] . " (" . $utilisateur['nom_complet'] . " - " . $utilisateur['role'] . ")</option>";
        }
        echo "</select>";
        echo "</div>";
        
        echo "<div style='margin-bottom: 10px;'>";
        echo "<label for='new_password'>Nouveau mot de passe:</label><br>";
        echo "<input type='password' id='new_password' name='new_password' required>";
        echo "</div>";
        
        echo "<div style='margin-bottom: 10px;'>";
        echo "<label for='confirm_password'>Confirmer le mot de passe:</label><br>";
        echo "<input type='password' id='confirm_password' name='confirm_password' required>";
        echo "</div>";
        
        echo "<button type='submit'>Réinitialiser le mot de passe</button>";
        echo "</form>";
    } else {
        // Traiter la réinitialisation
        $user_id = $_POST['user_id'];
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
        
        // Mettre à jour le mot de passe
        $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        
        if ($stmt->execute([$password_hash, $user_id])) {
            echo "<div style='color: green;'>✓ Mot de passe réinitialisé avec succès!</div>";
            
            // Afficher les détails de l'utilisateur
            $query = "SELECT nom_utilisateur, nom_complet, role FROM utilisateurs WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$user_id]);
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<pre>";
            echo "Utilisateur mis à jour:\n";
            echo "Nom d'utilisateur: " . $utilisateur['nom_utilisateur'] . "\n";
            echo "Nom complet: " . $utilisateur['nom_complet'] . "\n";
            echo "Rôle: " . $utilisateur['role'] . "\n";
            echo "</pre>";
            
            echo "<p><a href='verifier_mot_de_passe.php'>Retour à la vérification des mots de passe</a></p>";
        } else {
            throw new Exception("Erreur lors de la mise à jour du mot de passe.");
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Erreur : " . $e->getMessage() . "</div>";
    echo "<p><a href='reinitialiser_mot_de_passe.php'>Réessayer</a></p>";
}
?> 