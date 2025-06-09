<?php
/**
 * Modèle pour la gestion des évaluations des fournisseurs
 */
class EvaluationFournisseur {
    private $conn;
    private $table = "evaluations_fournisseurs";
    
    public $id;
    public $fournisseur_id;
    public $note;
    public $commentaire;
    public $date_evaluation;
    public $utilisateur_id;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Crée une nouvelle évaluation
     */
    public function creer() {
        $query = "INSERT INTO " . $this->table . " 
                 (fournisseur_id, note, commentaire, date_evaluation, utilisateur_id) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->fournisseur_id = htmlspecialchars(strip_tags($this->fournisseur_id));
        $this->note = htmlspecialchars(strip_tags($this->note));
        $this->commentaire = htmlspecialchars(strip_tags($this->commentaire));
        $this->date_evaluation = htmlspecialchars(strip_tags($this->date_evaluation));
        $this->utilisateur_id = htmlspecialchars(strip_tags($this->utilisateur_id));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->fournisseur_id);
        $stmt->bindParam(2, $this->note);
        $stmt->bindParam(3, $this->commentaire);
        $stmt->bindParam(4, $this->date_evaluation);
        $stmt->bindParam(5, $this->utilisateur_id);
        
        if($stmt->execute()) {
            // Mettre à jour la note moyenne du fournisseur
            $this->mettreAJourNoteMoyenne($this->fournisseur_id);
            return true;
        }
        return false;
    }
    
    /**
     * Récupère toutes les évaluations d'un fournisseur
     */
    public function lireParFournisseur($fournisseur_id) {
        $query = "SELECT e.*, u.nom_complet as evaluateur 
                 FROM " . $this->table . " e
                 LEFT JOIN utilisateurs u ON e.utilisateur_id = u.id
                 WHERE e.fournisseur_id = ?
                 ORDER BY e.date_evaluation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Met à jour la note moyenne du fournisseur
     */
    private function mettreAJourNoteMoyenne($fournisseur_id) {
        $query = "UPDATE fournisseurs 
                 SET note = (
                     SELECT AVG(note) 
                     FROM " . $this->table . " 
                     WHERE fournisseur_id = ?
                 ),
                 date_derniere_evaluation = NOW()
                 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->bindParam(2, $fournisseur_id);
        $stmt->execute();
    }
}
?> 