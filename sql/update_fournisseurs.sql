-- Ajout des colonnes pour la gestion des fournisseurs
ALTER TABLE fournisseurs
ADD COLUMN note DECIMAL(3,2) DEFAULT NULL,
ADD COLUMN delai_livraison_moyen INT DEFAULT NULL,
ADD COLUMN date_derniere_evaluation DATE DEFAULT NULL;

-- Table pour l'historique des évaluations des fournisseurs
CREATE TABLE IF NOT EXISTS evaluations_fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fournisseur_id INT NOT NULL,
    note DECIMAL(3,2) NOT NULL,
    commentaire TEXT,
    date_evaluation DATE NOT NULL,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table pour les documents des fournisseurs
CREATE TABLE IF NOT EXISTS documents_fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fournisseur_id INT NOT NULL,
    type_document ENUM('contrat', 'facture', 'autre') NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL,
    chemin_fichier VARCHAR(255) NOT NULL,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table pour le suivi des délais de livraison
CREATE TABLE IF NOT EXISTS suivi_livraisons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    fournisseur_id INT NOT NULL,
    date_commande DATE NOT NULL,
    date_livraison_prevue DATE NOT NULL,
    date_livraison_reelle DATE,
    statut ENUM('en_attente', 'en_cours', 'livree', 'retard') NOT NULL,
    commentaire TEXT,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id)
); 