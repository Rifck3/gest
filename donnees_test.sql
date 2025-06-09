-- Données de test pour la table categories
INSERT INTO categories (nom, description) VALUES
('Électronique', 'Produits électroniques et accessoires'),
('Informatique', 'Matériel et logiciels informatiques'),
('Mobilier', 'Meubles et accessoires de bureau'),
('Fournitures', 'Fournitures de bureau générales'),
('Papeterie', 'Produits de papeterie et consommables');



-- Données de test pour la table produits
INSERT INTO produits (nom, description, prix_unitaire, quantite_stock, seuil_alerte, categorie_id, etagere_id) VALUES
('Ordinateur portable HP', 'Ordinateur portable HP 15.6"', 699.99, 10, 5, 2, 3),
('Souris sans fil', 'Souris sans fil ergonomique', 29.99, 50, 10, 2, 4),
('Clavier mécanique', 'Clavier mécanique RGB', 89.99, 15, 5, 2, 3),
('Écran 24"', 'Écran LED 24 pouces', 199.99, 8, 3, 2, 3),
('Smartphone Samsung', 'Smartphone Samsung Galaxy S21', 799.99, 12, 5, 1, 1),
('Chargeur USB', 'Chargeur USB rapide 20W', 19.99, 100, 20, 1, 2),
('Bureau ajustable', 'Bureau de bureau ajustable en hauteur', 299.99, 5, 2, 3, 5),
('Chaise ergonomique', 'Chaise de bureau ergonomique', 199.99, 8, 3, 3, 5),
('Rame de papier A4', 'Rame de papier A4 80g', 4.99, 200, 50, 4, NULL),
('Stylos à bille', 'Pack de 10 stylos à bille', 9.99, 150, 30, 4, NULL);

-- Données de test pour la table utilisateurs
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'System', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Dupont', 'Jean', 'jean.dupont@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestionnaire'),
('Martin', 'Sophie', 'sophie.martin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'utilisateur');

-- Données de test pour la table commandes
INSERT INTO commandes (date_commande, statut, utilisateur_id) VALUES
('2024-03-01 10:00:00', 'en_cours', 2),
('2024-03-02 14:30:00', 'terminee', 2),
('2024-03-03 09:15:00', 'en_cours', 3);

-- Données de test pour la table commande_details
INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
(1, 1, 1, 699.99),
(1, 2, 2, 29.99),
(2, 3, 1, 89.99),
(2, 4, 1, 199.99),
(3, 5, 1, 799.99),
(3, 6, 3, 19.99);

-- Données de test pour la table mouvements_stock
INSERT INTO mouvements_stock (produit_id, type_mouvement, quantite, date_mouvement, utilisateur_id, commentaire) VALUES
(1, 'entree', 10, '2024-03-01 09:00:00', 2, 'Réception initiale'),
(2, 'entree', 50, '2024-03-01 09:30:00', 2, 'Réception initiale'),
(3, 'entree', 15, '2024-03-01 10:00:00', 2, 'Réception initiale'),
(4, 'entree', 8, '2024-03-01 10:30:00', 2, 'Réception initiale'),
(5, 'entree', 12, '2024-03-01 11:00:00', 2, 'Réception initiale'),
(1, 'sortie', 1, '2024-03-02 14:30:00', 2, 'Vente - Commande #2'),
(2, 'sortie', 2, '2024-03-02 14:30:00', 2, 'Vente - Commande #2'),
(3, 'sortie', 1, '2024-03-02 14:30:00', 2, 'Vente - Commande #2'),
(4, 'sortie', 1, '2024-03-02 14:30:00', 2, 'Vente - Commande #2');

-- Données de test pour la table journal_activites
INSERT INTO journal_activites (utilisateur_id, type_activite, description, date_activite) VALUES
(2, 'connexion', 'Connexion au système', '2024-03-01 08:30:00'),
(2, 'ajout_produit', 'Ajout de nouveaux produits', '2024-03-01 09:00:00'),
(2, 'vente', 'Vente - Commande #2', '2024-03-02 14:30:00'),
(3, 'connexion', 'Connexion au système', '2024-03-03 09:00:00'),
(3, 'vente', 'Vente - Commande #3', '2024-03-03 09:15:00'); 