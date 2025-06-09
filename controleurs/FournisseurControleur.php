<?php
/**
 * Contrôleur pour la gestion des fournisseurs
 */
class FournisseurControleur {
    private $db;
    private $fournisseur;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser le modèle
        require_once 'modeles/Fournisseur.php';
        $this->fournisseur = new Fournisseur($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche la liste des fournisseurs
     */
    public function index() {
        // Récupérer tous les fournisseurs
        $stmt = $this->fournisseur->lireTous();
        $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/fournisseurs/index.php';
    }
    
    /**
     * Affiche le formulaire de création d'un fournisseur
     */
    public function creer() {
        // Inclure la vue
        include 'vues/fournisseurs/creer.php';
    }
    
    /**
     * Enregistre un nouveau fournisseur
     */
    public function enregistrer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs du fournisseur
            $this->fournisseur->nom = $_POST['nom'];
            $this->fournisseur->email = $_POST['email'];
            $this->fournisseur->telephone = $_POST['telephone'];
            $this->fournisseur->adresse = $_POST['adresse'];
            $this->fournisseur->notes = $_POST['notes'];
            
            // Créer le fournisseur
            if($this->fournisseur->creer()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('ajout_fournisseur', "Fournisseur ajouté: {$this->fournisseur->nom}");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le fournisseur a été ajouté avec succès.";
                header('Location: index.php?controleur=fournisseur&action=index');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de l'ajout du fournisseur.";
                header('Location: index.php?controleur=fournisseur&action=creer');
                exit;
            }
        }
    }
    
    /**
     * Affiche les détails d'un fournisseur
     */
    public function voir() {
        // Récupérer l'ID du fournisseur
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du fournisseur non spécifié');
        
        // Récupérer les détails du fournisseur
        $fournisseur = $this->fournisseur->lireUn($id);
        
        // Récupérer les produits associés
        $query = "SELECT p.* FROM produits p WHERE p.fournisseur_id = ? ORDER BY p.nom";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les derniers mouvements
        $query = "SELECT ms.*, p.nom as nom_produit, u.nom_complet as utilisateur 
                 FROM mouvements_stock ms 
                 LEFT JOIN produits p ON ms.produit_id = p.id 
                 LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id 
                 WHERE ms.fournisseur_id = ? 
                 ORDER BY ms.date_mouvement DESC 
                 LIMIT 10";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $mouvements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/fournisseurs/voir.php';
    }
    
    /**
     * Affiche le formulaire de modification d'un fournisseur
     */
    public function modifier() {
        // Récupérer l'ID du fournisseur
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du fournisseur non spécifié');
        
        // Récupérer les détails du fournisseur
        $fournisseur = $this->fournisseur->lireUn($id);
        
        // Inclure la vue
        include 'vues/fournisseurs/modifier.php';
    }
    
    /**
     * Met à jour un fournisseur existant
     */
    public function mettreAJour() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs du fournisseur
            $this->fournisseur->id = $_POST['id'];
            $this->fournisseur->nom = $_POST['nom'];
            $this->fournisseur->email = $_POST['email'];
            $this->fournisseur->telephone = $_POST['telephone'];
            $this->fournisseur->adresse = $_POST['adresse'];
            $this->fournisseur->notes = $_POST['notes'];
            
            // Mettre à jour le fournisseur
            if($this->fournisseur->mettreAJour()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('modification_fournisseur', "Fournisseur modifié: {$this->fournisseur->nom} (ID: {$this->fournisseur->id})");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le fournisseur a été mis à jour avec succès.";
                header('Location: index.php?controleur=fournisseur&action=index');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour du fournisseur.";
                header('Location: index.php?controleur=fournisseur&action=modifier&id=' . $this->fournisseur->id);
                exit;
            }
        }
    }
    
    /**
     * Supprime un fournisseur
     */
    public function supprimer() {
        // Récupérer l'ID du fournisseur
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID du fournisseur non spécifié');
        
        // Récupérer les détails du fournisseur pour l'activité
        $fournisseur = $this->fournisseur->lireUn($id);
        
        // Supprimer le fournisseur
        if($this->fournisseur->supprimer($id)) {
            // Enregistrer l'activité
            $this->enregistrerActivite('suppression_fournisseur', "Fournisseur supprimé: {$fournisseur['nom']} (ID: {$id})");
            
            // Rediriger avec un message de succès
            $_SESSION['success'] = "Le fournisseur a été supprimé avec succès.";
        } else {
            // Rediriger avec un message d'erreur
            $_SESSION['error'] = "Une erreur s'est produite lors de la suppression du fournisseur. Vérifiez qu'aucun produit ou mouvement de stock n'est associé à ce fournisseur.";
        }
        
        header('Location: index.php?controleur=fournisseur&action=index');
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
}
