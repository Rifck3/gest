-- Désactiver le hachage pour tous les utilisateurs
-- Remplacez 'mot_de_passe_par_defaut' par le mot de passe souhaité pour tous les utilisateurs
UPDATE utilisateurs 
SET mot_de_passe = 'mot_de_passe_par_defaut';

-- Pour vérifier le résultat :
SELECT id, nom_utilisateur, mot_de_passe, role 
FROM utilisateurs;

-- Pour voir combien d'utilisateurs ont été modifiés :
SELECT COUNT(*) as nombre_utilisateurs_modifies 
FROM utilisateurs; 