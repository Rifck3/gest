-- Ajout des colonnes manquantes à la table journal_activites
ALTER TABLE journal_activites
ADD COLUMN IF NOT EXISTS ip_adresse VARCHAR(45) AFTER description,
ADD COLUMN IF NOT EXISTS navigateur VARCHAR(255) AFTER ip_adresse; 