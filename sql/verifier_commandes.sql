-- Vérifier si la table commandes existe
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'commandes');

-- Si la table n'existe pas, la créer
SET @create_table = IF(@table_exists = 0,
    'CREATE TABLE commandes (
        id int(11) NOT NULL AUTO_INCREMENT,
        reference varchar(20) NOT NULL,
        fournisseur_id int(11) NOT NULL,
        utilisateur_id int(11) NOT NULL,
        statut enum("en_attente","validee","annulee") NOT NULL DEFAULT "en_attente",
        montant_total decimal(10,2) DEFAULT 0.00,
        commentaire text,
        date_creation datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        date_validation datetime DEFAULT NULL,
        date_annulation datetime DEFAULT NULL,
        valide_par int(11) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY reference (reference),
        KEY fournisseur_id (fournisseur_id),
        KEY utilisateur_id (utilisateur_id),
        KEY valide_par (valide_par),
        CONSTRAINT commandes_ibfk_1 FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs (id),
        CONSTRAINT commandes_ibfk_2 FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id),
        CONSTRAINT commandes_ibfk_3 FOREIGN KEY (valide_par) REFERENCES utilisateurs (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    'SELECT "La table commandes existe déjà."'
);

PREPARE stmt FROM @create_table;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Vérifier si la colonne date_creation existe
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns 
    WHERE table_schema = DATABASE() 
    AND table_name = 'commandes' 
    AND column_name = 'date_creation');

-- Si la colonne n'existe pas, l'ajouter
SET @add_column = IF(@column_exists = 0,
    'ALTER TABLE commandes ADD COLUMN date_creation datetime NOT NULL DEFAULT CURRENT_TIMESTAMP',
    'SELECT "La colonne date_creation existe déjà."'
);

PREPARE stmt FROM @add_column;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Vérifier si la table commande_details existe
SET @details_table_exists = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'commande_details');

-- Si la table n'existe pas, la créer
SET @create_details_table = IF(@details_table_exists = 0,
    'CREATE TABLE commande_details (
        id int(11) NOT NULL AUTO_INCREMENT,
        commande_id int(11) NOT NULL,
        produit_id int(11) NOT NULL,
        quantite int(11) NOT NULL,
        prix_unitaire decimal(10,2) NOT NULL,
        PRIMARY KEY (id),
        KEY commande_id (commande_id),
        KEY produit_id (produit_id),
        CONSTRAINT commande_details_ibfk_1 FOREIGN KEY (commande_id) REFERENCES commandes (id) ON DELETE CASCADE,
        CONSTRAINT commande_details_ibfk_2 FOREIGN KEY (produit_id) REFERENCES produits (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
    'SELECT "La table commande_details existe déjà."'
);

PREPARE stmt FROM @create_details_table;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 