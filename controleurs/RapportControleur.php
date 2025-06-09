<?php
/**
 * Contrôleur pour la gestion des rapports
 */
class RapportControleur {
    private $db;
    private $produit;
    private $mouvement;
    private $fournisseur;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser les modèles
        require_once 'modeles/Produit.php';
        require_once 'modeles/MouvementStock.php';
        require_once 'modeles/Fournisseur.php';
        
        $this->produit = new Produit($db);
        $this->mouvement = new MouvementStock($db);
        $this->fournisseur = new Fournisseur($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche la page des rapports
     */
    public function index() {
        // Récupérer les catégories pour le filtre
        $query = "SELECT id, nom FROM categories ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les fournisseurs pour le filtre
        $stmt = $this->fournisseur->lireTous();
        $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/rapports/index.php';
    }
    
    /**
     * Génère un rapport sur les mouvements de stock
     */
    public function mouvementsStock() {
        // Récupérer les paramètres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        $type_mouvement = isset($_GET['type_mouvement']) ? $_GET['type_mouvement'] : '';
        $produit_id = isset($_GET['produit_id']) ? $_GET['produit_id'] : '';
        $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
        $fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
        
        // Récupérer les catégories pour le filtre
        $query = "SELECT id, nom FROM categories ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les fournisseurs pour le filtre
        $stmt = $this->fournisseur->lireTous();
        $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Initialiser la variable produit pour la vue
        $produit = $this->produit;
        
        // Construire la requête
        $query = "SELECT ms.*, p.nom as nom_produit, c.nom as nom_categorie, f.nom as nom_fournisseur, u.nom_complet as utilisateur 
                 FROM mouvements_stock ms 
                 LEFT JOIN produits p ON ms.produit_id = p.id 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id 
                 WHERE DATE(ms.date_mouvement) BETWEEN ? AND ?";
        
        $params = [$date_debut, $date_fin];
        
        if($type_mouvement) {
            $query .= " AND ms.type_mouvement = ?";
            $params[] = $type_mouvement;
        }
        
        if($produit_id) {
            $query .= " AND ms.produit_id = ?";
            $params[] = $produit_id;
        }
        
        if($categorie_id) {
            $query .= " AND p.categorie_id = ?";
            $params[] = $categorie_id;
        }
        
        if($fournisseur_id) {
            $query .= " AND p.fournisseur_id = ?";
            $params[] = $fournisseur_id;
        }
        
        $query .= " ORDER BY ms.date_mouvement DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $mouvements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Débogage
        error_log("Nombre de mouvements trouvés : " . count($mouvements));
        error_log("Requête SQL : " . $query);
        error_log("Paramètres : " . print_r($params, true));
        
        // Calculer les totaux
        $total_entrees = 0;
        $total_sorties = 0;
        
        foreach($mouvements as $mouvement) {
            if($mouvement['type_mouvement'] == 'entree') {
                $total_entrees += $mouvement['quantite'];
            } else {
                $total_sorties += $mouvement['quantite'];
            }
        }
        
        // Préparer les données pour le graphique
        $donnees_graphique = $this->mouvement->obtenirStatistiquesParJour($date_debut, $date_fin, $type_mouvement, $produit_id, $categorie_id, $fournisseur_id);
        
        // Enregistrer l'activité
        $this->enregistrerActivite('generation_rapport', "Rapport généré: Mouvements de stock");
        
        // Inclure la vue
        include 'vues/rapports/mouvements_stock.php';
    }
    
    /**
     * Génère un rapport sur l'état du stock
     */
    public function etatStock() {
        // Récupérer les paramètres
        $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
        $fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
        $stock_faible = isset($_GET['stock_faible']) ? $_GET['stock_faible'] : '';
        
        // Construire la requête
        $query = "SELECT p.*, c.nom as nom_categorie, f.nom as nom_fournisseur 
                 FROM produits p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 WHERE 1=1";
        
        $params = [];
        
        if($categorie_id) {
            $query .= " AND p.categorie_id = ?";
            $params[] = $categorie_id;
        }
        
        if($fournisseur_id) {
            $query .= " AND p.fournisseur_id = ?";
            $params[] = $fournisseur_id;
        }
        
        if($stock_faible) {
            $query .= " AND p.quantite <= p.quantite_min";
        }
        
        $query .= " ORDER BY p.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculer les totaux
        $valeur_totale = 0;
        $nombre_produits = count($produits);
        $nombre_stock_faible = 0;
        
        foreach($produits as $produit) {
            $valeur_totale += $produit['quantite'] * $produit['prix_unitaire'];
            
            if($produit['quantite'] <= $produit['quantite_min']) {
                $nombre_stock_faible++;
            }
        }
        
        // Préparer les données pour le graphique
        $query = "SELECT c.nom, COUNT(p.id) as total, SUM(p.quantite * p.prix_unitaire) as valeur 
                 FROM produits p 
                 JOIN categories c ON p.categorie_id = c.id 
                 GROUP BY c.id 
                 ORDER BY total DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $produits_par_categorie = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Enregistrer l'activité
        $this->enregistrerActivite('generation_rapport', "Rapport généré: État du stock");
        
        // Inclure la vue
        include 'vues/rapports/etat_stock.php';
    }
    
    /**
     * Génère un rapport sur l'activité des produits
     */
    public function activiteProduits() {
        // Récupérer les paramètres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
        $fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
        $limite = isset($_GET['limite']) ? $_GET['limite'] : 10;
        
        // Récupérer les produits les plus actifs
        $produits_actifs = $this->mouvement->obtenirStatistiquesParProduit($date_debut, $date_fin, $limite, $categorie_id, $fournisseur_id);
        
        // Enregistrer l'activité
        $this->enregistrerActivite('generation_rapport', "Rapport généré: Activité des produits");
        
        // Inclure la vue
        include 'vues/rapports/activite_produits.php';
    }
    
    /**
     * Génère un rapport sur l'activité des fournisseurs
     */
    public function activiteFournisseurs() {
        // Récupérer les paramètres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        
        // Nouvelle requête pour inclure tous les fournisseurs
        $query = "SELECT 
            f.id,
            f.nom,
            COUNT(ms.id) as total_mouvements,
            SUM(CASE WHEN ms.type_mouvement = 'entree' THEN ms.quantite ELSE 0 END) as total_entrees,
            SUM(CASE WHEN ms.type_mouvement = 'sortie' THEN ms.quantite ELSE 0 END) as total_sorties,
            COUNT(DISTINCT p.id) as nombre_produits
        FROM fournisseurs f
        LEFT JOIN produits p ON p.fournisseur_id = f.id
        LEFT JOIN mouvements_stock ms ON ms.produit_id = p.id AND (ms.id IS NULL OR DATE(ms.date_mouvement) BETWEEN ? AND ?)
        GROUP BY f.id, f.nom
        ORDER BY total_mouvements DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$date_debut, $date_fin]);
        $fournisseurs_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Enregistrer l'activité
        $this->enregistrerActivite('generation_rapport', "Rapport généré: Activité des fournisseurs");
        
