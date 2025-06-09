<?php
/**
 * Modèle pour la gestion des utilisateurs
 */
class Utilisateur {
    // Propriétés de la base de données
    private $conn;
    private $table = "utilisateurs";
    
    // Propriétés de l'objet
    public $id;
    public $nom_utilisateur;
    public $mot_de_passe;
    public $nom_complet;
    public $email;
    public $role;
    public $actif;
    public $derniere_connexion;
    public $date_creation;
    public $date_modification;
    
    /**
     * Constructeur avec connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère tous les utilisateurs
     * 
     * @return PDOStatement
     */
    public function lireTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nom_complet";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Récupère un seul utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @return array
     */
    public function lireUn($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un utilisateur par son nom d'utilisateur
     * 
     * @param string $nom_utilisateur Nom d'utilisateur de l'utilisateur
     * @return array|false
     */
    public function trouverParNomUtilisateur($nom_utilisateur) {
        $query = "SELECT * FROM " . $this->table . " WHERE nom_utilisateur = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $nom_utilisateur);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un utilisateur par son email
     * 
     * @param string $email Email de l'utilisateur
     * @return array|false
     */
    public function trouverParEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @return bool
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                 (nom_utilisateur, mot_de_passe, nom_complet, email, role, actif, date_creation, date_modification) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->nom_utilisateur = htmlspecialchars(strip_tags($this->nom_utilisateur));
        $this->nom_complet = htmlspecialchars(strip_tags($this->nom_complet));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->actif = $this->actif ? 1 : 0;
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom_utilisateur);
        $stmt->bindParam(2, $this->mot_de_passe);
        $stmt->bindParam(3, $this->nom_complet);
        $stmt->bindParam(4, $this->email);
        $stmt->bindParam(5, $this->role);
        $stmt->bindParam(6, $this->actif);
        
        // Exécuter la requête
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Met à jour un utilisateur
     * 
     * @return bool
     */
    public function mettreAJour() {
        $query = "UPDATE " . $this->table . " 
                 SET nom_utilisateur = ?, nom_complet = ?, email = ?, role = ?, actif = ?, date_modification = NOW() 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->nom_utilisateur = htmlspecialchars(strip_tags($this->nom_utilisateur));
        $this->nom_complet = htmlspecialchars(strip_tags($this->nom_complet));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->actif = $this->actif ? 1 : 0;
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom_utilisateur);
        $stmt->bindParam(2, $this->nom_complet);
        $stmt->bindParam(3, $this->email);
        $stmt->bindParam(4, $this->role);
        $stmt->bindParam(5, $this->actif);
        $stmt->bindParam(6, $this->id);
        
        // Exécuter la requête
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Met à jour le mot de passe d'un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param string $mot_de_passe Nouveau mot de passe (déjà hashé)
     * @return bool
     */
    public function mettreAJourMotDePasse($id, $mot_de_passe) {
        $query = "UPDATE " . $this->table . " 
                 SET mot_de_passe = ?, date_modification = NOW() 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Lier les valeurs
        $stmt->bindParam(1, $mot_de_passe);
        $stmt->bindParam(2, $id);
        
        // Exécuter la requête
        return $stmt->execute();
    }
    
    /**
     * Met à jour la date de dernière connexion
     * 
     * @param int $id ID de l'utilisateur
     * @return bool
     */
    public function mettreAJourDerniereConnexion($id) {
        $query = "UPDATE " . $this->table . " 
                 SET derniere_connexion = NOW() 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        return $stmt->execute();
    }
    
    /**
     * Supprime un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @return bool
     */
    public function supprimer($id) {
        // Vérifier s'il y a des commandes associées
        $query = "SELECT COUNT(*) as count FROM commandes WHERE utilisateur_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['count'] > 0) {
            throw new Exception("Impossible de supprimer cet utilisateur car il a des commandes associées.");
        }
        // Suppression réelle
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
    
    /**
     * Recherche des utilisateurs
     * 
     * @param string $terme Terme de recherche
     * @return PDOStatement
     */
    public function rechercher($terme) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE nom_utilisateur LIKE ? OR nom_complet LIKE ? OR email LIKE ? 
                 ORDER BY nom_complet";
        
        $terme = "%{$terme}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $terme);
        $stmt->bindParam(2, $terme);
        $stmt->bindParam(3, $terme);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Enregistre un token de réinitialisation de mot de passe
     * 
     * @param int $utilisateur_id ID de l'utilisateur
     * @param string $token Token unique
     * @param string $expiration Date d'expiration
     * @return bool
     */
    public function enregistrerTokenReinitialisation($utilisateur_id, $token, $expiration) {
        // D'abord, supprimer tout token existant pour cet utilisateur
        $query = "DELETE FROM tokens_reinitialisation WHERE utilisateur_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $utilisateur_id);
        $stmt->execute();
        
        // Ensuite, insérer le nouveau token
        $query = "INSERT INTO tokens_reinitialisation (utilisateur_id, token, expiration, date_creation) 
                 VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $utilisateur_id);
        $stmt->bindParam(2, $token);
        $stmt->bindParam(3, $expiration);
        
        return $stmt->execute();
    }
    
    /**
     * Vérifie si un token de réinitialisation est valide
     * 
     * @param string $token Token à vérifier
     * @return array|false Données de l'utilisateur si valide, false sinon
     */
    public function verifierTokenReinitialisation($token) {
        $query = "SELECT u.* FROM tokens_reinitialisation t 
                 JOIN " . $this->table . " u ON t.utilisateur_id = u.id 
                 WHERE t.token = ? AND t.expiration > NOW() 
                 LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $token);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Supprime un token de réinitialisation
     * 
     * @param string $token Token à supprimer
     * @return bool
     */
    public function supprimerTokenReinitialisation($token) {
        $query = "DELETE FROM tokens_reinitialisation WHERE token = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $token);
        
        return $stmt->execute();
    }
    
    /**
     * Compte le nombre total d'utilisateurs
     * 
     * @return int
     */
    public function compter() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['total'];
    }
}
