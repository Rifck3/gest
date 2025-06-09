<?php
/**
 * Modèle pour la gestion des fournisseurs
 */
class Fournisseur {
    // Propriétés de la base de données
    private $conn;
    private $table = "fournisseurs";
    
    // Propriétés de l'objet
    public $id;
    public $nom;
    public $adresse;
    public $telephone;
    public $email;
    public $contact;
    public $notes;
    public $date_creation;
    public $date_modification;
    
    /**
     * Constructeur avec connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère tous les fournisseurs
     * 
     * @return PDOStatement
     */
    public function lireTous() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Récupère un seul fournisseur
     * 
     * @param int $id ID du fournisseur
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
     * Crée un nouveau fournisseur
     * 
     * @return bool
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                 (nom, adresse, telephone, email, contact, notes, date_creation, date_modification) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contact = htmlspecialchars(strip_tags($this->contact));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->adresse);
        $stmt->bindParam(3, $this->telephone);
        $stmt->bindParam(4, $this->email);
        $stmt->bindParam(5, $this->contact);
        $stmt->bindParam(6, $this->notes);
        
        // Exécuter la requête
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Met à jour un fournisseur
     * 
     * @return bool
     */
    public function mettreAJour() {
        $query = "UPDATE " . $this->table . " 
                 SET nom = ?, adresse = ?, telephone = ?, email = ?, personne_contact = ?, notes = ?, date_modification = NOW() 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->adresse = htmlspecialchars(strip_tags($this->adresse));
        $this->telephone = htmlspecialchars(strip_tags($this->telephone));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->contact = htmlspecialchars(strip_tags($this->contact));
        $this->notes = htmlspecialchars(strip_tags($this->notes));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->adresse);
        $stmt->bindParam(3, $this->telephone);
        $stmt->bindParam(4, $this->email);
        $stmt->bindParam(5, $this->contact);
        $stmt->bindParam(6, $this->notes);
        $stmt->bindParam(7, $this->id);
        
        // Exécuter la requête
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Supprime un fournisseur
     * 
     * @param int $id ID du fournisseur
     * @return bool
     */
    public function supprimer($id) {
        // Vérifier s'il y a des produits associés
        $query = "SELECT COUNT(*) as count FROM produits WHERE fournisseur_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si des produits existent, ne pas supprimer
        if($row['count'] > 0) {
            return false;
        }
        
        // Vérifier s'il y a des produits associés au fournisseur
        $query = "SELECT COUNT(*) as count FROM produits WHERE id_fournisseur = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si des produits existent, ne pas supprimer
        if($row['count'] > 0) {
            return false;
        }
        
        // Supprimer le fournisseur
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Recherche des fournisseurs
     * 
     * @param string $terme Terme de recherche
     * @return PDOStatement
     */
    public function rechercher($terme) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE nom LIKE ? OR adresse LIKE ? OR email LIKE ? OR contact LIKE ? 
                 ORDER BY nom";
        
        $terme = "%{$terme}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $terme);
        $stmt->bindParam(2, $terme);
        $stmt->bindParam(3, $terme);
        $stmt->bindParam(4, $terme);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Récupère les produits d'un fournisseur
     * 
     * @param int $fournisseur_id ID du fournisseur
     * @return array
     */
    public function obtenirProduits($fournisseur_id) {
        $query = "SELECT p.*, c.nom as nom_categorie 
                 FROM produits p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 WHERE p.fournisseur_id = ? 
                 ORDER BY p.nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les livraisons d'un fournisseur
     * 
     * @param int $fournisseur_id ID du fournisseur
     * @return array
     */
    public function obtenirLivraisons($fournisseur_id) {
        $query = "SELECT sm.*, p.nom as nom_produit, u.nom_complet as utilisateur 
                 FROM mouvements_stock sm 
                 LEFT JOIN produits p ON sm.produit_id = p.id 
                 LEFT JOIN utilisateurs u ON sm.utilisateur_id = u.id 
                 WHERE sm.fournisseur_id = ? AND sm.type_mouvement = 'entree' 
                 ORDER BY sm.date_mouvement DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Compte le nombre total de fournisseurs
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
