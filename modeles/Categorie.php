<?php
/**
 * Modèle pour la gestion des catégories
 */
class Categorie {
    // Propriétés de la base de données
    private $conn;
    private $table = "categories";
    
    // Propriétés de l'objet
    public $id;
    public $nom;
    public $description;
    
    /**
     * Constructeur avec connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère toutes les catégories
     * 
     * @return PDOStatement
     */
    public function lireTous() {
        $query = "SELECT DISTINCT * FROM " . $this->table . " ORDER BY nom";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Récupère une seule catégorie
     * 
     * @param int $id ID de la catégorie
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
     * Crée une nouvelle catégorie
     * 
     * @return bool
     */
    public function creer() {
        // Vérifier si la catégorie existe déjà
        $query = "SELECT id FROM " . $this->table . " WHERE nom = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->nom);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return false; // La catégorie existe déjà
        }
        
        $query = "INSERT INTO " . $this->table . " 
                 (nom, description) 
                 VALUES (?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->description);
        
        // Exécuter la requête
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Met à jour une catégorie
     * 
     * @return bool
     */
    public function mettreAJour() {
        $query = "UPDATE " . $this->table . " 
                 SET nom = ?, description = ? 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->nom);
        $stmt->bindParam(2, $this->description);
        $stmt->bindParam(3, $this->id);
        
        // Exécuter la requête
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie les dépendances d'une catégorie
     * 
     * @param int $id ID de la catégorie
     * @return array Tableau contenant les informations sur les dépendances
     */
    private function verifierDependances($id) {
        $dependances = [];
        
        // Vérifier les produits
        $query = "SELECT COUNT(*) as count FROM produits WHERE categorie_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            $dependances['produits'] = $result['count'];
        }
        
        // Vérifier les commandes liées aux produits de cette catégorie
        $query = "SELECT COUNT(DISTINCT cd.commande_id) as count 
                  FROM commande_details cd 
                  JOIN produits p ON cd.produit_id = p.id 
                  WHERE p.categorie_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            $dependances['commandes'] = $result['count'];
        }
        
        // Vérifier les mouvements de stock liés aux produits de cette catégorie
        $query = "SELECT COUNT(*) as count 
                  FROM mouvements_stock ms 
                  JOIN produits p ON ms.produit_id = p.id 
                  WHERE p.categorie_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            $dependances['mouvements'] = $result['count'];
        }
        
        // Vérifier les étagères liées à cette catégorie
        $query = "SELECT COUNT(*) as count FROM etageres WHERE categorie_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            $dependances['etageres'] = $result['count'];
        }
        
        return $dependances;
    }
    
    /**
     * Nettoie les références orphelines dans les tables liées
     * 
     * @param int $id ID de la catégorie
     */
    private function nettoyerReferencesOrphelines($id) {
        try {
            // Supprimer les mouvements de stock orphelins
            $query = "DELETE ms FROM mouvements_stock ms 
                     LEFT JOIN produits p ON ms.produit_id = p.id 
                     WHERE p.id IS NULL OR p.categorie_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            // Supprimer les détails de commande orphelins
            $query = "DELETE cd FROM commande_details cd 
                     LEFT JOIN produits p ON cd.produit_id = p.id 
                     WHERE p.id IS NULL OR p.categorie_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            // Supprimer les étagères liées à cette catégorie
            $query = "DELETE FROM etageres WHERE categorie_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
        } catch (Exception $e) {
            throw new Exception("Erreur lors du nettoyage des références : " . $e->getMessage());
        }
    }
    
    /**
     * Supprime une catégorie
     * 
     * @param int $id ID de la catégorie
     * @return bool
     * @throws Exception Si la suppression échoue
     */
    public function supprimer($id) {
        try {
            // Démarrer une transaction
            $this->conn->beginTransaction();
            
            // Nettoyer les références orphelines
            $this->nettoyerReferencesOrphelines($id);
            
            // Vérifier s'il reste des dépendances
            $dependances = $this->verifierDependances($id);
            
            if (!empty($dependances)) {
                $messages = [];
                if (isset($dependances['produits'])) {
                    $messages[] = "{$dependances['produits']} produit(s)";
                }
                if (isset($dependances['commandes'])) {
                    $messages[] = "{$dependances['commandes']} commande(s)";
                }
                if (isset($dependances['mouvements'])) {
                    $messages[] = "{$dependances['mouvements']} mouvement(s) de stock";
                }
                if (isset($dependances['etageres'])) {
                    $messages[] = "{$dependances['etageres']} étagère(s)";
                }
                
                throw new Exception("Impossible de supprimer cette catégorie car elle est toujours liée à : " . implode(", ", $messages) . ". Veuillez d'abord gérer ces dépendances.");
        }
        
        // Supprimer la catégorie
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la suppression de la catégorie.");
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
    
    /**
     * Recherche des catégories
     * 
     * @param string $terme Terme de recherche
     * @return PDOStatement
     */
    public function rechercher($terme) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE nom LIKE ? OR description LIKE ? 
                 ORDER BY nom";
        
        $terme = "%{$terme}%";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $terme);
        $stmt->bindParam(2, $terme);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Compte le nombre total de catégories
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
