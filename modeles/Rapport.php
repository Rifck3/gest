<?php
/**
 * Modèle pour la gestion des rapports
 */
class Rapport {
    // Propriétés de la base de données
    private $conn;
    
    /**
     * Constructeur avec connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère les statistiques de stock par catégorie
     * 
     * @return array
     */
    public function obtenirStatistiquesParCategorie() {
        $query = "SELECT 
                 c.id,
                 c.nom,
                 COUNT(p.id) as nombre_produits,
                 SUM(p.quantite) as quantite_totale,
                 SUM(p.quantite * p.prix_unitaire) as valeur_totale,
                 COUNT(CASE WHEN p.quantite <= p.quantite_min THEN 1 ELSE NULL END) as produits_stock_faible
                 FROM categories c
                 LEFT JOIN produits p ON c.id = p.categorie_id
                 GROUP BY c.id, c.nom
                 ORDER BY nombre_produits DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les statistiques de stock par fournisseur
     * 
     * @return array
     */
    public function obtenirStatistiquesParFournisseur() {
        $query = "SELECT 
                 f.id,
                 f.nom,
                 COUNT(p.id) as nombre_produits,
                 SUM(p.quantite) as quantite_totale,
                 SUM(p.quantite * p.prix_unitaire) as valeur_totale,
                 COUNT(CASE WHEN p.quantite <= p.quantite_min THEN 1 ELSE NULL END) as produits_stock_faible
                 FROM fournisseurs f
                 LEFT JOIN produits p ON f.id = p.fournisseur_id
                 GROUP BY f.id, f.nom
                 ORDER BY nombre_produits DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les statistiques des mouvements de stock par période
     * 
     * @param string $periode 'jour', 'semaine', 'mois' ou 'annee'
     * @param string $date_debut Date de début
     * @param string $date_fin Date de fin
     * @return array
     */
    public function obtenirStatistiquesMouvementsParPeriode($periode, $date_debut, $date_fin) {
        $format = '';
        $label = '';
        
        switch($periode) {
            case 'jour':
                $format = '%Y-%m-%d';
                $label = 'DATE(date_mouvement)';
                break;
            case 'semaine':
                $format = '%Y-%u';
                $label = "CONCAT(YEAR(date_mouvement), '-', WEEK(date_mouvement))";
                break;
            case 'mois':
                $format = '%Y-%m';
                $label = "DATE_FORMAT(date_mouvement, '%Y-%m')";
                break;
            case 'annee':
                $format = '%Y';
                $label = 'YEAR(date_mouvement)';
                break;
            default:
                $format = '%Y-%m-%d';
                $label = 'DATE(date_mouvement)';
        }
        
        $query = "SELECT 
                 DATE_FORMAT(date_mouvement, ?) as periode,
                 {$label} as label,
                 SUM(CASE WHEN type_mouvement = 'entree' THEN quantite ELSE 0 END) as total_entrees,
                 SUM(CASE WHEN type_mouvement = 'sortie' THEN quantite ELSE 0 END) as total_sorties,
                 COUNT(id) as nombre_mouvements
                 FROM mouvements_stock
                 WHERE DATE(date_mouvement) BETWEEN ? AND ?
                 GROUP BY periode
                 ORDER BY periode";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $format);
        $stmt->bindParam(2, $date_debut);
        $stmt->bindParam(3, $date_fin);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les produits les plus actifs (entrées + sorties)
     * 
     * @param string $date_debut Date de début
     * @param string $date_fin Date de fin
     * @param int $limite Nombre de produits à retourner
     * @param int $categorie_id ID de la catégorie (optionnel)
     * @param int $fournisseur_id ID du fournisseur (optionnel)
     * @return array
     */
    public function obtenirProduitsActifs($date_debut, $date_fin, $limite = 10, $categorie_id = null, $fournisseur_id = null) {
        $params = [$date_debut, $date_fin];
        
        $query = "SELECT 
                 ms.produit_id,
                 p.nom as nom_produit,
                 c.nom as nom_categorie,
                 f.nom as nom_fournisseur,
                 p.quantite as stock_actuel,
                 p.quantite_min as stock_min,
                 COUNT(ms.id) as total_mouvements,
                 SUM(CASE WHEN ms.type_mouvement = 'entree' THEN ms.quantite ELSE 0 END) as total_entrees,
                 SUM(CASE WHEN ms.type_mouvement = 'sortie' THEN ms.quantite ELSE 0 END) as total_sorties
                 FROM mouvements_stock ms
                 JOIN produits p ON ms.produit_id = p.id
                 JOIN categories c ON p.categorie_id = c.id
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                 WHERE DATE(ms.date_mouvement) BETWEEN ? AND ?";
        
        if($categorie_id) {
            $query .= " AND p.categorie_id = ?";
            $params[] = $categorie_id;
        }
        
        if($fournisseur_id) {
            $query .= " AND (p.fournisseur_id = ? OR ms.fournisseur_id = ?)";
            $params[] = $fournisseur_id;
            $params[] = $fournisseur_id;
        }
        
        $query .= " GROUP BY ms.produit_id, p.nom, c.nom, f.nom, p.quantite, p.quantite_min
                   ORDER BY total_mouvements DESC
                   LIMIT ?";
        
        $params[] = $limite;
        
        $stmt = $this->conn->prepare($query);
        
        for($i = 0; $i < count($params); $i++) {
            if($i == count($params) - 1) {
                $stmt->bindParam($i + 1, $params[$i], PDO::PARAM_INT);
            } else {
                $stmt->bindParam($i + 1, $params[$i]);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les fournisseurs les plus actifs
     * 
     * @param string $date_debut Date de début
     * @param string $date_fin Date de fin
     * @param int $limite Nombre de fournisseurs à retourner
     * @return array
     */
    public function obtenirFournisseursActifs($date_debut, $date_fin, $limite = 10) {
        $query = "SELECT 
                 f.id,
                 f.nom,
                 COUNT(ms.id) as total_mouvements,
                 SUM(CASE WHEN ms.type_mouvement = 'entree' THEN ms.quantite ELSE 0 END) as total_entrees,
                 COUNT(DISTINCT ms.produit_id) as nombre_produits
                 FROM fournisseurs f
                 JOIN mouvements_stock ms ON f.id = ms.fournisseur_id
                 WHERE DATE(ms.date_mouvement) BETWEEN ? AND ?
                 GROUP BY f.id, f.nom
                 ORDER BY total_mouvements DESC
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date_debut);
        $stmt->bindParam(2, $date_fin);
        $stmt->bindParam(3, $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les produits en rupture ou en stock faible
     * 
     * @param int $categorie_id ID de la catégorie (optionnel)
     * @param int $fournisseur_id ID du fournisseur (optionnel)
     * @return array
     */
    public function obtenirProduitsStockFaible($categorie_id = null, $fournisseur_id = null) {
        $params = [];
        
        $query = "SELECT 
                 p.*,
                 c.nom as nom_categorie,
                 f.nom as nom_fournisseur,
                 CASE 
                    WHEN p.quantite <= 0 THEN 'Rupture'
                    WHEN p.quantite <= p.quantite_min THEN 'Stock faible'
                    ELSE 'Normal'
                 END as statut_stock
                 FROM produits p
                 LEFT JOIN categories c ON p.categorie_id = c.id
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id
                 WHERE p.quantite <= p.quantite_min";
        
        if($categorie_id) {
            $query .= " AND p.categorie_id = ?";
            $params[] = $categorie_id;
        }
        
        if($fournisseur_id) {
            $query .= " AND p.fournisseur_id = ?";
            $params[] = $fournisseur_id;
        }
        
        $query .= " ORDER BY p.quantite ASC, p.nom";
        
        $stmt = $this->conn->prepare($query);
        
        for($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i + 1, $params[$i]);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les données pour le tableau de bord
     * 
     * @return array
     */
    public function obtenirDonneesDashboard() {
        $resultat = [];
        
        // Nombre total de produits
        $query = "SELECT COUNT(*) as total FROM produits";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultat['total_produits'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Nombre de produits en stock faible
        $query = "SELECT COUNT(*) as total FROM produits WHERE quantite <= quantite_min AND quantite > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultat['produits_stock_faible'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Nombre de produits en rupture
        $query = "SELECT COUNT(*) as total FROM produits WHERE quantite <= 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultat['produits_rupture'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Valeur totale du stock
        $query = "SELECT SUM(quantite * prix_unitaire) as total FROM produits";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultat['valeur_stock'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Mouvements récents (30 derniers jours)
        $date_debut = date('Y-m-d', strtotime('-30 days'));
        $date_fin = date('Y-m-d');
        
        // Nombre d'entrées
        $query = "SELECT COUNT(*) as total, SUM(quantite) as quantite_totale 
                 FROM mouvements_stock 
                 WHERE type_mouvement = 'entree' AND DATE(date_mouvement) BETWEEN ? AND ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date_debut);
        $stmt->bindParam(2, $date_fin);
        $stmt->execute();
        $entrees = $stmt->fetch(PDO::FETCH_ASSOC);
        $resultat['total_entrees'] = $entrees['total'];
        $resultat['quantite_entrees'] = $entrees['quantite_totale'];
        
        // Nombre de sorties
        $query = "SELECT COUNT(*) as total, SUM(quantite) as quantite_totale 
                 FROM mouvements_stock 
                 WHERE type_mouvement = 'sortie' AND DATE(date_mouvement) BETWEEN ? AND ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date_debut);
        $stmt->bindParam(2, $date_fin);
        $stmt->execute();
        $sorties = $stmt->fetch(PDO::FETCH_ASSOC);
        $resultat['total_sorties'] = $sorties['total'];
        $resultat['quantite_sorties'] = $sorties['quantite_totale'];
        
        // Nombre de fournisseurs
        $query = "SELECT COUNT(*) as total FROM fournisseurs";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultat['total_fournisseurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Nombre de catégories
        $query = "SELECT COUNT(*) as total FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultat['total_categories'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        return $resultat;
    }
    
    /**
     * Récupère les dernières activités du journal
     * 
     * @param int $limite Nombre d'activités à retourner
     * @return array
     */
    public function obtenirDernieresActivites($limite = 10) {
        $query = "SELECT ja.*, u.nom_complet as utilisateur 
                 FROM journal_activites ja 
                 LEFT JOIN utilisateurs u ON ja.utilisateur_id = u.id 
                 ORDER BY ja.date_activite DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
