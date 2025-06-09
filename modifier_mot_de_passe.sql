-- Exemple 1: Désactiver le hachage (stockage en clair)
-- Remplacez 'votre_mot_de_passe' par le mot de passe souhaité
UPDATE utilisateurs 
SET mot_de_passe = 'votre_mot_de_passe' 
WHERE nom_utilisateur = 'admin';

-- Exemple 2: Activer le hachage (mot de passe: admin123)
-- Ce hash correspond au mot de passe 'admin123'
UPDATE utilisateurs 
SET mot_de_passe = '$2y$10$i5Sx1lTwCMefcvdIo85ele/mUIL32e2h9TUCZVc4kqnvbdA3CXPE2' 
WHERE nom_utilisateur = 'admin';

-- Pour vérifier le résultat :
SELECT nom_utilisateur, mot_de_passe 
FROM utilisateurs 
WHERE nom_utilisateur = 'admin'; 