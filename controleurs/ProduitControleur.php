<?php
require_once __DIR__ . '/../includes/functions.php';

/**
 * Contrôleur pour la gestion des produits
 */
class ProduitControleur {
    private $db;
    private $produit;
    private $categorie;
    private $fournisseur;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser les modèles
        require_once 'modeles/Produit.php';
        require_once 'modeles/Categorie.php';
        require_once 'modeles/Fournisseur.php';
        
        $this->produit = new Produit($db);
        $this->categorie = new Categorie($db);
        $this->fournisseur = new Fournisseur($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche la liste des produits
     */
    public function index() {
        // Récupérer tous les produits
        $produits = $this->produit->lireTous();
        
        // Inclure la vue
        include 'vues/produits/index.php';
    }
    
    /**
     * Affiche le formulaire de création d'un produit
     */
    public function creer() {
        // Récupérer les catégories
        $query = "SELECT id, nom FROM categories ORDER BY nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les fournisseurs
        $stmt = $this->fournisseur->lireTous();
        $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/produits/creer.php';
    }

    /**
     * Enregistre un nouveau produit
     */
    public function enregistrer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Vérifier si tous les champs requis sont remplis
                if(empty($_POST['nom']) || empty($_POST['fournisseur_id'])) {
                    throw new Exception("Le nom du produit et le fournisseur sont obligatoires.");
                }

                // Créer le produit dans une transaction
                $this->db->beginTransaction();
                $nom_produit = trim($_POST['nom']);
                $this->produit->nom = $nom_produit;
                $this->produit->description = $_POST['description'];
                $this->produit->categorie_id = $_POST['categorie_id'];
                $this->produit->fournisseur_id = $_POST['fournisseur_id'];
                $this->produit->prix_unitaire = $_POST['prix_unitaire'];
                $this->produit->quantite = $_POST['quantite'];
                $this->produit->quantite_min = $_POST['quantite_min'];
                $this->produit->reference = $_POST['reference'];
                $produit_id = $this->produit->creer();
                $this->db->commit(); // On valide la création du produit
                
                if($produit_id) {
                    // Mouvement de stock automatique
                    require_once 'modeles/MouvementStock.php';
                    $mouvement = new MouvementStock($this->db);
                    $mouvement->produit_id = $produit_id;
                    $mouvement->type_mouvement = 'entree';
                    $mouvement->quantite = $_POST['quantite'];
                    $mouvement->reference = $_POST['reference'];
                    $mouvement->raison = $_POST['raison'];
                    $mouvement->notes = "Première entrée en stock lors de la création du produit " . $nom_produit;
                    $mouvement->utilisateur_id = $_SESSION['user_id'];
                    $mouvement->creer();
                    
                    $this->enregistrerActivite('ajout_produit', "Produit ajouté: " . $nom_produit);
                    $_SESSION['success'] = "Le produit a été ajouté avec succès.";
                    header('Location: index.php?controleur=produit&action=index');
                    exit;
                }
            } catch (PDOException $e) {
                // Annuler la transaction en cas d'erreur
                $this->db->rollback();
                
                // Gestion de l'erreur d'unicité par code SQLSTATE
                if ($e->getCode() === '23000') {
                    $_SESSION['error'] = "Produit déjà existant.";
                } else {
                    $_SESSION['error'] = $e->getMessage();
                }
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?controleur=produit&action=creer');
                exit;
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $this->db->rollback();
                
                $_SESSION['error'] = $e->getMessage();
                $_SESSION['form_data'] = $_POST;
                header('Location: index.php?controleur=produit&action=creer');
                exit;
            }
        }
    }
    
    /**
     * Affiche les détails d'un produit
     */
    public function voir() {
        // Récupérer l'ID du produit
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du produit non spécifié');
        
        // Récupérer les détails du produit
        $produit = $this->produit->lireUn($id);
        
        // Récupérer l'historique des mouvements
        $query = "SELECT sm.*, u.nom_complet as utilisateur 
                 FROM mouvements_stock sm 
                 LEFT JOIN utilisateurs u ON sm.utilisateur_id = u.id 
                 WHERE sm.produit_id = ? 
                 ORDER BY sm.date_mouvement DESC 
                 LIMIT 10";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $mouvements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/produits/voir.php';
    }
    
    
    /**
     * Met à jour un produit existant
     */
    public function mettreAJour() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs du produit
            $this->produit->id = $_POST['id'];
            $this->produit->nom = $_POST['nom'];
            $this->produit->description = $_POST['description'];
            $this->produit->categorie_id = $_POST['categorie_id'];
            $this->produit->fournisseur_id = $_POST['fournisseur_id'];
            $this->produit->prix_unitaire = $_POST['prix_unitaire'];
            $this->produit->quantite = $_POST['quantite'];
            $this->produit->quantite_min = $_POST['quantite_min'];
            
            // Mettre à jour le produit
            if($this->produit->mettreAJour()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('modification_produit', "Produit modifié: {$this->produit->nom} (ID: {$this->produit->id})");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le produit a été mis à jour avec succès.";
                header('Location: index.php?controleur=produit&action=index');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour du produit.";
                header('Location: index.php?controleur=produit&action=modifier&id=' . $this->produit->id);
                exit;
            }
        }
    }
    
    /**
     * Supprime un produit
     */
    public function supprimer() {
        // Récupérer l'ID du produit
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du produit non spécifié');
        
        // Récupérer les détails du produit pour l'activité
        $produit = $this->produit->lireUn($id);
        
        try {
            // Supprimer le produit, ses détails de commande et ses mouvements de stock
            if($this->produit->supprimer($id)) {
                // Enregistrer l'activité
                $this->enregistrerActivite('suppression_produit', "Produit supprimé: {$produit['nom']} (ID: {$id})");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le produit, ses détails de commande et ses mouvements de stock ont été supprimés avec succès.";
            }
        } catch (Exception $e) {
            // Rediriger avec un message d'erreur détaillé
            $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
        }
        
        header('Location: index.php?controleur=produit&action=index');
        exit;
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
     * Affiche la liste des produits en rupture de stock
     */
    public function ruptureStock() {
        // Récupérer les produits en rupture de stock
        $query = "SELECT p.*, c.nom as nom_categorie, f.nom as nom_fournisseur 
                 FROM produits p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 WHERE p.quantite <= 0 
                 ORDER BY p.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/produits/rupture_stock.php';
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
    
public function modifier() {
    // Récupérer l'ID du produit
    $id = isset($_GET['id']) ? $_GET['id'] : '';
    
    if(!$id) {
        $_SESSION['error'] = "ID du produit non spécifié";
        header("Location: index.php?controleur=produit&action=index");
        exit();
    }
    
    // Récupérer les informations du produit
    $produit = $this->produit->lire($id);
    
    if(!$produit) {
        $_SESSION['error'] = "Produit non trouvé";
        header("Location: index.php?controleur=produit&action=index");
        exit();
    }
    
    // Récupérer les catégories et fournisseurs
    $categories = $this->categorie->lireTous()->fetchAll();
    $fournisseurs = $this->fournisseur->lireTous()->fetchAll();
    
    // Inclure la vue
    include 'vues/produits/modifier.php';
}
}
