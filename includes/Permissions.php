<?php
class Permissions {
    // Liste des actions autorisées pour chaque rôle
    private static $permissions = [
        'admin' => [
            // Gestion des utilisateurs
            'gestion_utilisateurs' => true,
            'creer_utilisateur' => true,
            'modifier_utilisateur' => true,
            'supprimer_utilisateur' => true,
            'reinitialiser_mot_de_passe' => true,
            
            // Gestion des données
            'gestion_produits' => true,
            'gestion_fournisseurs' => true,
            'gestion_categories' => true,
            'gestion_mouvements' => true,
            'gestion_commandes' => true,
            
            // Permissions spécifiques aux commandes
            'voir_commandes' => true,
            'creer_commandes' => true,
            'valider_commandes' => true,
            'annuler_commandes' => true,
            'modifier_commandes' => true,
            'supprimer_commandes' => true,
            
            // Opérations critiques
            'supprimer_donnees' => true,
            'annuler_transactions' => true,
            'valider_ajustements' => true,
            
            // Rapports et statistiques
            'voir_rapports' => true,
            'voir_statistiques' => true,
            'voir_rapports_financiers' => true,
            'voir_journal_audit' => true,
            
            // Configuration système
            'gerer_parametres' => true,
            'configurer_systeme' => true,
            'gerer_sauvegardes' => true
        ],
        'gestionnaire' => [
            // Gestion des utilisateurs (restreinte)
            'gestion_utilisateurs' => false,
            'creer_utilisateur' => false,
            'modifier_utilisateur' => false,
            'supprimer_utilisateur' => false,
            'reinitialiser_mot_de_passe' => false,
            
            // Gestion des données (autorisée)
            'gestion_produits' => true,
            'gestion_fournisseurs' => true,
            'gestion_categories' => true,
            'gestion_mouvements' => true,
            'gestion_commandes' => true,
            
            // Permissions spécifiques aux commandes
            'voir_commandes' => true,
            'creer_commandes' => true,
            'valider_commandes' => false,
            'annuler_commandes' => false,
            'modifier_commandes' => false,
            'supprimer_commandes' => false,
            
            // Opérations critiques (restreintes)
            'supprimer_donnees' => false,
            'annuler_transactions' => false,
            'valider_ajustements' => false,
            
            // Rapports et statistiques (restreints)
            'voir_rapports' => true,
            'voir_statistiques' => true,
            'voir_rapports_financiers' => false,
            'voir_journal_audit' => false,
            
            // Configuration système (restreinte)
            'gerer_parametres' => false,
            'configurer_systeme' => false,
            'gerer_sauvegardes' => false
        ]
    ];

    /**
     * Vérifie si l'utilisateur a la permission d'effectuer une action
     */
    public static function aPermission($action) {
        if (!isset($_SESSION['role'])) {
            return false;
        }

        $role = $_SESSION['role'];
        
        if (!isset(self::$permissions[$role])) {
            return false;
        }

        return isset(self::$permissions[$role][$action]) && self::$permissions[$role][$action];
    }

    /**
     * Vérifie si l'utilisateur est administrateur
     */
    public static function estAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est gestionnaire
     */
    public static function estGestionnaire() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'gestionnaire';
    }

    /**
     * Redirige l'utilisateur si il n'a pas la permission
     */
    public static function verifierPermission($action) {
        if (!self::aPermission($action)) {
            $_SESSION['error'] = "Vous n'avez pas les droits suffisants pour cette action.";
            header('Location: index.php?controleur=tableau&action=index');
            exit;
        }
    }

    /**
     * Vérifie si l'utilisateur peut effectuer une action critique
     */
    public static function peutEffectuerActionCritique($action) {
        return self::estAdmin() && self::aPermission($action);
    }

    /**
     * Vérifie si l'utilisateur peut voir les rapports sensibles
     */
    public static function peutVoirRapportsSensibles() {
        return self::estAdmin() && self::aPermission('voir_rapports_financiers');
    }
} 