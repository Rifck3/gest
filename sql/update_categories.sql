-- Ajouter une contrainte UNIQUE sur la colonne 'nom' de la table 'categories'
ALTER TABLE categories ADD UNIQUE INDEX idx_categories_nom (nom); 