<?php
require_once 'config/database.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Correction du mot de passe administrateur</h1>";
    
    // Récupérer le mot de passe actuel
    $query = "SELECT mot_de_passe FROM utilisateurs WHERE nom_utilisateur = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $current_password = $result['mot_de_passe'];
        
        // Vérifier si le mot de passe est déjà hashé
        if (strpos($current_password, '$2y$') === 0) {
            echo "<div style='color: green; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;'>";
            echo "Le mot de passe est déjà correctement hashé.";
            echo "</div>";
        } else {
            // Hasher le mot de passe actuel
            $password_hash = password_hash($current_password, PASSWORD_DEFAULT);
            
            // Mettre à jour le mot de passe avec le hash
            $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE nom_utilisateur = 'admin'";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([$password_hash])) {
                echo "<div style='color: green; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;'>";
                echo "✓ Le mot de passe a été correctement hashé et mis à jour dans la base de données.";
                echo "</div>";
                
                // Vérifier que le hash fonctionne
                if (password_verify($current_password, $password_hash)) {
                    echo "<div style='color: green; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin-top: 10px;'>";
                    echo "✓ Vérification réussie : le hash fonctionne correctement.";
                    echo "</div>";
                } else {
                    echo "<div style='color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin-top: 10px;'>";
                    echo "✗ Erreur : la vérification du hash a échoué.";
                    echo "</div>";
                }
            } else {
                throw new Exception("Erreur lors de la mise à jour du mot de passe.");
            }
        }
        
        // Afficher les informations de connexion
        echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
        echo "<h3>Informations de connexion :</h3>";
        echo "<p><strong>Nom d'utilisateur :</strong> admin</p>";
        echo "<p><strong>Mot de passe :</strong> " . $current_password . "</p>";
        echo "</div>";
        
        // Bouton pour forcer la déconnexion
        echo "<form method='post' action=''>";
        echo "<button type='submit' name='deconnexion' style='padding: 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer; margin-top: 20px;'>Forcer la déconnexion</button>";
        echo "</form>";
        
        // Traiter la déconnexion
        if (isset($_POST['deconnexion'])) {
            session_start();
            session_destroy();
            echo "<div style='color: green; margin-top: 10px;'>Session détruite avec succès</div>";
            echo "<script>setTimeout(function() { window.location.reload(); }, 1000);</script>";
        }
        
    } else {
        echo "<div style='color: red; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;'>";
        echo "L'utilisateur admin n'a pas été trouvé dans la base de données.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: #721c24; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;'>";
    echo "Erreur : " . $e->getMessage();
    echo "</div>";
}
?> 