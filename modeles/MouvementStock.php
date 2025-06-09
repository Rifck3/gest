<?php
/**
 * Modèle pour la gestion des mouvements de stock
 */
class MouvementStock {
    // Propriétés de la base de données
    private $conn;
    private $table = "mouvements_stock";
    
    // Propriétés de l'objet
    public $id;
    public $produit_id;
    public $type_mouvement; // 'entree' ou 'sortie'
    public $quantite;
    public $reference;
    public $raison;
    public $notes;
    public $utilisateur_id;
    public $date_mouvement;
    
    /**
     * Constructeur avec connexion à la base de données
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Récupère tous les mouvements de stock avec filtres
     * 
     * @param string $type Type de mouvement (entree/sortie)
     * @param int $produit_id ID du produit
     * @param string $date_debut Date de début
     * @param string $date_fin Date de fin
     * @return array
     */
    public function lireTous($type = null, $produit_id = null, $date_debut = null, $date_fin = null) {
        $sql = "SELECT DISTINCT ms.id, ms.produit_id, ms.type_mouvement, ms.quantite, ms.reference, 
                       ms.raison, ms.notes, ms.date_mouvement, ms.utilisateur_id,
                       p.nom as nom_produit, u.nom_complet as utilisateur
                FROM " . $this->table . " ms
                LEFT JOIN produits p ON ms.produit_id = p.id
                LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if($type) {
            $sql .= " AND ms.type_mouvement = ?";
            $params[] = $type;
        }
        
        if($produit_id) {
            $sql .= " AND ms.produit_id = ?";
            $params[] = $produit_id;
        }
        
        if($date_debut) {
            $sql .= " AND DATE(ms.date_mouvement) >= ?";
            $params[] = $date_debut;
        }
        
        if($date_fin) {
            $sql .= " AND DATE(ms.date_mouvement) <= ?";
            $params[] = $date_fin;
        }
        
        $sql .= " ORDER BY ms.date_mouvement DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Nombre de mouvements trouvés : " . count($resultats));
        return $resultats;
    }
    
    /**
     * Récupère un seul mouvement de stock
     * 
     * @param int $id ID du mouvement
     * @return array
     */
    public function lireUn($id) {
        $query = "SELECT ms.*, p.nom as nom_produit, f.nom as nom_fournisseur, u.nom_complet as utilisateur 
                 FROM " . $this->table . " ms 
                 LEFT JOIN produits p ON ms.produit_id = p.id 
                 LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id 
                 LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id 
                 WHERE ms.id = ? 
                 LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouveau mouvement de stock
     * 
     * @return bool
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                 (produit_id, type_mouvement, quantite, reference, raison, notes, utilisateur_id, date_mouvement) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->produit_id = htmlspecialchars(strip_tags($this->produit_id));
        $this->type_mouvement = htmlspecialchars(strip_tags($this->type_mouvement));
        $this->quantite = htmlspecialchars(strip_tags($this->quantite));
        $this->reference = $this->reference ? htmlspecialchars(strip_tags($this->reference)) : null;
        $this->raison = $this->raison ? htmlspecialchars(strip_tags($this->raison)) : null;
        $this->notes = $this->notes ? htmlspecialchars(strip_tags($this->notes)) : null;
        $this->utilisateur_id = htmlspecialchars(strip_tags($this->utilisateur_id));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->produit_id);
        $stmt->bindParam(2, $this->type_mouvement);
        $stmt->bindParam(3, $this->quantite);
        $stmt->bindParam(4, $this->reference);
        $stmt->bindParam(5, $this->raison);
        $stmt->bindParam(6, $this->notes);
        $stmt->bindParam(7, $this->utilisateur_id);
        
        // Exécuter la requête
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        return false;
    }
    
    /**
     * Récupère les mouvements de stock d'un produit
     * 
     * @param int $produit_id ID du produit
     * @param int $limite Nombre de résultats à retourner (0 = tous)
     * @return array
     */
    public function lireParProduit($produit_id, $limite = 0) {
        $query = "SELECT ms.*, f.nom as nom_fournisseur, u.nom_complet as utilisateur 
                 FROM " . $this->table . " ms 
                 LEFT JOIN fournisseurs f ON ms.fournisseur_id = f.id 
                 LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id 
                 WHERE ms.produit_id = ? 
                 ORDER BY ms.date_mouvement DESC";
        
        if($limite > 0) {
            $query .= " LIMIT 0, " . $limite;
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $produit_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les mouvements de stock d'un fournisseur
     * 
     * @param int $fournisseur_id ID du fournisseur
     * @return array
     */
    public function lireParFournisseur($fournisseur_id) {
        $query = "SELECT ms.*, p.nom as nom_produit, u.nom_complet as utilisateur 
                 FROM " . $this->table . " ms 
                 LEFT JOIN produits p ON ms.produit_id = p.id 
                 LEFT JOIN utilisateurs u ON ms.utilisateur_id = u.id 
                 WHERE p.fournisseur_id = ? 
                 ORDER BY ms.date_mouvement DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les statistiques de mouvements de stock par jour
     * 
     * @param string $date_debut Date de début
     * @param string $date_fin Date de fin
     * @return array
     */
    public function obtenirStatistiquesParJour($date_debut, $date_fin) {
        $query = "SELECT 
                 DATE_FORMAT(date_mouvement, '%Y-%m') as mois,
                 SUM(CASE WHEN type_mouvement = 'entree' THEN quantite ELSE 0 END) as entrees,
                 SUM(CASE WHEN type_mouvement = 'sortie' THEN quantite ELSE 0 END) as sorties
                 FROM " . $this->table . " 
                 WHERE DATE(date_mouvement) BETWEEN ? AND ?
                 GROUP BY DATE_FORMAT(date_mouvement, '%Y-%m')
                 ORDER BY mois";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$date_debut, $date_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les statistiques de mouvements de stock par produit
     * 
     * @param string $date_debut Date de début
     * @param string $date_fin Date de fin
     * @param int $limite Nombre de résultats à retourner
     * @return array
     */
    public function obtenirStatistiquesParProduit($date_debut, $date_fin, $limite = 10) {
        $query = "SELECT 
                 ms.produit_id,
                 p.nom as nom_produit,
                 SUM(CASE WHEN ms.type_mouvement = 'entree' THEN ms.quantite ELSE 0 END) as total_entrees,
                 SUM(CASE WHEN ms.type_mouvement = 'sortie' THEN ms.quantite ELSE 0 END) as total_sorties,
                 COUNT(ms.id) as total_mouvements
                 FROM " . $this->table . " ms
                 JOIN produits p ON ms.produit_id = p.id
                 WHERE DATE(ms.date_mouvement) BETWEEN ? AND ?
                 GROUP BY ms.produit_id, p.nom
                 ORDER BY total_mouvements DESC
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $date_debut);
        $stmt->bindParam(2, $date_fin);
        $stmt->bindParam(3, $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Compte le nombre total de mouvements
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
    
    /**
     * Compte le nombre de mouvements par type
     * 
     * @return array
     */
    public function compterParType() {
        $query = "SELECT 
                 type_mouvement,
                 COUNT(*) as total,
                 SUM(quantite) as quantite_totale
                 FROM " . $this->table . "
                 GROUP BY type_mouvement";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Met à jour un mouvement de stock
     * 
     * @return bool
     */
    public function mettreAJour() {
        $query = "UPDATE " . $this->table . " 
                 SET produit_id = ?, 
                     type_mouvement = ?, 
                     quantite = ?, 
                     reference = ?, 
                     raison = ?, 
                     notes = ? 
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->produit_id = htmlspecialchars(strip_tags($this->produit_id));
        $this->type_mouvement = htmlspecialchars(strip_tags($this->type_mouvement));
        $this->quantite = htmlspecialchars(strip_tags($this->quantite));
        $this->reference = $this->reference ? htmlspecialchars(strip_tags($this->reference)) : null;
        $this->raison = $this->raison ? htmlspecialchars(strip_tags($this->raison)) : null;
        $this->notes = $this->notes ? htmlspecialchars(strip_tags($this->notes)) : null;
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->produit_id);
        $stmt->bindParam(2, $this->type_mouvement);
        $stmt->bindParam(3, $this->quantite);
        $stmt->bindParam(4, $this->reference);
        $stmt->bindParam(5, $this->raison);
        $stmt->bindParam(6, $this->notes);
        $stmt->bindParam(7, $this->id);
        
        // Exécuter la requête
        return $stmt->execute();
    }
    
    /**
     * Supprime un mouvement de stock
     * 
     * @return bool
     */
    public function supprimer() {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
