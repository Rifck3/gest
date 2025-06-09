<?php
// Vérification des permissions
if (!isset($_SESSION['user_id']) || !Permissions::aPermission('voir_produits')) {
    header('Location: index.php?controleur=tableau&action=index');
    exit();
}

// Vérifier si le produit existe
if (!is_array($produit)) {
    echo '<div class="alert alert-danger">Le produit demandé n\'existe pas.</div>';
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails du produit</h1>
        <div>
            <a href="index.php?controleur=produit&action=index" class="btn btn-secondary" data-toggle="tooltip" title="Retourner à la liste des produits">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <?php if (Permissions::aPermission('modifier_produits')): ?>
                <a href="index.php?controleur=produit&action=modifier&id=<?php echo $produit['id']; ?>" class="btn btn-warning" data-toggle="tooltip" title="Modifier ce produit">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du produit</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Nom</th>
                            <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td><?php echo nl2br(htmlspecialchars($produit['description'] ?? '')); ?></td>
                        </tr>
                        <tr>
                            <th>Catégorie</th>
                            <td><?php echo htmlspecialchars($produit['categorie_nom'] ?? 'Non catégorisé'); ?></td>
                        </tr>
                        <tr>
                            <th>Fournisseur</th>
                            <td><?php echo htmlspecialchars($produit['fournisseur_nom'] ?? 'Non spécifié'); ?></td>
                        </tr>
                        <tr>
                            <th>Prix unitaire</th>
                            <td><?php echo number_format($produit['prix_unitaire'], 2, ',', ' '); ?> Fr</td>
                        </tr>
                        <tr>
                            <th>Quantité en stock</th>
                            <td>
                                <?php if ($produit['quantite'] <= $produit['quantite_min']): ?>
                                    <span class="badge badge-danger"><?php echo $produit['quantite']; ?></span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?php echo $produit['quantite']; ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Stock minimum</th>
                            <td><?php echo $produit['quantite_min']; ?></td>
                        </tr>
                        <tr>
                            <th>Date de création</th>
                            <td><?php echo date('d/m/Y H:i', strtotime($produit['date_creation'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Historique des mouvements</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($mouvements)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Quantité</th>
                                        <th>Utilisateur</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($mouvements as $mouvement): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i', strtotime($mouvement['date_mouvement'])); ?></td>
                                            <td>
                                                <?php if ($mouvement['type_mouvement'] == 'entree'): ?>
                                                    <span class="badge badge-success">Entrée</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Sortie</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $mouvement['quantite']; ?></td>
                                            <td><?php echo htmlspecialchars($mouvement['utilisateur'] ?? 'Système'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Aucun mouvement enregistré pour ce produit.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script> 