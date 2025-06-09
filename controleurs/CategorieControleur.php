<?php
/**
 * Contrôleur pour la gestion des catégories
 */
class CategorieControleur {
    private $db;
    private $categorie;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser le modèle
        require_once 'modeles/Categorie.php';
        $this->categorie = new Categorie($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche la liste des catégories
     */
    public function index() {
        // Récupérer toutes les catégories
        $categories = $this->categorie->lireTous();
        
        // Inclure la vue
        include 'vues/categories/index.php';
    }
    
    /**
     * Enregistre une nouvelle catégorie
     */
    public function enregistrer() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs de la catégorie
            $this->categorie->nom = $_POST['nom'];
            $this->categorie->description = $_POST['description'];
            
            // Créer la catégorie
            if($this->categorie->creer()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('ajout_categorie', "Catégorie ajoutée: {$this->categorie->nom}");
                
                // Répondre avec un message de succès (pour les requêtes AJAX)
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    echo json_encode(['success' => true, 'message' => 'La catégorie a été ajoutée avec succès.']);
                    exit;
                }
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "La catégorie a été ajoutée avec succès.";
                header('Location: index.php?controleur=categorie&action=index');
                exit;
            } else {
                // Répondre avec un message d'erreur (pour les requêtes AJAX)
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    echo json_encode(['success' => false, 'message' => 'Cette catégorie existe déjà.']);
                    exit;
                }
                
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Cette catégorie existe déjà.";
                header('Location: index.php?controleur=categorie&action=index');
                exit;
            }
        }
    }
    
    /**
     * Met à jour une catégorie existante
     */
    public function mettreAJour() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Définir les valeurs de la catégorie
            $this->categorie->id = $_POST['id'];
            $this->categorie->nom = $_POST['nom'];
            $this->categorie->description = $_POST['description'];
            
            // Mettre à jour la catégorie
            if($this->categorie->mettreAJour()) {
                // Enregistrer l'activité
                $this->enregistrerActivite('modification_categorie', "Catégorie modifiée: {$this->categorie->nom} (ID: {$this->categorie->id})");
                
                // Répondre avec un message de succès (pour les requêtes AJAX)
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    echo json_encode(['success' => true, 'message' => 'La catégorie a été mise à jour avec succès.']);
                    exit;
                }
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "La catégorie a été mise à jour avec succès.";
                header('Location: index.php?controleur=categorie&action=index');
                exit;
            } else {
                // Répondre avec un message d'erreur (pour les requêtes AJAX)
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                    echo json_encode(['success' => false, 'message' => 'Une erreur s\'est produite lors de la mise à jour de la catégorie.']);
                    exit;
                }
                
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour de la catégorie.";
                header('Location: index.php?controleur=categorie&action=index');
                exit;
            }
        }
    }
    
    /**
     * Supprime une catégorie
     */
    public function supprimer() {
        // Récupérer l'ID de la catégorie
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID de la catégorie non spécifié');
        
        // Récupérer les détails de la catégorie pour l'activité
        $categorie = $this->categorie->lireUn($id);
        
        try {
        // Supprimer la catégorie
        if($this->categorie->supprimer($id)) {
            // Enregistrer l'activité
            $this->enregistrerActivite('suppression_categorie', "Catégorie supprimée: {$categorie['nom']} (ID: {$id})");
            
            // Répondre avec un message de succès (pour les requêtes AJAX)
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => true, 'message' => 'La catégorie a été supprimée avec succès.']);
                exit;
            }
            
            // Rediriger avec un message de succès
            $_SESSION['success'] = "La catégorie a été supprimée avec succès.";
            }
        } catch (Exception $e) {
            // Répondre avec un message d'erreur (pour les requêtes AJAX)
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
            
            // Rediriger avec un message d'erreur
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: index.php?controleur=categorie&action=index');
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
