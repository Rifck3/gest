<?php
require_once 'config/database.php';

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h1>Gestion du hachage des mots de passe</h1>";
    
    // Traiter le formulaire si soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            $action = $_POST['action'];
            $password = $_POST['password'];
            
            if ($action === 'hasher') {
                // Hasher le mot de passe
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE nom_utilisateur = 'admin'";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$hash])) {
                    echo "<div style='color: green; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;'>";
                    echo "✓ Le mot de passe a été hashé et mis à jour.";
                    echo "</div>";
                }
            } else if ($action === 'desactiver') {
                // Désactiver le hachage (stockage en clair)
                $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE nom_utilisateur = 'admin'";
                $stmt = $db->prepare($query);
                
                if ($stmt->execute([$password])) {
                    echo "<div style='color: orange; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px;'>";
                    echo "⚠ Le mot de passe est maintenant stocké en clair (non recommandé pour la sécurité).";
                    echo "</div>";
                }
            }
        }
    }
    
    // Récupérer l'état actuel
    $query = "SELECT mot_de_passe FROM utilisateurs WHERE nom_utilisateur = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $current_password = $result['mot_de_passe'];
        $is_hashed = strpos($current_password, '$2y$') === 0;
        
        // Afficher l'état actuel
        echo "<div style='margin: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
        echo "<h3>État actuel :</h3>";
        echo "<p>Le mot de passe est " . ($is_hashed ? "<span style='color: green;'>hashé</span>" : "<span style='color: red;'>non hashé</span>") . "</p>";
        echo "</div>";
        
        // Formulaire pour changer le mot de passe
        echo "<div style='margin: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
        echo "<h3>Changer le mot de passe :</h3>";
        echo "<form method='post' action=''>";
        echo "<div style='margin-bottom: 15px;'>";
        echo "<label for='password' style='display: block; margin-bottom: 5px;'>Nouveau mot de passe :</label>";
        echo "<input type='password' id='password' name='password' required style='padding: 8px; width: 100%; max-width: 300px;'>";
        echo "</div>";
        
        echo "<div style='margin-bottom: 15px;'>";
        echo "<label style='display: block; margin-bottom: 5px;'>Action :</label>";
        echo "<select name='action' style='padding: 8px; width: 100%; max-width: 300px;'>";
        echo "<option value='hasher'>Hasher le mot de passe (recommandé)</option>";
        echo "<option value='desactiver'>Désactiver le hachage (non recommandé)</option>";
        echo "</select>";
        echo "</div>";
        
        echo "<button type='submit' style='padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;'>Appliquer</button>";
        echo "</form>";
        echo "</div>";
        
        // Afficher les informations de connexion
        echo "<div style='margin: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;'>";
        echo "<h3>Informations de connexion :</h3>";
        echo "<p><strong>Nom d'utilisateur :</strong> admin</p>";
        echo "<p><strong>Mot de passe actuel :</strong> " . ($is_hashed ? "[HASHÉ]" : $current_password) . "</p>";
        echo "</div>";
        
        // Bouton pour forcer la déconnexion
        echo "<form method='post' action=''>";
        echo "<button type='submit' name='deconnexion' style='padding: 10px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;'>Forcer la déconnexion</button>";
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