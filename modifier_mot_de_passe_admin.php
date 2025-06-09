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
    
    // Formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "<form method='post' action=''>";
        echo "<div style='margin-bottom: 10px;'>";
        echo "<label for='new_password'>Nouveau mot de passe:</label><br>";
        echo "<input type='password' id='new_password' name='new_password' required>";
        echo "</div>";
        
        echo "<div style='margin-bottom: 10px;'>";
        echo "<label for='confirm_password'>Confirmer le mot de passe:</label><br>";
        echo "<input type='password' id='confirm_password' name='confirm_password' required>";
        echo "</div>";
        
        echo "<button type='submit'>Modifier le mot de passe</button>";
        echo "</form>";
    } else {
        // Traiter la modification
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
            echo "<div style='color: green;'>✓ Mot de passe administrateur modifié avec succès!</div>";
            echo "<p>Vous pouvez maintenant vous connecter avec :</p>";
            echo "<ul>";
            echo "<li>Nom d'utilisateur : admin</li>";
            echo "<li>Nouveau mot de passe : " . str_repeat('*', strlen($new_password)) . "</li>";
            echo "</ul>";
            echo "<p><a href='index.php?controleur=auth&action=connexion'>Aller à la page de connexion</a></p>";
        } else {
            throw new Exception("Erreur lors de la modification du mot de passe.");
        }
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Erreur : " . $e->getMessage() . "</div>";
    echo "<p><a href='modifier_mot_de_passe_admin.php'>Réessayer</a></p>";
}
?> 