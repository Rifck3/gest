<?php
/**
 * Modèle pour la gestion des produits
 */
class Produit {
    // Propriétés de la base de données
    private $conn;
    private $table = "produits";
    
    // Propriétés de l'objet
    public $id;
    public $nom;
    public $description;
    public $categorie_id;
    public $fournisseur_id;
    public $prix_unitaire;
    public $quantite;
    public $quantite_min;
    public $date_creation;
    
    /**
     * Constructeur avec connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère tous les produits
     * 
     * @return PDOStatement
     */
    public function lireTous() {
        $query = "SELECT p.*, c.nom as nom_categorie, f.nom as nom_fournisseur 
                 FROM " . $this->table . " p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 ORDER BY p.nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Récupère un seul produit
     * 
     * @param int $id ID du produit
     * @return array
     */
    public function lire($id) {
        $query = "SELECT p.*, c.nom as nom_categorie, f.nom as nom_fournisseur 
                 FROM " . $this->table . " p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 WHERE p.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un seul produit (alias de lire)
     * 
     * @param int $id ID du produit
     * @return array
     */
    public function lireUn($id) {
        return $this->lire($id);
    }
    
    /**
     * Vérifie si un produit existe déjà
     * 
     * @param string $nom Nom du produit
     * @param int $fournisseur_id ID du fournisseur
     * @return bool
     */
    public function existe($nom, $fournisseur_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE LOWER(nom) = LOWER(?) AND fournisseur_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $nom);
        $stmt->bindParam(2, $fournisseur_id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }
    
    /**
     * Crée un nouveau produit
     * 
     * @return bool
     * @throws Exception Si le produit existe déjà
     */
    public function creer() {
        // Vérifier si le produit existe déjà
        if ($this->existe($this->nom, $this->fournisseur_id)) {
            throw new Exception("Un produit avec ce nom existe déjà pour ce fournisseur. Veuillez utiliser un nom différent ou sélectionner un autre fournisseur.");
        }

        $query = "INSERT INTO " . $this->table . " 
                 (nom, description, categorie_id, fournisseur_id, prix_unitaire, quantite, quantite_min, date_creation) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        $this->fournisseur_id = htmlspecialchars(strip_tags($this->fournisseur_id));
        $this->prix_unitaire = htmlspecialchars(strip_tags($this->prix_unitaire));
        $this->quantite = htmlspecialchars(strip_tags($this->quantite));
        $this->quantite_min = htmlspecialchars(strip_tags($this->quantite_min));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->description);
        $stmt->bindParam(3, $this->categorie_id);
        $stmt->bindParam(4, $this->fournisseur_id);
        $stmt->bindParam(5, $this->prix_unitaire);
        $stmt->bindParam(6, $this->quantite);
        $stmt->bindParam(7, $this->quantite_min);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Met à jour un produit
     * 
     * @return bool
     */
    public function mettreAJour() {
        $query = "UPDATE " . $this->table . " 
                 SET nom = ?, description = ?, categorie_id = ?, fournisseur_id = ?, 
                     prix_unitaire = ?, quantite = ?, quantite_min = ? 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->categorie_id = htmlspecialchars(strip_tags($this->categorie_id));
        $this->fournisseur_id = htmlspecialchars(strip_tags($this->fournisseur_id));
        $this->prix_unitaire = htmlspecialchars(strip_tags($this->prix_unitaire));
        $this->quantite = htmlspecialchars(strip_tags($this->quantite));
        $this->quantite_min = htmlspecialchars(strip_tags($this->quantite_min));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->description);
        $stmt->bindParam(3, $this->categorie_id);
        $stmt->bindParam(4, $this->fournisseur_id);
        $stmt->bindParam(5, $this->prix_unitaire);
        $stmt->bindParam(6, $this->quantite);
        $stmt->bindParam(7, $this->quantite_min);
        $stmt->bindParam(8, $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Compte le nombre total de produits
     * 
     * @return int
     */
    public function compter() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }
    
    /**
     * Calcule la valeur totale du stock
     * 
     * @return float
     */
    public function calculerValeurStock() {
        $query = "SELECT SUM(quantite * prix_unitaire) as valeur_totale 
                 FROM " . $this->table;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['valeur_totale'] ?? 0;
    }

    /**
     * Récupère les produits en stock faible (mais pas en rupture)
     * 
     * @return array
     */
    public function obtenirStockFaible() {
        $query = "SELECT p.*, c.nom as nom_categorie, f.nom as nom_fournisseur 
                 FROM " . $this->table . " p 
                 LEFT JOIN categories c ON p.categorie_id = c.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 WHERE p.quantite <= p.quantite_min AND p.quantite > 0
                 ORDER BY p.quantite ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Supprime tous les mouvements de stock d'un produit
     * 
     * @param int $id ID du produit
     * @return bool
     */
    private function supprimerMouvementsStock($id) {
        $query = "DELETE FROM mouvements_stock WHERE produit_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    /**
     * Supprime tous les détails de commande d'un produit
     * 
     * @param int $id ID du produit
     * @return bool
     */
    private function supprimerDetailsCommande($id) {
        $query = "DELETE FROM commande_details WHERE produit_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    /**
     * Supprime un produit
     * 
     * @param int $id ID du produit à supprimer
     * @return bool
     * @throws Exception Si la suppression échoue
     */
    public function supprimer($id) {
        try {
            // Démarrer une transaction
            $this->conn->beginTransaction();
            
            // Supprimer d'abord les détails de commande
            if (!$this->supprimerDetailsCommande($id)) {
                throw new Exception("Erreur lors de la suppression des détails de commande.");
            }
            
            // Supprimer ensuite les mouvements de stock
            if (!$this->supprimerMouvementsStock($id)) {
                throw new Exception("Erreur lors de la suppression des mouvements de stock.");
            }
            
            // Supprimer le produit
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la suppression du produit.");
            }
            
            // Valider la transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function mettreAJourQuantite($produit_id, $nouvelle_quantite) {
        $query = "UPDATE " . $this->table . " SET quantite = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $nouvelle_quantite);
        $stmt->bindParam(2, $produit_id);
        return $stmt->execute();
    }
}
