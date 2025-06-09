<?php
/**
 * Contrôleur pour la gestion des commandes
 */
class CommandeControleur {
    private $db;
    private $commande;
    private $produit;
    private $fournisseur;
    private $permissions;
    private $utilisateur;
    private $journal;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser les modèles
        require_once 'modeles/Commande.php';
        require_once 'modeles/Produit.php';
        require_once 'modeles/Fournisseur.php';
        require_once 'includes/Permissions.php';
        require_once 'modeles/Utilisateur.php';
        require_once 'modeles/Journal.php';
        
        $this->commande = new Commande($db);
        $this->produit = new Produit($db);
        $this->fournisseur = new Fournisseur($db);
        $this->permissions = new Permissions();
        $this->utilisateur = new Utilisateur($db);
        $this->journal = new Journal($db);
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche la liste des commandes
     */
    public function index() {
        // Vérifier les permissions
        if (!Permissions::aPermission('voir_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de voir les commandes.";
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }
        
        // Récupérer les filtres
        $filtres = [];
        if (isset($_GET['statut'])) $filtres['statut'] = $_GET['statut'];
        if (isset($_GET['fournisseur_id'])) $filtres['fournisseur_id'] = $_GET['fournisseur_id'];
        if (isset($_GET['date_debut'])) $filtres['date_debut'] = $_GET['date_debut'];
        if (isset($_GET['date_fin'])) $filtres['date_fin'] = $_GET['date_fin'];
        
        // Récupérer les commandes
        global $commandes, $fournisseurs;
        $commandes = $this->commande->lireToutes($filtres);
        $fournisseurs = $this->fournisseur->lireTous();
        
        // Ne pas inclure la vue ici, elle sera incluse par index.php
    }
    
    /**
     * Affiche le formulaire de création d'une commande
     */
    public function creer() {
        // Vérifier les permissions
        if (!Permissions::aPermission('creer_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de créer des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }
        
        // Récupérer les produits et fournisseurs
        global $produits, $fournisseurs;
        $produits = $this->produit->lireTous();
        $fournisseurs = $this->fournisseur->lireTous();
        
        // Ne pas inclure la vue ici, elle sera incluse par index.php
    }
    
    /**
     * Enregistre une nouvelle commande
     */
    public function enregistrer() {
        // Vérifier les permissions
        if (!Permissions::aPermission('gestion_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas les permissions nécessaires pour créer une commande.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Démarrer la transaction
                $this->commande->beginTransaction();
                
                // Définir les valeurs de la commande
                $this->commande->fournisseur_id = $_POST['fournisseur_id'];
                $this->commande->utilisateur_id = $_SESSION['user_id'];
                $this->commande->commentaire = $_POST['commentaire'] ?? null;
                
                // Créer la commande
                $commande_id = $this->commande->creer();
                
                // Enregistrer les détails de la commande
                $montant_total = 0;
                foreach($_POST['produits'] as $produit) {
                    $this->commande->ajouterDetail(
                        $commande_id,
                        $produit['id'],
                        $produit['quantite'],
                        $produit['prix_unitaire']
                    );
                    $montant_total += $produit['quantite'] * $produit['prix_unitaire'];
                }
                
                // Mettre à jour le montant total
                $this->commande->mettreAJourMontantTotal($commande_id, $montant_total);
                
                // Valider la transaction
                $this->commande->commit();
                
                // Enregistrer l'activité
                $this->enregistrerActivite('creation_commande', "Commande créée: {$this->commande->reference}");
                
                // Rediriger avec un message de succès
                $_SESSION['success'] = "La commande a été créée avec succès.";
                header('Location: index.php?controleur=commande&action=index');
                exit;
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $this->commande->rollback();
                
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la création de la commande : " . $e->getMessage();
                header('Location: index.php?controleur=commande&action=creer');
                exit;
            }
        }
    }
    
    /**
     * Affiche les détails d'une commande
     */
    public function voir() {
        // Vérifier les permissions
        if (!Permissions::aPermission('voir_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de voir les détails des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer l'ID de la commande
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $_SESSION['error'] = "ID de commande non spécifié.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer les détails de la commande
        global $commande, $details;
        $commande = $this->commande->lireUn($id);
        
        // Vérifier si la commande existe
        if (!$commande) {
            $_SESSION['error'] = "La commande demandée n'existe pas.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer les détails des produits
        $details = $this->commande->lireDetails($id);
        if ($details === false) {
            $_SESSION['error'] = "Erreur lors de la récupération des détails de la commande.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }
    }
    
    /**
     * Valide une commande
     */
    public function valider() {
        // Vérifier les permissions
        if (!Permissions::aPermission('valider_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de valider des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $_SESSION['error'] = "ID de commande non spécifié.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        try {
            // Démarrer la transaction
            $this->commande->beginTransaction();
            
            // Valider la commande
            if ($this->commande->valider($id)) {
                // Enregistrer l'activité
                $this->enregistrerActivite('validation_commande', "Commande validée: {$this->commande->reference}");
                
                // Valider la transaction
                $this->commande->commit();
                
                $_SESSION['success'] = "La commande a été validée avec succès.";
            } else {
                throw new Exception("Impossible de valider la commande.");
            }
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->commande->rollback();
            $_SESSION['error'] = "Une erreur s'est produite lors de la validation de la commande : " . $e->getMessage();
        }
        
        header('Location: index.php?controleur=commande&action=voir&id=' . $id);
        exit;
    }
    
    /**
     * Annule une commande
     */
    public function annuler() {
        // Vérifier les permissions
        if (!Permissions::aPermission('annuler_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission d'annuler des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }
        
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $_SESSION['error'] = "ID de commande non spécifié.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        try {
            // Démarrer la transaction
            $this->commande->beginTransaction();
            
            // Supprimer la commande
            if ($this->commande->supprimer($id)) {
                // Enregistrer l'activité
                $this->enregistrerActivite('annulation_commande', "Commande supprimée: {$this->commande->reference}");
                
                // Valider la transaction
                $this->commande->commit();
                
                $_SESSION['success'] = "La commande a été supprimée avec succès.";
            } else {
                throw new Exception("Impossible de supprimer la commande.");
            }
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->commande->rollback();
            $_SESSION['error'] = "Une erreur s'est produite lors de la suppression de la commande : " . $e->getMessage();
        }
        
        header('Location: index.php?controleur=commande&action=index');
        exit;
    }
    
    /**
     * Affiche le formulaire de modification d'une commande
     */
    public function modifier() {
        // Vérifier les permissions
        if (!Permissions::aPermission('modifier_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de modifier des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer l'ID de la commande
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $_SESSION['error'] = "ID de commande non spécifié.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer les détails de la commande
        global $commande, $details, $produits, $fournisseurs;
        $commande = $this->commande->lireUn($id);
        
        // Vérifier si la commande existe et est modifiable
        if (!$commande || $commande['statut'] !== 'en_attente') {
            $_SESSION['error'] = "Cette commande ne peut pas être modifiée.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer les détails des produits
        $details = $this->commande->lireDetails($id);
        if ($details === false) {
            $_SESSION['error'] = "Erreur lors de la récupération des détails de la commande.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        // Récupérer les produits et fournisseurs pour le formulaire
        $produits = $this->produit->lireTous();
        $fournisseurs = $this->fournisseur->lireTous();
    }

    /**
     * Met à jour une commande existante
     */
    public function mettreAJour() {
        // Vérifier les permissions
        if (!Permissions::aPermission('modifier_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de modifier des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                if (!$id) {
                    throw new Exception("ID de commande non spécifié.");
                }

                // Vérifier si la commande existe et est modifiable
                $commande = $this->commande->lireUn($id);
                if (!$commande || $commande['statut'] !== 'en_attente') {
                    throw new Exception("Cette commande ne peut pas être modifiée.");
                }

                // Démarrer la transaction
                $this->commande->beginTransaction();

                // Mettre à jour les informations de base
                $this->commande->id = $id;
                $this->commande->fournisseur_id = $_POST['fournisseur_id'];
                $this->commande->commentaire = $_POST['commentaire'] ?? null;

                // Supprimer les anciens détails
                $this->commande->supprimerDetails($id);

                // Ajouter les nouveaux détails
                $montant_total = 0;
                foreach($_POST['produits'] as $produit) {
                    $this->commande->ajouterDetail(
                        $id,
                        $produit['id'],
                        $produit['quantite'],
                        $produit['prix_unitaire']
                    );
                    $montant_total += $produit['quantite'] * $produit['prix_unitaire'];
                }

                // Mettre à jour le montant total
                $this->commande->mettreAJourMontantTotal($id, $montant_total);

                // Valider la transaction
                $this->commande->commit();

                // Enregistrer l'activité
                $this->enregistrerActivite('modification_commande', "Commande modifiée: {$this->commande->reference}");

                // Rediriger avec un message de succès
                $_SESSION['success'] = "La commande a été modifiée avec succès.";
                header('Location: index.php?controleur=commande&action=voir&id=' . $id);
                exit;
            } catch (Exception $e) {
                // Annuler la transaction en cas d'erreur
                $this->commande->rollback();
                
                // Rediriger avec un message d'erreur
                $_SESSION['error'] = "Une erreur s'est produite lors de la modification de la commande : " . $e->getMessage();
                header('Location: index.php?controleur=commande&action=modifier&id=' . $id);
                exit;
            }
        }
    }

    /**
     * Supprime une commande
     */
    public function supprimer() {
        // Vérifier les permissions
        if (!Permissions::aPermission('supprimer_commandes')) {
            $_SESSION['error'] = "Vous n'avez pas la permission de supprimer des commandes.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) {
            $_SESSION['error'] = "ID de commande non spécifié.";
            header('Location: index.php?controleur=commande&action=index');
            exit;
        }

        try {
            // Vérifier si la commande existe et est supprimable
            $commande = $this->commande->lireUn($id);
            if (!$commande || $commande['statut'] !== 'en_attente') {
                throw new Exception("Cette commande ne peut pas être supprimée.");
            }

            // Démarrer la transaction
            $this->commande->beginTransaction();

            // Supprimer la commande
            if ($this->commande->supprimer($id)) {
                // Enregistrer l'activité
                $this->enregistrerActivite('suppression_commande', "Commande supprimée: {$commande['reference']}");
                
                // Valider la transaction
                $this->commande->commit();
                
                $_SESSION['success'] = "La commande a été supprimée avec succès.";
            } else {
                throw new Exception("Impossible de supprimer la commande.");
            }
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->commande->rollback();
            $_SESSION['error'] = "Une erreur s'est produite lors de la suppression de la commande : " . $e->getMessage();
        }
        
        header('Location: index.php?controleur=commande&action=index');
        exit;
    }
    
    /**
     * Enregistre une activité dans le journal
     */
    private function enregistrerActivite($type, $description) {
        $this->journal->enregistrer(
            $_SESSION['user_id'],
            $type,
            $description,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        );
    }
} 