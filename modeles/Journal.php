<?php
/**
 * Modèle pour la gestion du journal des activités
 */
class Journal {
    private $db;
    private $table = 'journal_activites';
    
    /**
     * Constructeur
     */
    public function __construct($db = null) {
        if ($db === null) {
            $db = Database::getInstance();
        }
        $this->db = $db;
    }
    
    /**
     * Enregistre une activité dans le journal
     */
    public function enregistrer($utilisateur_id, $type_activite, $description, $ip_adresse = '', $navigateur = '') {
        $sql = "INSERT INTO {$this->table} (
            utilisateur_id,
            type_activite,
            description,
            ip_adresse,
            navigateur,
            date_activite
        ) VALUES (
            :utilisateur_id,
            :type_activite,
            :description,
            :ip_adresse,
            :navigateur,
            NOW()
        )";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'utilisateur_id' => $utilisateur_id,
            'type_activite' => $type_activite,
            'description' => $description,
            'ip_adresse' => $ip_adresse,
            'navigateur' => $navigateur
        ]);
    }
    
    /**
     * Lit les activités d'un utilisateur
     */
    public function lireActivitesUtilisateur($utilisateur_id, $limit = 50) {
        $sql = "SELECT ja.*, u.nom_complet as utilisateur_nom
                FROM {$this->table} ja
                LEFT JOIN utilisateurs u ON ja.utilisateur_id = u.id
                WHERE ja.utilisateur_id = :utilisateur_id
                ORDER BY ja.date_activite DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lit les dernières activités du système
     */
    public function lireDernieresActivites($limit = 100) {
        $sql = "SELECT ja.*, u.nom_complet as utilisateur_nom
                FROM {$this->table} ja
                LEFT JOIN utilisateurs u ON ja.utilisateur_id = u.id
                ORDER BY ja.date_activite DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lit les activités par type
     */
    public function lireActivitesParType($type_activite, $limit = 50) {
        $sql = "SELECT ja.*, u.nom_complet as utilisateur_nom
                FROM {$this->table} ja
                LEFT JOIN utilisateurs u ON ja.utilisateur_id = u.id
                WHERE ja.type_activite = :type_activite
                ORDER BY ja.date_activite DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':type_activite', $type_activite, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Supprime les anciennes activités
     */
    public function nettoyerAnciennesActivites($jours = 30) {
        $sql = "DELETE FROM {$this->table} 
                WHERE date_activite < DATE_SUB(NOW(), INTERVAL :jours DAY)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':jours', $jours, PDO::PARAM_INT);
        return $stmt->execute();
    }
} 