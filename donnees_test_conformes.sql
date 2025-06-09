-- Données de test pour la table categories
INSERT INTO categories (nom, description) VALUES
('Électronique', 'Produits électroniques et accessoires'),
('Informatique', 'Matériel et logiciels informatiques'),
('Mobilier', 'Meubles et accessoires de bureau'),
('Fournitures', 'Fournitures de bureau générales'),
('Papeterie', 'Produits de papeterie et consommables');

-- Données de test pour la table fournisseurs
INSERT INTO fournisseurs (nom, personne_contact, telephone, email, adresse) VALUES
('TechWorld', 'Thomas Bernard', '0123456789', 'contact@techworld.com', '123 Rue de la Technologie, Paris'),
('OfficePro', 'Sophie Dubois', '0234567890', 'info@officepro.com', '456 Avenue des Bureaux, Lyon'),
('MobilierPlus', 'Pierre Martin', '0345678901', 'ventes@mobilierplus.com', '789 Boulevard du Mobilier, Marseille'),
('PapeterieExpress', 'Marie Lambert', '0456789012', 'contact@papeterieexpress.com', '321 Rue du Papier, Bordeaux'),
('InformatiquePro', 'Lucas Petit', '0567890123', 'info@informatiquepro.com', '654 Avenue des Ordinateurs, Lille');

-- Données de test pour la table produits
INSERT INTO produits (nom, description, categorie_id, fournisseur_id, prix_unitaire, quantite, quantite_min) VALUES
('Ordinateur portable HP', 'Ordinateur portable HP 15.6"', 2, 5, 699.99, 10, 5),
('Souris sans fil', 'Souris sans fil ergonomique', 2, 5, 29.99, 50, 10),
('Clavier mécanique', 'Clavier mécanique RGB', 2, 5, 89.99, 15, 5),
('Écran 24"', 'Écran LED 24 pouces', 2, 5, 199.99, 8, 3),
('Smartphone Samsung', 'Smartphone Samsung Galaxy S21', 1, 1, 799.99, 12, 5),
('Chargeur USB', 'Chargeur USB rapide 20W', 1, 1, 19.99, 100, 20),
('Bureau ajustable', 'Bureau de bureau ajustable en hauteur', 3, 3, 299.99, 5, 2),
('Chaise ergonomique', 'Chaise de bureau ergonomique', 3, 3, 199.99, 8, 3),
('Rame de papier A4', 'Rame de papier A4 80g', 4, 4, 4.99, 200, 50),
('Stylos à bille', 'Pack de 10 stylos à bille', 5, 4, 9.99, 150, 30);

-- Données de test pour la table utilisateurs
INSERT INTO utilisateurs (nom_utilisateur, mot_de_passe, nom_complet, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur Système', 'admin@example.com', 'admin'),
('jean.dupont', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jean Dupont', 'jean.dupont@example.com', 'gestionnaire'),
('sophie.martin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sophie Martin', 'sophie.martin@example.com', 'employe'),
('pierre.durand', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pierre Durand', 'pierre.durand@example.com', 'employe');

-- Données de test pour la table commandes
INSERT INTO commandes (reference, fournisseur_id, date_commande, statut, montant_total, utilisateur_id, valide_par, date_validation, commentaire) VALUES
('CMD-2024-001', 5, '2024-03-01 10:00:00', 'completee', 1299.97, 2, 1, '2024-03-01 11:00:00', 'Commande de matériel informatique'),
('CMD-2024-002', 1, '2024-03-02 14:30:00', 'completee', 859.96, 2, 1, '2024-03-02 15:30:00', 'Commande de produits électroniques'),
('CMD-2024-003', 3, '2024-03-03 09:15:00', 'en_attente', 499.98, 3, NULL, NULL, 'Commande de mobilier de bureau'),
('CMD-2024-004', 4, '2024-03-04 11:20:00', 'en_attente', 149.97, 4, NULL, NULL, 'Commande de fournitures de bureau');

-- Données de test pour la table details_commande
INSERT INTO details_commande (commande_id, produit_id, quantite, prix_unitaire) VALUES
(1, 1, 1, 699.99),
(1, 2, 2, 29.99),
(1, 3, 1, 89.99),
(2, 5, 1, 799.99),
(2, 6, 3, 19.99),
(3, 7, 1, 299.99),
(3, 8, 1, 199.99),
(4, 9, 10, 4.99),
(4, 10, 10, 9.99);

-- Données de test pour la table mouvements_stock
INSERT INTO mouvements_stock (produit_id, type_mouvement, quantite, prix_unitaire, raison, reference, utilisateur_id, date_mouvement) VALUES
(1, 'entree', 10, 699.99, 'Réception initiale', 'CMD-2024-001', 2, '2024-03-01 09:00:00'),
(2, 'entree', 50, 29.99, 'Réception initiale', 'CMD-2024-001', 2, '2024-03-01 09:30:00'),
(3, 'entree', 15, 89.99, 'Réception initiale', 'CMD-2024-001', 2, '2024-03-01 10:00:00'),
(4, 'entree', 8, 199.99, 'Réception initiale', 'CMD-2024-001', 2, '2024-03-01 10:30:00'),
(5, 'entree', 12, 799.99, 'Réception initiale', 'CMD-2024-002', 2, '2024-03-01 11:00:00'),
(1, 'sortie', 1, 699.99, 'Vente', 'VENTE-2024-001', 3, '2024-03-02 14:30:00'),
(2, 'sortie', 2, 29.99, 'Vente', 'VENTE-2024-001', 3, '2024-03-02 14:30:00'),
(3, 'sortie', 1, 89.99, 'Vente', 'VENTE-2024-001', 3, '2024-03-02 14:30:00'),
(4, 'sortie', 1, 199.99, 'Vente', 'VENTE-2024-001', 3, '2024-03-02 14:30:00');

-- Données de test pour la table journal_activites
INSERT INTO journal_activites (utilisateur_id, type_activite, description, date_activite, ip_adresse, navigateur) VALUES
(2, 'connexion', 'Connexion au système', '2024-03-01 08:30:00', '192.168.1.100', 'Chrome 120.0.0'),
(2, 'ajout_produit', 'Ajout de nouveaux produits', '2024-03-01 09:00:00', '192.168.1.100', 'Chrome 120.0.0'),
(2, 'commande', 'Création de la commande CMD-2024-001', '2024-03-01 10:00:00', '192.168.1.100', 'Chrome 120.0.0'),
(1, 'validation', 'Validation de la commande CMD-2024-001', '2024-03-01 11:00:00', '192.168.1.101', 'Firefox 121.0.0'),
(3, 'connexion', 'Connexion au système', '2024-03-03 09:00:00', '192.168.1.102', 'Edge 120.0.0'),
(3, 'vente', 'Vente - VENTE-2024-001', '2024-03-03 09:15:00', '192.168.1.102', 'Edge 120.0.0'),
(4, 'connexion', 'Connexion au système', '2024-03-04 10:00:00', '192.168.1.103', 'Safari 17.0.0'),
(4, 'commande', 'Création de la commande CMD-2024-004', '2024-03-04 11:20:00', '192.168.1.103', 'Safari 17.0.0'); 