<?php
/**
 * Modèle pour la gestion du suivi des livraisons
 */
class SuiviLivraison {
    private $conn;
    private $table = "suivi_livraisons";
    
    public $id;
    public $commande_id;
    public $fournisseur_id;
    public $date_commande;
    public $date_livraison_prevue;
    public $date_livraison_reelle;
    public $statut;
    public $commentaire;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crée un nouveau suivi de livraison
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                 (commande_id, fournisseur_id, date_commande, date_livraison_prevue, statut, commentaire) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->commande_id = htmlspecialchars(strip_tags($this->commande_id));
        $this->fournisseur_id = htmlspecialchars(strip_tags($this->fournisseur_id));
        $this->date_commande = htmlspecialchars(strip_tags($this->date_commande));
        $this->date_livraison_prevue = htmlspecialchars(strip_tags($this->date_livraison_prevue));
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->commande_id);
        $stmt->bindParam(2, $this->fournisseur_id);
        $stmt->bindParam(3, $this->date_commande);
        $stmt->bindParam(4, $this->date_livraison_prevue);
        $stmt->bindParam(5, $this->statut);
        $stmt->bindParam(6, $this->commentaire);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Met à jour le statut d'une livraison
     */
    public function mettreAJourStatut() {
        $query = "UPDATE " . $this->table . " 
                 SET statut = ?, 
                     date_livraison_reelle = CASE 
                         WHEN ? = 'livree' THEN NOW() 
                         ELSE date_livraison_reelle 
                     END,
                     commentaire = ?
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->statut);
        $stmt->bindParam(2, $this->statut);
        $stmt->bindParam(3, $this->commentaire);
        $stmt->bindParam(4, $this->id);
        
        if($stmt->execute()) {
            // Mettre à jour le délai moyen de livraison du fournisseur
            $this->mettreAJourDelaiMoyen($this->fournisseur_id);
            return true;
        }
        return false;
    }
    
    /**
     * Récupère toutes les livraisons d'un fournisseur
     */
    public function lireParFournisseur($fournisseur_id) {
        $query = "SELECT s.*, c.numero as numero_commande 
                 FROM " . $this->table . " s
                 LEFT JOIN commandes c ON s.commande_id = c.id
                 WHERE s.fournisseur_id = ?
                 ORDER BY s.date_commande DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Met à jour le délai moyen de livraison du fournisseur
     */
    private function mettreAJourDelaiMoyen($fournisseur_id) {
        $query = "UPDATE fournisseurs 
                 SET delai_livraison_moyen = (
                     SELECT AVG(DATEDIFF(date_livraison_reelle, date_commande))
                     FROM " . $this->table . "
                     WHERE fournisseur_id = ? 
                     AND date_livraison_reelle IS NOT NULL
                 )
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->bindParam(2, $fournisseur_id);
        $stmt->execute();
    }
}
?> 