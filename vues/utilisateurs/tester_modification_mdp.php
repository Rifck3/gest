<?php
// Vérifier si l'utilisateur est connecté et a les permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?controleur=auth&action=connexion');
    exit;
}

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Créer une instance de la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<div class='container mt-4'>";
    echo "<h1>Test de modification de mot de passe</h1>";
    
    // 1. Vérifier l'état initial
    echo "<h2>État initial des mots de passe :</h2>";
    $query = "SELECT id, nom_utilisateur, mot_de_passe, role FROM utilisateurs ORDER BY id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $utilisateurs_avant = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<div class='table-responsive'>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='thead-light'>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Nom d'utilisateur</th>";
    echo "<th>Mot de passe (avant)</th>";
    echo "<th>Rôle</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    foreach ($utilisateurs_avant as $utilisateur) {
        echo "<tr>";
        echo "<td>" . $utilisateur['id'] . "</td>";
        echo "<td>" . $utilisateur['nom_utilisateur'] . "</td>";
        echo "<td>" . $utilisateur['mot_de_passe'] . "</td>";
        echo "<td>" . $utilisateur['role'] . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
    
    // 2. Instructions pour tester
    echo "<div class='alert alert-info mt-4'>";
    echo "<h3>Instructions pour tester :</h3>";
    echo "<ol>";
    echo "<li>Allez dans la gestion des utilisateurs</li>";
    echo "<li>Modifiez le mot de passe d'un utilisateur</li>";
    echo "<li>Revenez sur cette page et cliquez sur le bouton de vérification</li>";
    echo "</ol>";
    echo "</div>";
    
    // 3. Formulaire pour vérifier après modification
    echo "<form method='post' class='mt-4'>";
    echo "<div class='card'>";
    echo "<div class='card-body'>";
    echo "<h3>Vérifier après modification :</h3>";
    echo "<button type='submit' name='verifier' class='btn btn-primary'>Vérifier les changements</button>";
    echo "</div>";
    echo "</div>";
    echo "</form>";
    
    // 4. Afficher les changements si le formulaire est soumis
    if (isset($_POST['verifier'])) {
        echo "<h2 class='mt-4'>État après modification :</h2>";
        $stmt->execute();
        $utilisateurs_apres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead class='thead-light'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Nom d'utilisateur</th>";
        echo "<th>Mot de passe (après)</th>";
        echo "<th>Rôle</th>";
        echo "<th>Changement détecté</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($utilisateurs_apres as $utilisateur) {
            $utilisateur_avant = array_filter($utilisateurs_avant, function($u) use ($utilisateur) {
                return $u['id'] == $utilisateur['id'];
            });
            $utilisateur_avant = reset($utilisateur_avant);
            $a_change = $utilisateur_avant['mot_de_passe'] !== $utilisateur['mot_de_passe'];
            $classe = $a_change ? "table-success" : "";
            
            echo "<tr class='" . $classe . "'>";
            echo "<td>" . $utilisateur['id'] . "</td>";
            echo "<td>" . $utilisateur['nom_utilisateur'] . "</td>";
            echo "<td>" . $utilisateur['mot_de_passe'] . "</td>";
            echo "<td>" . $utilisateur['role'] . "</td>";
            echo "<td>" . ($a_change ? "Oui" : "Non") . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        
        // Vérifier si les mots de passe sont en clair
        $tous_en_clair = true;
        foreach ($utilisateurs_apres as $utilisateur) {
            if (strpos($utilisateur['mot_de_passe'], '$2y$') === 0) {
                $tous_en_clair = false;
                break;
            }
        }
        
        $classe_alert = $tous_en_clair ? "alert-success" : "alert-danger";
        echo "<div class='alert " . $classe_alert . " mt-4'>";
        echo "<h3>Résultat de la vérification :</h3>";
        if ($tous_en_clair) {
            echo "<p>✅ Tous les mots de passe sont en clair (non hashés)</p>";
        } else {
            echo "<p>❌ Certains mots de passe sont encore hashés</p>";
        }
        echo "</div>";
    }
    
    echo "</div>"; // Fermeture du container
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger mt-4'>";
    echo "Erreur : " . $e->getMessage();
    echo "</div>";
}
?> 