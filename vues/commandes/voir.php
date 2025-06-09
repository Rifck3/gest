<?php
// Vérification des permissions
if (!isset($_SESSION['user_id']) || !Permissions::aPermission('voir_commandes')) {
    header('Location: index.php?controleur=tableau&action=index');
    exit();
}

// Vérifier si la commande existe
if (!is_array($commande)) {
    echo '<div class="alert alert-danger">La commande demandée n\'existe pas.</div>';
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de la commande</h1>
        <a href="index.php?controleur=commande&action=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Informations de la commande</h6>
            <?php if ($commande['statut'] === 'en_attente'): ?>
                <div>
                    <?php if (Permissions::aPermission('valider_commandes')): ?>
                        <a href="index.php?controleur=commande&action=valider&id=<?php echo $commande['id']; ?>" 
                           class="btn btn-success btn-sm" 
                           data-toggle="tooltip"
                           title="Valider la commande"
                           onclick="return confirm('Êtes-vous sûr de vouloir valider cette commande ? Cette action est irréversible.')">
                            <i class="fas fa-check"></i> Valider
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning mt-2">
                            Vous n'avez pas la permission de valider cette commande. <br>
                            Si vous êtes administrateur et ne voyez pas le bouton, vérifiez vos permissions ou contactez le support technique.
                        </div>
                    <?php endif; ?>

                    <?php if (Permissions::aPermission('annuler_commandes')): ?>
                        <a href="index.php?controleur=commande&action=annuler&id=<?php echo $commande['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           data-toggle="tooltip"
                           title="Annuler la commande"
                           onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.')">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Référence:</strong> <?php echo htmlspecialchars($commande['reference']); ?></p>
                    <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></p>
                    <p><strong>Fournisseur:</strong> <?php echo htmlspecialchars($commande['fournisseur_nom']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Créé par:</strong> <?php echo htmlspecialchars($commande['utilisateur_nom']); ?></p>
                    <p><strong>Statut:</strong> 
                        <?php
                        $statutClasses = [
                            'en_attente' => 'warning',
                            'validee' => 'success',
                            'annulee' => 'danger'
                        ];
                        $statutLabels = [
                            'en_attente' => 'En attente',
                            'validee' => 'Validée',
                            'annulee' => 'Annulée'
                        ];
                        ?>
                        <span class="badge bg-<?php echo $statutClasses[$commande['statut']]; ?>">
                            <?php echo $statutLabels[$commande['statut']]; ?>
                        </span>
                    </p>
                    <?php if ($commande['statut'] === 'validee'): ?>
                        <p><strong>Date de validation:</strong> <?php echo date('d/m/Y H:i', strtotime($commande['date_validation'])); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Détails des produits</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total = 0;
                        foreach ($details as $detail): 
                            $total += $detail['quantite'] * $detail['prix_unitaire'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detail['produit_nom']); ?></td>
                                <td><?php echo $detail['quantite']; ?></td>
                                <td class="text-end"><?php echo number_format($detail['prix_unitaire'], 2); ?> Fr</td>
                                <td class="text-end"><?php echo number_format($detail['quantite'] * $detail['prix_unitaire'], 2); ?> Fr</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total</th>
                            <th class="text-end"><?php echo number_format($total, 2); ?> Fr</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialiser les tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script> 