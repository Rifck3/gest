<?php
/**
 * Script pour remplir la base de données avec des données de test
 * 
 * Ce script insère des données dans les tables principales :
 * - utilisateurs
 * - catégories
 * - fournisseurs
 * - produits
 * - mouvements de stock
 */

// Inclure la configuration de la base de données
require_once 'config/database.php';

// Créer une instance de la base de données
$database = new Database();
$db = $database->getConnection();

// Fonction pour afficher les messages
function afficherMessage($message, $type = 'info') {
    $couleur = $type === 'success' ? 'green' : ($type === 'error' ? 'red' : 'blue');
    echo "<div style='color: $couleur; margin: 10px 0;'>$message</div>";
}

// Fonction pour vérifier si une table est vide
function tableEstVide($db, $table) {
    $query = "SELECT COUNT(*) as count FROM $table";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] == 0;
}

// Afficher l'en-tête
echo "<!DOCTYPE html>\n<html>\n<head>\n<title>Remplissage de la base de données</title>\n<style>\nbody { font-family: Arial, sans-serif; margin: 20px; }\nh1 { color: #333; }\n</style>\n</head>\n<body>\n<h1>Remplissage de la base de données</h1>";

// Vérifier la connexion à la base de données
try {
    // Créer des utilisateurs
    if (tableEstVide($db, 'utilisateurs') || count($db->query("SELECT * FROM utilisateurs")->fetchAll()) <= 1) {
        afficherMessage("Ajout d'utilisateurs...");
        
        // Hasher les mots de passe
        $mot_de_passe_gestionnaire = password_hash('gestionnaire123', PASSWORD_DEFAULT);
        $mot_de_passe_employe = password_hash('employe123', PASSWORD_DEFAULT);
        
        $query = "INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, nom_complet, email, role) VALUES 
                 ('gestionnaire', :mdp_gestionnaire, 'Jean Dupont', 'jean.dupont@example.com', 'gestionnaire'),
                 ('employe', :mdp_employe, 'Marie Martin', 'marie.martin@example.com', 'employe')";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':mdp_gestionnaire', $mot_de_passe_gestionnaire);
        $stmt->bindParam(':mdp_employe', $mot_de_passe_employe);
        
        if ($stmt->execute()) {
            afficherMessage("Utilisateurs ajoutés avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de l'ajout des utilisateurs.", 'error');
        }
    } else {
        afficherMessage("Des utilisateurs existent déjà dans la base de données.");
    }
    
    // Créer des catégories supplémentaires
    if (tableEstVide($db, 'categories') || count($db->query("SELECT * FROM categories")->fetchAll()) <= 3) {
        afficherMessage("Ajout de catégories...");
        
        $query = "INSERT INTO categories (nom, description) VALUES 
                 ('Informatique', 'Matériel informatique et accessoires'),
                 ('Papeterie', 'Articles de papeterie et fournitures'),
                 ('Outillage', 'Outils et équipements'),
                 ('Consommables', 'Produits consommables divers'),
                 ('Équipement de bureau', 'Mobilier et équipement pour bureau')";
        
        if ($db->exec($query)) {
            afficherMessage("Catégories ajoutées avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de l'ajout des catégories.", 'error');
        }
    } else {
        afficherMessage("Des catégories existent déjà dans la base de données.");
    }
    
    // Créer des fournisseurs supplémentaires
    if (tableEstVide($db, 'fournisseurs') || count($db->query("SELECT * FROM fournisseurs")->fetchAll()) <= 3) {
        afficherMessage("Ajout de fournisseurs...");
        
        $query = "INSERT INTO fournisseurs (nom, personne_contact, telephone, email, adresse) VALUES 
                 ('InfoTech', 'Sophie Dubois', '0123456789', 'contact@infotech.com', '10 Rue de l\'Informatique, Paris'),
                 ('BureauPlus', 'Thomas Leroy', '0234567890', 'info@bureauplus.com', '25 Avenue des Bureaux, Lyon'),
                 ('OutilPro', 'Lucas Martin', '0345678901', 'contact@outilpro.com', '5 Rue des Outils, Marseille'),
                 ('PapierExpress', 'Emma Petit', '0456789012', 'info@papierexpress.com', '15 Boulevard du Papier, Lille'),
                 ('MobilierBureau', 'Hugo Bernard', '0567890123', 'contact@mobilierbureau.com', '30 Rue du Mobilier, Bordeaux')";
        
        if ($db->exec($query)) {
            afficherMessage("Fournisseurs ajoutés avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de l'ajout des fournisseurs.", 'error');
        }
    } else {
        afficherMessage("Des fournisseurs existent déjà dans la base de données.");
    }
    
    // Récupérer les IDs des catégories et fournisseurs
    $categories = $db->query("SELECT id FROM categories")->fetchAll(PDO::FETCH_COLUMN);
    $fournisseurs = $db->query("SELECT id FROM fournisseurs")->fetchAll(PDO::FETCH_COLUMN);
    
    // Créer des produits
    if (tableEstVide($db, 'produits')) {
        afficherMessage("Ajout de produits...");
        
        $produits = [
            ['Ordinateur portable', 'Ordinateur portable 15 pouces', $categories[0], $fournisseurs[0], 899.99, 10, 3],
            ['Souris sans fil', 'Souris ergonomique sans fil', $categories[0], $fournisseurs[0], 29.99, 25, 5],
            ['Clavier mécanique', 'Clavier mécanique rétroéclairé', $categories[0], $fournisseurs[0], 79.99, 15, 3],
            ['Écran 24 pouces', 'Écran HD 24 pouces', $categories[0], $fournisseurs[0], 199.99, 8, 2],
            ['Stylos bleus', 'Lot de 50 stylos à bille bleus', $categories[1], $fournisseurs[3], 12.99, 30, 10],
            ['Cahiers A4', 'Lot de 10 cahiers A4 quadrillés', $categories[1], $fournisseurs[3], 15.99, 20, 5],
            ['Classeurs', 'Lot de 5 classeurs à levier', $categories[1], $fournisseurs[3], 19.99, 15, 5],
            ['Marteaux', 'Marteau de charpentier', $categories[2], $fournisseurs[2], 24.99, 10, 3],
            ['Tournevis', 'Set de tournevis de précision', $categories[2], $fournisseurs[2], 18.99, 12, 4],
            ['Perceuse', 'Perceuse sans fil 18V', $categories[2], $fournisseurs[2], 129.99, 5, 2],
            ['Cartouches d\'encre', 'Cartouches d\'encre pour imprimante', $categories[3], $fournisseurs[0], 49.99, 15, 5],
            ['Papier A4', 'Ramette de papier A4 500 feuilles', $categories[3], $fournisseurs[3], 5.99, 50, 10],
            ['Toner', 'Toner pour imprimante laser', $categories[3], $fournisseurs[0], 79.99, 8, 3],
            ['Bureau', 'Bureau de travail 120x60cm', $categories[4], $fournisseurs[4], 199.99, 5, 2],
            ['Chaise de bureau', 'Chaise de bureau ergonomique', $categories[4], $fournisseurs[4], 149.99, 8, 3],
            ['Lampe de bureau', 'Lampe de bureau LED ajustable', $categories[4], $fournisseurs[4], 39.99, 12, 4]
        ];
        
        $query = "INSERT INTO produits (nom, description, categorie_id, fournisseur_id, prix_unitaire, quantite, quantite_min) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        $produits_ajoutes = 0;
        foreach ($produits as $produit) {
            if ($stmt->execute($produit)) {
                $produits_ajoutes++;
            }
        }
        
        if ($produits_ajoutes > 0) {
            afficherMessage("$produits_ajoutes produits ajoutés avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de l'ajout des produits.", 'error');
        }
    } else {
        afficherMessage("Des produits existent déjà dans la base de données.");
    }
    
    // Créer des mouvements de stock
    if (tableEstVide($db, 'mouvements_stock')) {
        afficherMessage("Ajout de mouvements de stock...");
        
        // Récupérer les IDs des produits et utilisateurs
        $produits = $db->query("SELECT id FROM produits")->fetchAll(PDO::FETCH_COLUMN);
        $utilisateurs = $db->query("SELECT id FROM utilisateurs")->fetchAll(PDO::FETCH_COLUMN);
        
        $mouvements = [];
        
        // Générer des mouvements d'entrée
        foreach ($produits as $produit_id) {
            // Entrée initiale
            $mouvements[] = [
                $produit_id,
                'entree',
                rand(5, 20),
                'REF-E-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'Approvisionnement initial',
                'Livraison standard',
                $utilisateurs[rand(0, count($utilisateurs) - 1)]
            ];
            
            // Entrée supplémentaire pour certains produits
            if (rand(0, 1)) {
                $mouvements[] = [
                    $produit_id,
                    'entree',
                    rand(3, 10),
                    'REF-E-' . str_pad(rand(1000, 1999), 3, '0', STR_PAD_LEFT),
                    'Réapprovisionnement',
                    'Commande urgente',
                    $utilisateurs[rand(0, count($utilisateurs) - 1)]
                ];
            }
        }
        
        // Générer des mouvements de sortie
        foreach ($produits as $produit_id) {
            // Sortie pour certains produits
            if (rand(0, 1)) {
                $mouvements[] = [
                    $produit_id,
                    'sortie',
                    rand(1, 5),
                    'REF-S-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'Utilisation interne',
                    'Demande service technique',
                    $utilisateurs[rand(0, count($utilisateurs) - 1)]
                ];
            }
            
            // Sortie supplémentaire pour certains produits
            if (rand(0, 1)) {
                $mouvements[] = [
                    $produit_id,
                    'sortie',
                    rand(1, 3),
                    'REF-S-' . str_pad(rand(1000, 1999), 3, '0', STR_PAD_LEFT),
                    'Vente client',
                    'Commande client',
                    $utilisateurs[rand(0, count($utilisateurs) - 1)]
                ];
            }
        }
        
        $query = "INSERT INTO mouvements_stock (produit_id, type_mouvement, quantite, reference, raison, notes, utilisateur_id, date_mouvement) VALUES (?, ?, ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))";
        $stmt = $db->prepare($query);
        
        $mouvements_ajoutes = 0;
        foreach ($mouvements as $mouvement) {
            // Ajouter un intervalle aléatoire pour la date
            $mouvement[] = rand(0, 30); // Jusqu'à 30 jours dans le passé
            if ($stmt->execute($mouvement)) {
                $mouvements_ajoutes++;
            }
        }
        
        if ($mouvements_ajoutes > 0) {
            afficherMessage("$mouvements_ajoutes mouvements de stock ajoutés avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de l'ajout des mouvements de stock.", 'error');
        }
        
        // Mettre à jour les quantités de produits en fonction des mouvements
        afficherMessage("Mise à jour des quantités de produits...");
        
        $query = "UPDATE produits p SET p.quantite = (
                 SELECT COALESCE(SUM(CASE WHEN ms.type_mouvement = 'entree' THEN ms.quantite ELSE -ms.quantite END), 0)
                 FROM mouvements_stock ms WHERE ms.produit_id = p.id
                 )";
        
        if ($db->exec($query)) {
            afficherMessage("Quantités de produits mises à jour avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de la mise à jour des quantités de produits.", 'error');
        }
    } else {
        afficherMessage("Des mouvements de stock existent déjà dans la base de données.");
    }
    
    // Créer des entrées dans le journal d'activités
    if (tableEstVide($db, 'journal_activites')) {
        afficherMessage("Ajout d'entrées dans le journal d'activités...");
        
        // Récupérer les IDs des utilisateurs
        $utilisateurs = $db->query("SELECT id FROM utilisateurs")->fetchAll(PDO::FETCH_COLUMN);
        
        $activites = [
            ['connexion', 'Connexion au système'],
            ['ajout_produit', 'Ajout d\'un nouveau produit'],
            ['modification_produit', 'Modification d\'un produit existant'],
            ['ajout_fournisseur', 'Ajout d\'un nouveau fournisseur'],
            ['entree_stock', 'Enregistrement d\'une entrée de stock'],
            ['sortie_stock', 'Enregistrement d\'une sortie de stock'],
            ['consultation_rapport', 'Consultation d\'un rapport']
        ];
        
        $query = "INSERT INTO journal_activites (utilisateur_id, type_activite, description, date_activite) VALUES (?, ?, ?, DATE_SUB(NOW(), INTERVAL ? DAY))";
        $stmt = $db->prepare($query);
        
        $activites_ajoutees = 0;
        for ($i = 0; $i < 30; $i++) { // Ajouter 30 activités
            $activite = $activites[rand(0, count($activites) - 1)];
            $params = [
                $utilisateurs[rand(0, count($utilisateurs) - 1)],
                $activite[0],
                $activite[1],
                rand(0, 30) // Jusqu'à 30 jours dans le passé
            ];
            
            if ($stmt->execute($params)) {
                $activites_ajoutees++;
            }
        }
        
        if ($activites_ajoutees > 0) {
            afficherMessage("$activites_ajoutees entrées ajoutées au journal d'activités avec succès!", 'success');
        } else {
            afficherMessage("Erreur lors de l'ajout des entrées au journal d'activités.", 'error');
        }
    } else {
        afficherMessage("Des entrées existent déjà dans le journal d'activités.");
    }
    
    afficherMessage("Remplissage de la base de données terminé!", 'success');
    
} catch (PDOException $e) {
    afficherMessage("Erreur de connexion à la base de données: " . $e->getMessage(), 'error');
}

// Afficher le pied de page
echo "<p><a href='index.php'>Retour à l'accueil</a></p>\n</body>\n</html>";
?>