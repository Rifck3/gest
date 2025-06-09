-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_stock;
USE gestion_stock;

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom_complet VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'gestionnaire', 'employe') NOT NULL,
    actif TINYINT(1) DEFAULT 1,
    derniere_connexion DATETIME DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    description TEXT
);

-- Table des fournisseurs
CREATE TABLE fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    personne_contact VARCHAR(100),
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des produits
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    categorie_id INT,
    fournisseur_id INT,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    quantite INT DEFAULT 0,
    quantite_min INT DEFAULT 5,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id),
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id)
);

-- Table des mouvements de stock
CREATE TABLE mouvements_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2),
    raison VARCHAR(100),
    reference VARCHAR(50),
    utilisateur_id INT,
    date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produit_id) REFERENCES produits(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table des commandes
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference VARCHAR(50) NOT NULL UNIQUE,
    fournisseur_id INT,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente', 'completee', 'annulee') DEFAULT 'en_attente',
    montant_total DECIMAL(10, 2),
    utilisateur_id INT,
    valide_par INT,
    date_validation DATETIME,
    commentaire TEXT,
    FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (valide_par) REFERENCES utilisateurs(id)
);

-- Table des détails de commande
CREATE TABLE details_commande (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Table des tokens de réinitialisation de mot de passe
CREATE TABLE tokens_reinitialisation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expiration DATETIME NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Table des journaux d'activités
CREATE TABLE journal_activites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    type_activite VARCHAR(50) NOT NULL,
    description TEXT,
    date_activite TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_adresse VARCHAR(45),
    navigateur VARCHAR(255),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Insertion d'un utilisateur administrateur par défaut (mot de passe: admin123)
INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, nom_complet, email, role) 
VALUES ('admin', '$2y$10$i5Sx1lTwCMefcvdIo85ele/mUIL32e2h9TUCZVc4kqnvbdA3CXPE2', 'Administrateur', 'admin@example.com', 'admin');

-- Insertion de quelques catégories
INSERT INTO categories (nom, description) VALUES 
('Électronique', 'Produits électroniques et gadgets'),
('Fournitures de bureau', 'Papeterie et fournitures de bureau'),
('Mobilier', 'Meubles et équipements');

-- Insertion de quelques fournisseurs
INSERT INTO fournisseurs (nom, personne_contact, telephone, email, adresse) VALUES 
('TechSupply', 'Jean Dupont', '0123456789', 'contact@techsupply.com', '123 Rue de la Technologie, Paris'),
('Office Plus', 'Marie Martin', '0234567890', 'info@officeplus.com', '456 Avenue des Bureaux, Lyon'),
('MeublesPro', 'Pierre Durand', '0345678901', 'ventes@meublespro.com', '789 Boulevard du Mobilier, Marseille');
