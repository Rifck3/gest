<?php
/**
 * Modèle pour la gestion des documents des fournisseurs
 */
class DocumentFournisseur {
    private $conn;
    private $table = "documents_fournisseurs";
    
    public $id;
    public $fournisseur_id;
    public $type_document;
    public $nom_fichier;
    public $chemin_fichier;
    public $date_ajout;
    public $utilisateur_id;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Ajoute un nouveau document
     */
    public function ajouter() {
        $query = "INSERT INTO " . $this->table . " 
                 (fournisseur_id, type_document, nom_fichier, chemin_fichier, utilisateur_id) 
                 VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Nettoyer les données
        $this->fournisseur_id = htmlspecialchars(strip_tags($this->fournisseur_id));
        $this->type_document = htmlspecialchars(strip_tags($this->type_document));
        $this->nom_fichier = htmlspecialchars(strip_tags($this->nom_fichier));
        $this->chemin_fichier = htmlspecialchars(strip_tags($this->chemin_fichier));
        $this->utilisateur_id = htmlspecialchars(strip_tags($this->utilisateur_id));
        
        // Lier les valeurs
        $stmt->bindParam(1, $this->fournisseur_id);
        $stmt->bindParam(2, $this->type_document);
        $stmt->bindParam(3, $this->nom_fichier);
        $stmt->bindParam(4, $this->chemin_fichier);
        $stmt->bindParam(5, $this->utilisateur_id);
        
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
    
    /**
     * Récupère tous les documents d'un fournisseur
     */
    public function lireParFournisseur($fournisseur_id) {
        $query = "SELECT d.*, u.nom_complet as ajoute_par 
                 FROM " . $this->table . " d
                 LEFT JOIN utilisateurs u ON d.utilisateur_id = u.id
                 WHERE d.fournisseur_id = ?
                 ORDER BY d.date_ajout DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fournisseur_id);
        $stmt->execute();
        
        return $stmt;
    }
    
    /**
     * Supprime un document
     */
    public function supprimer($id) {
        // Récupérer le chemin du fichier avant la suppression
        $query = "SELECT chemin_fichier FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($document) {
            // Supprimer le fichier physique
            if(file_exists($document['chemin_fichier'])) {
                unlink($document['chemin_fichier']);
            }
            
            // Supprimer l'enregistrement de la base de données
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            
            return $stmt->execute();
        }
        return false;
    }
}
?> 