        // Inclure la vue
        include 'vues/rapports/activite_fournisseurs.php';
    }
    
    /**
     * Exporte un rapport au format CSV
     */
    public function exporterCSV() {
        // Récupérer le type de rapport
        $type_rapport = isset($_GET['type_rapport']) ? $_GET['type_rapport'] : '';
        
        if(!$type_rapport) {
            $_SESSION['error'] = "Type de rapport non spécifié.";
            header('Location: index.php?controleur=rapport&action=index');
            exit;
        }
        
        // Définir l'en-tête pour le téléchargement
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rapport_' . $type_rapport . '_' . date('Y-m-d') . '.csv"');
        
        // Créer le fichier CSV
        $output = fopen('php://output', 'w');
        
        // Ajouter l'en-tête UTF-8 BOM
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Traiter selon le type de rapport
        switch($type_rapport) {
            case 'mouvements_stock':
                $this->exporterMouvementsStock($output);
                break;
            case 'etat_stock':
                $this->exporterEtatStock($output);
                break;
            case 'activite_produits':
                $this->exporterActiviteProduits($output);
                break;
            case 'activite_fournisseurs':
                $this->exporterActiviteFournisseurs($output);
                break;
            default:
                fclose($output);
                $_SESSION['error'] = "Type de rapport non valide.";
                header('Location: index.php?controleur=rapport&action=index');
                exit;
        }
        
        // Fermer le fichier
        fclose($output);
        exit;
    }
    
    /**
     * Exporte les mouvements de stock au format CSV
     */
    private function exporterMouvementsStock($output) {
        // Récupérer les paramètres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        $type_mouvement = isset($_GET['type_mouvement']) ? $_GET['type_mouvement'] : '';
        $produit_id = isset($_GET['produit_id']) ? $_GET['produit_id'] : '';
        $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
        $fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
        
        // Construire la requête
        $query = "SELECT ms.id, ms.type_mouvement, ms.quantite, ms.date_mouvement, 
                 p.nom as nom_produit, c.nom as nom_categorie, f.nom as nom_fournisseur, u.nom_complet as utilisateur,
                 ms.reference, ms.raison
                 FROM mouvements_stock ms 
                 LEFT JOIN produits p ON ms.produit_id = p.id 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id 
                 WHERE DATE(ms.date_mouvement) BETWEEN ? AND ?";
        
        $params = [$date_debut, $date_fin];
        
        if($type_mouvement) {
            $query .= " AND ms.type_mouvement = ?";
            $params[] = $type_mouvement;
        }
        
        if($produit_id) {
            $query .= " AND ms.produit_id = ?";
            $params[] = $produit_id;
        }
        
        if($categorie_id) {
            $query .= " AND p.categorie_id = ?";
            $params[] = $categorie_id;
        }
        
        if($fournisseur_id) {
            $query .= " AND p.fournisseur_id = ?";
            $params[] = $fournisseur_id;
        }
        
        $query .= " ORDER BY ms.date_mouvement DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        // Écrire l'en-tête du CSV
        fputcsv($output, [
            'Date', 
            'Type de mouvement', 
            'Produit', 
            'Catégorie', 
            'Fournisseur', 
            'Quantité', 
            'Référence', 
            'Raison', 
            'Utilisateur'
        ]);
        
        // Écrire les données
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                date('d/m/Y H:i', strtotime($row['date_mouvement'])),
                ($row['type_mouvement'] == 'entree') ? 'Entrée' : 'Sortie',
                $row['nom_produit'],
                $row['nom_categorie'],
                $row['nom_fournisseur'],
                $row['quantite'],
                $row['reference'],
                $row['raison'],
                $row['utilisateur']
            ]);
        }
        
        // Enregistrer l'activité
        $this->enregistrerActivite('export_rapport', "Rapport exporté: Mouvements de stock (CSV)");
    }
    
    /**
     * Exporte l'état du stock au format CSV
     */
    private function exporterEtatStock($output) {
        // Récupérer les paramètres
        $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
        $fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
        $stock_faible = isset($_GET['stock_faible']) ? $_GET['stock_faible'] : '';
        
        // Construire la requête
        $query = "SELECT p.id, p.nom, p.description, p.prix_unitaire, p.quantite, p.quantite_min, 
                 c.nom as nom_categorie, f.nom as nom_fournisseur, p.date_modification
                 FROM produits p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 WHERE 1=1";
        
        $params = [];
        
        if($categorie_id) {
            $query .= " AND p.categorie_id = ?";
            $params[] = $categorie_id;
        }
        
        if($fournisseur_id) {
            $query .= " AND p.fournisseur_id = ?";
            $params[] = $fournisseur_id;
        }
        
        if($stock_faible) {
            $query .= " AND p.quantite <= p.quantite_min";
        }
        
        $query .= " ORDER BY p.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        // Écrire l'en-tête du CSV
        fputcsv($output, [
            'ID', 
            'Nom', 
            'Description', 
            'Catégorie', 
            'Fournisseur', 
            'Prix unitaire', 
            'Quantité', 
            'Stock minimum', 
            'Valeur', 
            'Statut', 
            'Dernière mise à jour'
        ]);
        
        // Écrire les données
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $valeur = $row['quantite'] * $row['prix_unitaire'];
            
            $statut = 'En stock';
            if($row['quantite'] <= 0) {
                $statut = 'Rupture';
            } elseif($row['quantite'] <= $row['quantite_min']) {
                $statut = 'Stock faible';
            }
            
            fputcsv($output, [
                $row['id'],
                $row['nom'],
                $row['description'],
                $row['nom_categorie'],
                $row['nom_fournisseur'],
                number_format($row['prix_unitaire'], 2, ',', ' '),
                $row['quantite'],
                $row['quantite_min'],
                number_format($valeur, 2, ',', ' '),
                $statut,
                date('d/m/Y H:i', strtotime($row['date_modification']))
            ]);
        }
        
        // Enregistrer l'activité
        $this->enregistrerActivite('export_rapport', "Rapport exporté: État du stock (CSV)");
    }
    
    /**
     * Exporte l'activité des produits au format CSV
     */
    private function exporterActiviteProduits($output) {
        // Récupérer les paramètres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        $categorie_id = isset($_GET['categorie_id']) ? $_GET['categorie_id'] : '';
        $fournisseur_id = isset($_GET['fournisseur_id']) ? $_GET['fournisseur_id'] : '';
        
        // Récupérer les produits les plus actifs
        $produits_actifs = $this->mouvement->obtenirStatistiquesParProduit($date_debut, $date_fin, 100, $categorie_id, $fournisseur_id);
        
        // Écrire l'en-tête du CSV
        fputcsv($output, [
            'ID', 
            'Produit', 
            'Catégorie', 
            'Fournisseur', 
            'Total mouvements', 
            'Entrées', 
            'Sorties', 
            'Stock actuel', 
            'Stock minimum'
        ]);
        
        // Écrire les données
        foreach($produits_actifs as $produit) {
            fputcsv($output, [
                $produit['produit_id'],
                $produit['nom_produit'],
                $produit['nom_categorie'],
                $produit['nom_fournisseur'],
                $produit['total_mouvements'],
                $produit['total_entrees'],
                $produit['total_sorties'],
                $produit['stock_actuel'],
                $produit['stock_min']
            ]);
        }
        
        // Enregistrer l'activité
        $this->enregistrerActivite('export_rapport', "Rapport exporté: Activité des produits (CSV)");
    }
    
    /**
     * Exporte l'activité des fournisseurs au format CSV
     */
    private function exporterActiviteFournisseurs($output) {
        // Récupérer les paramètres
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        
        // Récupérer les statistiques par fournisseur
        $query = "SELECT f.id, f.nom, f.email, f.telephone, f.adresse,
                 COUNT(ms.id) as total_mouvements, 
                 SUM(CASE WHEN ms.type_mouvement = 'entree' THEN ms.quantite ELSE 0 END) as total_entrees,
                 SUM(CASE WHEN ms.type_mouvement = 'sortie' THEN ms.quantite ELSE 0 END) as total_sorties,
                 COUNT(DISTINCT ms.produit_id) as nombre_produits
                 FROM fournisseurs f 
                 LEFT JOIN mouvements_stock ms ON ms.fournisseur_id = f.id 
                 WHERE (ms.id IS NULL OR DATE(ms.date_mouvement) BETWEEN ? AND ?)
                 GROUP BY f.id 
                 ORDER BY total_mouvements DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$date_debut, $date_fin]);
        
        // Écrire l'en-tête du CSV
        fputcsv($output, [
            'ID', 
            'Fournisseur', 
            'Email', 
            'Téléphone', 
            'Adresse', 
            'Total mouvements', 
            'Entrées', 
            'Sorties', 
            'Nombre de produits'
        ]);
        
        // Écrire les données
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, [
                $row['id'],
                $row['nom'],
                $row['email'],
                $row['telephone'],
                $row['adresse'],
                $row['total_mouvements'] ?? 0,
                $row['total_entrees'] ?? 0,
                $row['total_sorties'] ?? 0,
                $row['nombre_produits'] ?? 0
            ]);
        }
        
        // Enregistrer l'activité
        $this->enregistrerActivite('export_rapport', "Rapport exporté: Activité des fournisseurs (CSV)");
    }
    
    /**
     * Enregistre une activité dans le journal
     */
    private function enregistrerActivite($type_activite, $description) {
        $query = "INSERT INTO journal_activites (utilisateur_id, type_activite, description, date_activite) 
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $type_activite, $description]);
    }
}
