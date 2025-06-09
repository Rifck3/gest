<?php
// Initialisation des statistiques si elles ne sont pas déjà définies
if (!isset($stats)) {
    $stats = [];
    
    // Obtenir la connexion à la base de données
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    // Requête pour le nombre total de fournisseurs
    $query = "SELECT COUNT(*) as total FROM fournisseurs";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_fournisseurs'] = $result['total'];
    
    // Requête pour les produits en rupture de stock
    $query = "SELECT COUNT(*) as total FROM produits WHERE quantite <= 0";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['produits_rupture_stock'] = $result['total'];
    
    // Requête pour les produits en stock faible
    $query = "SELECT COUNT(*) as total FROM produits WHERE quantite <= quantite_min AND quantite > 0";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['produits_stock_faible'] = $result['total'];
}
?> 