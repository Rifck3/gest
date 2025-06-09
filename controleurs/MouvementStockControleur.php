<?php
/**
 * Contrôleur pour la gestion des mouvements de stock
 */
class MouvementStockControleur {
    private $db;
    private $mouvement;
    private $produit;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser les modèles
        require_once 'modeles/MouvementStock.php';
        require_once 'modeles/Produit.php';
        
        $this->mouvement = new MouvementStock($db);
        $this->produit = new Produit($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche la liste des mouvements de stock
     */
    public function index() {
        // Récupérer les filtres
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $produit_id = isset($_GET['produit_id']) ? $_GET['produit_id'] : '';
        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days'));
        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');
        
        // Récupérer les mouvements de stock
        $mouvements = $this->mouvement->lireTous($type, $produit_id, $date_debut, $date_fin);
        
        // Récupérer tous les produits pour le filtre
        $stmt = $this->produit->lireTous();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/mouvements/index.php';
    }
    
    /**
     * Affiche le formulaire d'ajout d'un mouvement de stock
     */
    public function ajouter() {
        // Récupérer tous les produits
        $stmt = $this->produit->lireTous();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer tous les fournisseurs
        $query = "SELECT id, nom FROM fournisseurs ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/mouvements/ajouter.php';
    }
    
    /**
     * Enregistre un nouveau mouvement de stock
     */
    public function enregistrer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs du mouvement
            $this->mouvement->produit_id = $_POST['produit_id'];
            $this->mouvement->type_mouvement = $_POST['type_mouvement'];
            $this->mouvement->quantite = $_POST['quantite'];
            $this->mouvement->reference = $_POST['reference'] ?? null;
            $this->mouvement->raison = $_POST['raison'];
            $this->mouvement->notes = $_POST['notes'] ?? null;
            $this->mouvement->utilisateur_id = $_SESSION['user_id'];
            
            // Récupérer le produit actuel
            $produit = $this->produit->lireUn($this->mouvement->produit_id);
            $quantite_actuelle = $produit['quantite'];
            
            // Calculer la nouvelle quantité selon le type de mouvement
            if($this->mouvement->type_mouvement == 'entree') {
                $nouvelle_quantite = $quantite_actuelle + $this->mouvement->quantite;
            } else if($this->mouvement->type_mouvement == 'sortie') {
                if($quantite_actuelle < $this->mouvement->quantite) {
                    $_SESSION['error'] = "Quantité insuffisante en stock. Stock actuel: {$quantite_actuelle}.";
                    header('Location: index.php?controleur=mouvement&action=ajouter');
                    exit;
                }
                $nouvelle_quantite = $quantite_actuelle - $this->mouvement->quantite;
            } else {
                $_SESSION['error'] = "Type de mouvement inconnu.";
                header('Location: index.php?controleur=mouvement&action=ajouter');
                exit;
            }
            
            // Créer le mouvement et mettre à jour le stock
            if($this->mouvement->creer() && $this->produit->mettreAJourQuantite($this->mouvement->produit_id, $nouvelle_quantite)) {
                // Enregistrer l'activité
                $type_texte = ($this->mouvement->type_mouvement == 'entree') ? 'Entrée' : 'Sortie';
                $this->enregistrerActivite('mouvement_stock', "{$type_texte} de stock: {$this->mouvement->quantite} unités du produit {$produit['nom']}");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le mouvement de stock a été enregistré avec succès.";
                header('Location: index.php?controleur=mouvement&action=index');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de l'enregistrement du mouvement.";
                header('Location: index.php?controleur=mouvement&action=ajouter');
                exit;
            }
        }
    }
    
    /**
     * Affiche les détails d'un mouvement de stock
     */
    public function voir() {
        // Récupérer l'ID du mouvement
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du mouvement non spécifié');
        
        // Récupérer les détails du mouvement
        $mouvement = $this->mouvement->lireUn($id);
        
        // Inclure la vue
        include 'vues/mouvements/voir.php';
    }
    
    /**
     * Affiche la liste des produits en stock faible
     */
    public function stockFaible() {
        // Récupérer les produits en stock faible
        $produits = $this->produit->obtenirStockFaible();
        
        // Inclure la vue
        include 'vues/produits/stock_faible.php';
    }
    
    /**
     * Affiche le formulaire de modification d'un mouvement de stock
     */
    public function modifier() {
        // Récupérer l'ID du mouvement
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du mouvement non spécifié');
        
        // Récupérer les détails du mouvement
        $mouvement = $this->mouvement->lireUn($id);
        
        // Récupérer tous les produits
        $stmt = $this->produit->lireTous();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/mouvements/modifier.php';
    }
    
    /**
     * Met à jour un mouvement de stock
     */
    public function mettreAJour() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs du mouvement
            $this->mouvement->id = $_POST['id'];
            $this->mouvement->produit_id = $_POST['produit_id'];
            $this->mouvement->type_mouvement = $_POST['type_mouvement'];
            $this->mouvement->quantite = $_POST['quantite'];
            $this->mouvement->reference = $_POST['reference'] ?? null;
            $this->mouvement->raison = $_POST['raison'];
            $this->mouvement->notes = $_POST['notes'] ?? null;
            
            // Récupérer le mouvement original pour la comparaison
            $mouvement_original = $this->mouvement->lireUn($this->mouvement->id);
            
            // Mettre à jour le mouvement
            if($this->mouvement->mettreAJour()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('mouvement_stock', "Modification du mouvement de stock ID: {$this->mouvement->id}");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le mouvement de stock a été modifié avec succès.";
                header('Location: index.php?controleur=mouvement&action=index');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la modification du mouvement.";
                header('Location: index.php?controleur=mouvement&action=modifier&id=' . $this->mouvement->id);
                exit;
            }
        }
    }
    
    /**
     * Supprime un mouvement de stock
     */
    public function supprimer() {
        if(isset($_GET['id'])) {
            $this->mouvement->id = $_GET['id'];
            
            // Récupérer les détails du mouvement avant suppression
            $mouvement = $this->mouvement->lireUn($this->mouvement->id);
            
            if($this->mouvement->supprimer()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('mouvement_stock', "Suppression du mouvement de stock ID: {$this->mouvement->id}");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le mouvement de stock a été supprimé avec succès.";
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la suppression du mouvement.";
            }
        }
        
        header('Location: index.php?controleur=mouvement&action=index');
        exit;
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
    
    public function initialiserMouvements() {
        require_once 'modeles/Produit.php';
        $produitModel = new Produit($this->db);
        $stmt = $produitModel->lireTous();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $nb_crees = 0;
        foreach ($produits as $produit) {
            $mouvements = $this->mouvement->lireParProduit($produit['id'], 1);
            if (empty($mouvements)) {
                $this->mouvement->produit_id = $produit['id'];
                $this->mouvement->type_mouvement = 'entree';
                $this->mouvement->quantite = $produit['quantite'];
                $this->mouvement->reference = 'INIT-' . $produit['id'];
                $this->mouvement->raison = "Initialisation du stock";
                $this->mouvement->notes = "Mouvement généré automatiquement pour initialiser l'historique";
                $this->mouvement->utilisateur_id = $_SESSION['user_id'] ?? 1;
                if ($this->mouvement->creer()) {
                    $nb_crees++;
                    echo "Mouvement créé pour le produit ID " . $produit['id'] . "<br>";
                } else {
                    echo "Erreur lors de la création du mouvement pour le produit ID " . $produit['id'] . "<br>";
                }
            } else {
                echo "Déjà un mouvement pour le produit ID " . $produit['id'] . "<br>";
            }
        }
        echo "$nb_crees mouvements de stock initiaux créés.<br>";
        exit;
    }
}
