-- Modifier les mots de passe individuellement pour chaque utilisateur
-- Remplacez les mots de passe selon vos besoins

-- Pour l'administrateur
UPDATE utilisateurs 
SET mot_de_passe = 'admin123' 
WHERE nom_utilisateur = 'admin';

-- Pour les autres utilisateurs (exemples)
UPDATE utilisateurs 
SET mot_de_passe = 'user123' 
WHERE nom_utilisateur = 'user1';

UPDATE utilisateurs 
SET mot_de_passe = 'user456' 
WHERE nom_utilisateur = 'user2';

-- Pour voir la liste des utilisateurs et leurs mots de passe actuels :
SELECT id, nom_utilisateur, mot_de_passe, role 
FROM utilisateurs 
ORDER BY id;

-- Pour voir les utilisateurs qui n'ont pas encore de mot de passe défini :
SELECT id, nom_utilisateur, role 
FROM utilisateurs 
WHERE mot_de_passe IS NULL OR mot_de_passe = ''; 