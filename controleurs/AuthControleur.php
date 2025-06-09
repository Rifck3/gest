<?php
/**
 * Contrôleur pour la gestion de l'authentification
 */
class AuthControleur {
    private $db;
    private $utilisateur;
    
    /**
     * Constructeur
     */
    public function __construct() {
        global $db;
        $this->db = $db;
        
        // Initialiser le modèle utilisateur
        require_once 'modeles/Utilisateur.php';
        $this->utilisateur = new Utilisateur($db);
    }
    
    /**
     * Affiche le formulaire de connexion
     */
    public function connexion() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if(isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }
        
        // Inclure la vue
        include 'vues/auth/connexion.php';
    }
    
    /**
     * Traite la demande de connexion
     */
    public function authentifier() {
        // Vérifier si le formulaire a été soumis
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $identifiant = $_POST['identifiant'];
            $mot_de_passe = $_POST['mot_de_passe'];
            
            // Vérifier si les champs sont remplis
            if(empty($identifiant) || empty($mot_de_passe)) {
                $_SESSION['error'] = "Tous les champs sont obligatoires.";
                header('Location: index.php?controleur=auth&action=connexion');
                exit;
            }
            
            // Vérifier si l'identifiant existe
            $utilisateur = $this->utilisateur->trouverParNomUtilisateur($identifiant);
            
            if($utilisateur) {
                // Vérifier le mot de passe (sans hachage)
                if($mot_de_passe === $utilisateur['mot_de_passe']) {
                    // Vérifier si le compte est actif
                    if(!$utilisateur['actif']) {
                        $_SESSION['error'] = "Ce compte a été désactivé.";
                        header('Location: index.php?controleur=auth&action=connexion');
                        exit;
                    }
                    
                    // Créer la session
                    $_SESSION['user_id'] = $utilisateur['id'];
                    $_SESSION['username'] = $utilisateur['nom_utilisateur'];
                    $_SESSION['nom_complet'] = $utilisateur['nom_complet'];
                    $_SESSION['role'] = $utilisateur['role'];
                    
                    // Enregistrer l'activité
                    $this->enregistrerActivite('connexion', "Connexion réussie");
                    
                    // Mettre à jour la dernière connexion
                    $this->utilisateur->mettreAJourDerniereConnexion($utilisateur['id']);
                    
                    // Rediriger vers le tableau de bord
                    header('Location: index.php?controleur=tableau&action=index');
                    exit;
                } else {
                    $_SESSION['error'] = "Identifiant ou mot de passe incorrect.";
                    header('Location: index.php?controleur=auth&action=connexion');
                    exit;
                }
            } else {
                $_SESSION['error'] = "Identifiant ou mot de passe incorrect.";
                header('Location: index.php?controleur=auth&action=connexion');
                exit;
            }
        } else {
            // Si la méthode n'est pas POST, rediriger vers le formulaire de connexion
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function deconnexion() {
        // Vérifier si l'utilisateur est connecté
        if(isset($_SESSION['user_id'])) {
            // Enregistrer l'activité
            $this->enregistrerActivite('deconnexion', "Déconnexion réussie");
            
            // Détruire la session
            session_unset();
            session_destroy();
        }
        
        // Rediriger vers la page de connexion
        header('Location: index.php?controleur=auth&action=connexion');
        exit;
    }
    
    /**
     * Affiche le formulaire de réinitialisation du mot de passe
     */
    public function motDePasseOublie() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if(isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }
        
        // Inclure la vue
        include 'vues/auth/mot_de_passe_oublie.php';
    }
    
    /**
     * Traite la demande de réinitialisation du mot de passe
     */
    public function envoyerReinitialisationMotDePasse() {
        // Vérifier si le formulaire a été soumis
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer l'email du formulaire
            $email = $_POST['email'];
            
            // Vérifier si l'email est rempli
            if(empty($email)) {
                $_SESSION['error'] = "L'email est obligatoire.";
                header('Location: index.php?controleur=auth&action=motDePasseOublie');
                exit;
            }
            
            // Vérifier si l'email existe
            $utilisateur = $this->utilisateur->trouverParEmail($email);
            
            if($utilisateur) {
                // Générer un token unique
                $token = bin2hex(random_bytes(32));
                
                // Définir la date d'expiration (24 heures)
                $expiration = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                // Enregistrer le token dans la base de données
                if($this->utilisateur->enregistrerTokenReinitialisation($utilisateur['id'], $token, $expiration)) {
                    // Envoyer l'email (à implémenter)
                    // ...
                    
                    // Enregistrer l'activité
                    $this->enregistrerActivite('reinitialisation_demande', "Demande de réinitialisation de mot de passe pour l'utilisateur ID: {$utilisateur['id']}");
                    
                    $_SESSION['success'] = "Un email de réinitialisation a été envoyé à votre adresse email.";
                    header('Location: index.php?controleur=auth&action=connexion');
                    exit;
                } else {
                    $_SESSION['error'] = "Une erreur s'est produite lors de la réinitialisation du mot de passe.";
                    header('Location: index.php?controleur=auth&action=motDePasseOublie');
                    exit;
                }
            } else {
                // Pour des raisons de sécurité, ne pas indiquer si l'email existe ou non
                $_SESSION['success'] = "Si l'adresse email existe dans notre base de données, un email de réinitialisation sera envoyé.";
                header('Location: index.php?controleur=auth&action=connexion');
                exit;
            }
        } else {
            // Si la méthode n'est pas POST, rediriger vers le formulaire de réinitialisation
            header('Location: index.php?controleur=auth&action=motDePasseOublie');
            exit;
        }
    }
    
    /**
     * Affiche le formulaire de réinitialisation du mot de passe
     */
    public function reinitialiserMotDePasse() {
        // Si l'utilisateur est déjà connecté, rediriger vers le tableau de bord
        if(isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }
        
        // Vérifier si le token est fourni
        if(!isset($_GET['token'])) {
            $_SESSION['error'] = "Token invalide.";
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
        
        $token = $_GET['token'];
        
        // Vérifier si le token est valide
        $utilisateur = $this->utilisateur->verifierTokenReinitialisation($token);
        
        if(!$utilisateur) {
            $_SESSION['error'] = "Token invalide ou expiré.";
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
        
        // Inclure la vue
        include 'vues/auth/reinitialiser_mot_de_passe.php';
    }
    
    /**
     * Traite la demande de mise à jour du mot de passe
     */
    public function mettreAJourMotDePasse() {
        // Vérifier si le formulaire a été soumis
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les données du formulaire
            $token = $_POST['token'];
            $mot_de_passe = $_POST['mot_de_passe'];
            $confirmer_mot_de_passe = $_POST['confirmer_mot_de_passe'];
            
            // Vérifier si les mots de passe correspondent
            if($mot_de_passe !== $confirmer_mot_de_passe) {
                $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
                header('Location: index.php?controleur=auth&action=reinitialiserMotDePasse&token=' . $token);
                exit;
            }
            
            // Vérifier si le token est valide
            $utilisateur = $this->utilisateur->verifierTokenReinitialisation($token);
            
            if(!$utilisateur) {
                $_SESSION['error'] = "Token invalide ou expiré.";
                header('Location: index.php?controleur=auth&action=connexion');
                exit;
            }
            
            // Mettre à jour le mot de passe (sans hachage)
            if($this->utilisateur->mettreAJourMotDePasse($utilisateur['id'], $mot_de_passe)) {
                // Supprimer le token
                $this->utilisateur->supprimerTokenReinitialisation($token);
                
                // Enregistrer l'activité
                $this->enregistrerActivite('reinitialisation_mot_de_passe', "Mot de passe réinitialisé pour l'utilisateur ID: {$utilisateur['id']}");
                
                $_SESSION['success'] = "Votre mot de passe a été mis à jour avec succès. Vous pouvez maintenant vous connecter.";
                header('Location: index.php?controleur=auth&action=connexion');
                exit;
            } else {
                $_SESSION['error'] = "Une erreur s'est produite lors de la mise à jour du mot de passe.";
                header('Location: index.php?controleur=auth&action=reinitialiserMotDePasse&token=' . $token);
                exit;
            }
        } else {
            // Si la méthode n'est pas POST, rediriger vers la page de connexion
            header('Location: index.php?controleur=auth&action=connexion');
            exit;
        }
    }
    
    /**
     * Affiche le formulaire de réinitialisation du mot de passe administrateur
     */
    public function reinitialiserAdmin() {
        // Vérifier si l'utilisateur est déjà connecté
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }

        // Traiter le formulaire si soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $new_password = $_POST['new_password'];
                $confirm_password = $_POST['confirm_password'];
                
                // Vérifications
                if ($new_password !== $confirm_password) {
                    throw new Exception("Les mots de passe ne correspondent pas.");
                }
                
                // Mettre à jour le mot de passe de l'administrateur (sans hachage)
                $query = "UPDATE utilisateurs SET mot_de_passe = ? WHERE nom_utilisateur = 'admin'";
                $stmt = $this->db->prepare($query);
                
                if ($stmt->execute([$new_password])) {
                    $_SESSION['success'] = "Mot de passe administrateur modifié avec succès!";
                    header('Location: index.php?controleur=auth&action=connexion');
                    exit;
                } else {
                    throw new Exception("Erreur lors de la modification du mot de passe.");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
        }
        
        // Afficher le formulaire
        include 'vues/auth/reinitialiser_admin.php';
    }
    
    /**
     * Enregistre une activité dans le journal
     */
    private function enregistrerActivite($type_activite, $description) {
        $utilisateur_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        $query = "INSERT INTO journal_activites (utilisateur_id, type_activite, description, date_activite) 
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$utilisateur_id, $type_activite, $description]);
    }
}
