<?php
/**
 * Modèle pour la gestion des commandes
 */
class Commande {
    private $db;
    private $table = 'commandes';
    private $table_details = 'commande_details';
    public $id;
    public $reference;
    public $fournisseur_id;
    public $utilisateur_id;
    public $statut;
    public $date_creation;
    public $date_validation;
    public $valide_par;
    public $commentaire;
    
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
     * Crée une nouvelle commande
     */
    public function creer() {
        // Validation des données
        if (empty($this->fournisseur_id)) {
            throw new Exception('Le fournisseur est requis.');
        }
        if (empty($this->utilisateur_id)) {
            throw new Exception('L\'utilisateur est requis.');
        }

        $sql = "INSERT INTO {$this->table} (
            reference,
            fournisseur_id,
            utilisateur_id,
            statut,
            commentaire,
            date_creation
        ) VALUES (
            :reference,
            :fournisseur_id,
            :utilisateur_id,
            'en_attente',
            :commentaire,
            NOW()
        )";

        // Debug temporaire : afficher la requête SQL
        echo '<pre>'.$sql.'</pre>';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'reference' => $this->genererReference(),
            'fournisseur_id' => $this->fournisseur_id,
            'utilisateur_id' => $this->utilisateur_id,
            'commentaire' => $this->commentaire
        ]);

        $this->id = $this->db->lastInsertId();
        return $this->id;
    }
    
    /**
     * Ajoute un produit à une commande
     */
    public function ajouterDetail($commande_id, $produit_id, $quantite, $prix_unitaire) {
        // Validation des données
        if (empty($commande_id)) {
            throw new Exception('L\'ID de la commande est requis.');
        }
        if (empty($produit_id)) {
            throw new Exception('L\'ID du produit est requis.');
        }
        if ($quantite <= 0) {
            throw new Exception('La quantité doit être supérieure à 0.');
        }
        if ($prix_unitaire <= 0) {
            throw new Exception('Le prix unitaire doit être supérieur à 0.');
        }

        // Vérifier que la commande existe et est en attente
        $commande = $this->lireUn($commande_id);
        if (!$commande) {
            throw new Exception('La commande n\'existe pas.');
        }
        if ($commande['statut'] !== 'en_attente') {
            throw new Exception('Impossible d\'ajouter des produits à une commande non en attente.');
        }

        $sql = "INSERT INTO {$this->table_details} (
            commande_id,
            produit_id,
            quantite,
            prix_unitaire
        ) VALUES (
            :commande_id,
            :produit_id,
            :quantite,
            :prix_unitaire
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'commande_id' => $commande_id,
            'produit_id' => $produit_id,
            'quantite' => $quantite,
            'prix_unitaire' => $prix_unitaire
        ]);

        // Mettre à jour le montant total
        $this->calculerMontantTotal($commande_id);

        return $this->db->lastInsertId();
    }
    
    /**
     * Lit une commande par son ID
     */
    public function lireUn($id) {
        $sql = "SELECT c.*, 
                f.nom as fournisseur_nom,
                u.nom_complet as utilisateur_nom
                FROM {$this->table} c
                LEFT JOIN fournisseurs f ON c.fournisseur_id = f.id
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
                WHERE c.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lit toutes les commandes avec filtres
     */
    public function lireToutes($filtres = []) {
        $sql = "SELECT c.*, 
                f.nom as fournisseur_nom,
                u.nom_complet as utilisateur_nom
                FROM {$this->table} c
                LEFT JOIN fournisseurs f ON c.fournisseur_id = f.id
                LEFT JOIN utilisateurs u ON c.utilisateur_id = u.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtres['statut'])) {
            $sql .= " AND c.statut = :statut";
            $params['statut'] = $filtres['statut'];
        }

        if (!empty($filtres['fournisseur_id'])) {
            $sql .= " AND c.fournisseur_id = :fournisseur_id";
            $params['fournisseur_id'] = $filtres['fournisseur_id'];
        }

        if (!empty($filtres['date_debut'])) {
            $sql .= " AND c.date_creation >= :date_debut";
            $params['date_debut'] = $filtres['date_debut'] . ' 00:00:00';
        }

        if (!empty($filtres['date_fin'])) {
            $sql .= " AND c.date_creation <= :date_fin";
            $params['date_fin'] = $filtres['date_fin'] . ' 23:59:59';
        }

        $sql .= " ORDER BY c.date_creation DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Lit les détails d'une commande
     */
    public function lireDetails($commande_id) {
        $sql = "SELECT cd.*, p.nom as produit_nom
                FROM {$this->table_details} cd
                LEFT JOIN produits p ON cd.produit_id = p.id
                WHERE cd.commande_id = :commande_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['commande_id' => $commande_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Valide une commande
     */
    public function valider($id) {
        // Vérifier que la commande existe et est en attente
        $commande = $this->lireUn($id);
        if (!$commande || $commande['statut'] !== 'en_attente') {
            throw new Exception('La commande ne peut pas être validée.');
        }

        $sql = "UPDATE {$this->table} 
                SET statut = 'validee',
                    date_validation = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(['id' => $id]);
        return $result;
    }
    
    /**
     * Annule une commande
     */
    public function annuler($id) {
        // Vérifier que la commande existe et est en attente
        $commande = $this->lireUn($id);
        if (!$commande || $commande['statut'] !== 'en_attente') {
            throw new Exception('La commande ne peut pas être annulée.');
        }

        // Démarrer une transaction
        $this->beginTransaction();
        
        try {
            // Supprimer les détails de la commande
            $sql = "DELETE FROM {$this->table_details} WHERE commande_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            // Supprimer la commande
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            // Valider la transaction
            $this->commit();
            
            return true;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Met à jour le montant total d'une commande
     */
    public function mettreAJourMontantTotal($id, $montant) {
        $sql = "UPDATE {$this->table} 
                SET montant_total = :montant
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'montant' => $montant
        ]);
    }
    
    /**
     * Génère une référence unique pour une commande
     */
    private function genererReference() {
        $prefixe = 'CMD';
        $date = date('Ymd');
        
        // Trouver la dernière référence pour cette date
        $query = "SELECT reference FROM commandes WHERE reference LIKE ? ORDER BY id DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$prefixe . $date . '%']);
        $derniereRef = $stmt->fetchColumn();
        
        if ($derniereRef) {
            // Extraire le numéro séquentiel et l'incrémenter
            $numero = intval(substr($derniereRef, -4)) + 1;
        } else {
            // Première commande de la journée
            $numero = 1;
        }
        
        // Formater la référence
        return $prefixe . $date . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Démarre une transaction
     */
    public function beginTransaction() {
        if (!$this->db->inTransaction()) {
            $this->db->beginTransaction();
        }
    }
    
    /**
     * Valide une transaction
     */
    public function commit() {
        if ($this->db->inTransaction()) {
            $this->db->commit();
        }
    }
    
    /**
     * Annule une transaction
     */
    public function rollback() {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }
    
    /**
     * Supprime une commande et ses détails
     * @param int $id ID de la commande à supprimer
     * @return bool True si la suppression est réussie, False sinon
     */
    public function supprimer($id) {
        try {
            // Vérifier que la commande existe
            $commande = $this->lireUn($id);
            if (!$commande) {
                throw new Exception('Cette commande ne peut pas être supprimée');
            }

            // Démarrer la transaction
            $this->beginTransaction();
            
            // Supprimer d'abord les détails de la commande
            $sql = "DELETE FROM {$this->table_details} WHERE commande_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            // Supprimer la commande
            $sql = "DELETE FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            
            // Valider la transaction
            $this->commit();
            return true;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Calcule et met à jour le montant total d'une commande
     */
    private function calculerMontantTotal($commande_id) {
        $sql = "SELECT SUM(quantite * prix_unitaire) as total 
                FROM {$this->table_details} 
                WHERE commande_id = :commande_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['commande_id' => $commande_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->mettreAJourMontantTotal($commande_id, $result['total'] ?? 0);
    }

    /**
     * Supprime les commandes de plus de 6 mois
     * @return int Nombre de commandes supprimées
     */
    public function supprimerAnciennesCommandes() {
        try {
            // Démarrer la transaction
            $this->beginTransaction();
            
            // Récupérer les commandes de plus de 6 mois
            $sql = "SELECT id FROM {$this->table} WHERE date_creation < DATE_SUB(NOW(), INTERVAL 6 MONTH)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $commandes = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $nombreSupprimees = 0;
            
            // Supprimer chaque commande et ses détails
            foreach ($commandes as $id) {
                if ($this->supprimer($id)) {
                    $nombreSupprimees++;
                }
            }
            
            // Valider la transaction
            $this->commit();
            return $nombreSupprimees;
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->rollback();
            return 0;
        }
    }
} 