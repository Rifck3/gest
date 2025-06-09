<?php
require_once 'config.php'; // Adapter si besoin
require_once 'modeles/Produit.php';
require_once 'modeles/MouvementStock.php';

$produitModel = new Produit($db);
$mouvementModel = new MouvementStock($db);

// Récupérer tous les produits
$stmt = $produitModel->lireTous();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($produits as $produit) {
    // Vérifier s'il existe déjà un mouvement pour ce produit
    $mouvements = $mouvementModel->lireParProduit($produit['id'], 1);
    if (empty($mouvements)) {
        // Créer un mouvement d'entrée initiale
        $mouvementModel->produit_id = $produit['id'];
        $mouvementModel->type_mouvement = 'entree';
        $mouvementModel->quantite = $produit['quantite'];
        $mouvementModel->reference = 'INIT-' . $produit['id'];
        $mouvementModel->raison = "Initialisation du stock";
        $mouvementModel->notes = "Mouvement généré automatiquement pour initialiser l'historique";
        $mouvementModel->utilisateur_id = 1; // À adapter si besoin
        $mouvementModel->creer();
        echo "Mouvement créé pour le produit ID " . $produit['id'] . "<br>";
    }
}
echo "Traitement terminé."; 