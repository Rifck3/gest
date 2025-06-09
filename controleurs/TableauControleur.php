<?php
/**
 * Contrôleur pour le tableau de bord
 */
class TableauControleur {
    private $db;
    private $produit;
    private $mouvement;
    private $fournisseur;
    private $utilisateur;
    
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
        require_once 'modeles/Utilisateur.php';
        
        $this->produit = new Produit($db);
        $this->mouvement = new MouvementStock($db);
        $this->fournisseur = new Fournisseur($db);
        $this->utilisateur = new Utilisateur($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche le tableau de bord
     */
    public function index() {
        // Récupérer les statistiques générales
        $stats = $this->obtenirStatistiques();
        
        // Récupérer les produits en stock faible
        $produits_stock_faible = $this->produit->obtenirStockFaible();
        
        // Récupérer les données pour les graphiques
        $donnees_graphiques = $this->obtenirDonneesGraphiques();
        
        // Débogage
        error_log('Données des graphiques: ' . print_r($donnees_graphiques, true));
        
        // Inclure la vue
        include 'vues/tableau/index.php';
    }
    
    /**
     * Récupère les statistiques générales
     * 
     * @return array
     */
    private function obtenirStatistiques() {
        $stats = [];
        
        // Nombre total de produits
        $stats['total_produits'] = $this->produit->compter();
        
        // Valeur totale du stock
        $stats['valeur_stock'] = $this->produit->calculerValeurStock();
        
        // Nombre de produits en stock faible (mais pas en rupture)
        $query = "SELECT COUNT(*) as count FROM produits WHERE quantite <= quantite_min AND quantite > 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['produits_stock_faible'] = $row['count'];
        
        // Nombre de produits en rupture de stock
        $query = "SELECT COUNT(*) as count FROM produits WHERE quantite <= 0";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['produits_rupture_stock'] = $row['count'];
        
        // Nombre de fournisseurs
        $stats['total_fournisseurs'] = $this->fournisseur->compter();
        
        // Mouvements de stock du mois courant
        $debut_mois = date('Y-m-01');
        $fin_mois = date('Y-m-t');
        
        $query = "SELECT 
                 SUM(CASE WHEN type_mouvement = 'entree' THEN quantite ELSE 0 END) as total_entrees,
                 SUM(CASE WHEN type_mouvement = 'sortie' THEN quantite ELSE 0 END) as total_sorties
                 FROM mouvements_stock
                 WHERE DATE(date_mouvement) BETWEEN ? AND ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$debut_mois, $fin_mois]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stats['entrees_mois'] = $row['total_entrees'] ?? 0;
        $stats['sorties_mois'] = $row['total_sorties'] ?? 0;
        
        return $stats;
    }
    
    /**
     * Récupère les données pour les graphiques
     * 
     * @return array
     */
    private function obtenirDonneesGraphiques() {
        $donnees = [];
        
        // Mouvements de stock des 6 derniers mois
        $date_debut = date('Y-m-d', strtotime('-6 months'));
        $date_fin = date('Y-m-d');
        
        $donnees['mouvements_6mois'] = $this->mouvement->obtenirStatistiquesParJour($date_debut, $date_fin);
        
        // Produits les plus actifs
        $donnees['produits_actifs'] = $this->mouvement->obtenirStatistiquesParProduit($date_debut, $date_fin, 10);
        
        // Répartition des produits par catégorie
        $query = "SELECT c.nom, COUNT(p.id) as total 
                 FROM produits p 
                 JOIN categories c ON p.categorie_id = c.id 
                 GROUP BY c.id 
                 ORDER BY total DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $donnees['produits_par_categorie'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $donnees;
    }
}
