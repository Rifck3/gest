<?php
/**
 * Contrôleur pour la gestion des utilisateurs
 */
require_once 'includes/Permissions.php';

class UtilisateurControleur {
    private $db;
    private $utilisateur;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser le modèle
        require_once 'modeles/Utilisateur.php';
        $this->utilisateur = new Utilisateur($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
        
        // Vérifier si l'utilisateur a le rôle d'administrateur
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Vous n'avez pas les droits nécessaires pour accéder à cette page.";
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }
    }
    
    /**
     * Affiche la liste des utilisateurs
     */
    public function index() {
        // Vérifier les permissions
        Permissions::verifierPermission('gestion_utilisateurs');
        
        // Récupérer tous les utilisateurs
        $stmt = $this->utilisateur->lireTous();
        $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/utilisateurs/index.php';
    }
    
    /**
     * Affiche le formulaire de création d'un utilisateur
     */
    public function creer() {
        // Vérifier les permissions
        Permissions::verifierPermission('gestion_utilisateurs');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le rôle est valide
            if (!in_array($_POST['role'], ['admin', 'gestionnaire'])) {
                $_SESSION['error'] = "Rôle invalide.";
                header('Location: index.php?controleur=utilisateur&action=creer');
                exit;
            }
            
            // Définir les propriétés de l'utilisateur
            $this->utilisateur->nom_utilisateur = $_POST['nom_utilisateur'];
            $this->utilisateur->mot_de_passe = $_POST['mot_de_passe'];
            $this->utilisateur->nom_complet = $_POST['nom_complet'];
            $this->utilisateur->email = $_POST['email'];
            $this->utilisateur->role = $_POST['role'];
            $this->utilisateur->actif = isset($_POST['actif']) ? 1 : 0;
            
            // Créer l'utilisateur
            if ($this->utilisateur->creer()) {
                $_SESSION['success'] = "Utilisateur créé avec succès.";
                header('Location: index.php?controleur=utilisateur&action=index');
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la création de l'utilisateur.";
            }
        }
        
        // Inclure la vue
        include 'vues/utilisateurs/creer.php';
    }
    
    /**
     * Affiche les détails d'un utilisateur
     */
    public function voir() {
        // Récupérer l'ID de l'utilisateur
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID de l\'utilisateur non spécifié');
        
        // Récupérer les détails de l'utilisateur
        $utilisateur = $this->utilisateur->lireUn($id);
        
        // Récupérer les dernières activités
        $query = "SELECT * FROM journal_activites 
                 WHERE utilisateur_id = ? 
                 ORDER BY date_creation DESC 
                 LIMIT 20";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $activites = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Inclure la vue
        include 'vues/utilisateurs/voir.php';
    }
    
    /**
     * Affiche le formulaire de modification d'un utilisateur
     */
    public function modifier() {
        // Vérifier les permissions
        Permissions::verifierPermission('gestion_utilisateurs');
        
        if (!isset($_GET['id'])) {
            header('Location: index.php?controleur=utilisateur&action=index');
            exit;
        }
        
        $id = $_GET['id'];
        $utilisateur = $this->utilisateur->lireUn($id);
        
        if (!$utilisateur) {
            $_SESSION['error'] = "Utilisateur non trouvé.";
            header('Location: index.php?controleur=utilisateur&action=index');
                exit;
            }
            
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom_utilisateur = $_POST['nom_utilisateur'];
            $nom_complet = $_POST['nom_complet'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $actif = isset($_POST['actif']) ? 1 : 0;
            
            // Vérifier si le rôle est valide
            if (!in_array($role, ['admin', 'gestionnaire'])) {
                $_SESSION['error'] = "Rôle invalide.";
                header('Location: index.php?controleur=utilisateur&action=modifier&id=' . $id);
                exit;
            }
            
            // Mettre à jour l'utilisateur
            if ($this->utilisateur->mettreAJour($id, $nom_utilisateur, $nom_complet, $email, $role, $actif)) {
                $_SESSION['success'] = "Utilisateur mis à jour avec succès.";
                header('Location: index.php?controleur=utilisateur&action=index');
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la mise à jour de l'utilisateur.";
            }
        }
        
        // Inclure la vue
        include 'vues/utilisateurs/modifier.php';
    }
    
    /**
     * Affiche le formulaire de changement de mot de passe
     */
    public function changerMotDePasse() {
        // Récupérer l'ID de l'utilisateur
        $id = isset($_GET['id']) ? $_GET['id'] : die('ID de l\'utilisateur non spécifié');
        
        // Récupérer les détails de l'utilisateur
        $utilisateur = $this->utilisateur->lireUn($id);
        
        // Inclure la vue
        include 'vues/utilisateurs/changer_mot_de_passe.php';
    }
    
    /**
     * Met à jour le mot de passe d'un utilisateur
     */
    public function mettreAJourMotDePasse() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $mot_de_passe = $_POST['mot_de_passe'];
            $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'];
            
            // Vérifier si les mots de passe correspondent
            if($mot_de_passe !== $confirmer_mot_de_passe) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                header('Location: index.php?controleur=utilisateur&action=changerMotDePasse&id=' . $id);
                exit;
            }
            
            // Mettre à jour le mot de passe (sans hachage)
            if($this->utilisateur->mettreAJourMotDePasse($id, $mot_de_passe)) {
                // Enregistrer l'activité
                $this->enregistrerActivite('modification_mot_de_passe', "Mot de passe modifié pour l'utilisateur ID: {$id}");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "Le mot de passe a été mis à jour avec succès.";
                header('Location: index.php?controleur=utilisateur&action=index');
                exit;
            } else {
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour du mot de passe.";
                header('Location: index.php?controleur=utilisateur&action=changerMotDePasse&id=' . $id);
                exit;
            }
        }
    }
    
    /**
     * Supprime un utilisateur
     */
    public function supprimer() {
        try {
            $id = $_GET['id'];
            $this->utilisateur->supprimer($id);
            $_SESSION['success'] = "Utilisateur supprimé avec succès.";
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: index.php?controleur=utilisateur&action=index');
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
    
    /**
     * Affiche la page de test de modification des mots de passe
     */
    public function testerModificationMdp() {
        // Vérifier les permissions
        Permissions::verifierPermission('gestion_utilisateurs');
        
        // Inclure la vue de test
        include 'vues/utilisateurs/tester_modification_mdp.php';
    }
}
