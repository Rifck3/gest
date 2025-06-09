<?php
// Vérification des permissions
if (!isset($_SESSION['user_id']) || !Permissions::aPermission('gestion_commandes')) {
    header('Location: index.php?controleur=tableau&action=index');
    exit();
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Gestion des commandes</h1>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php" class="row">
                <input type="hidden" name="controleur" value="commande">
                <input type="hidden" name="action" value="index">
                
                <div class="col-md-3 mb-3">
                    <label for="statut">Statut</label>
                    <select name="statut" id="statut" class="form-control">
                        <option value="">Tous</option>
                        <option value="en_attente" <?php echo isset($_GET['statut']) && $_GET['statut'] == 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                        <option value="validee" <?php echo isset($_GET['statut']) && $_GET['statut'] == 'validee' ? 'selected' : ''; ?>>Validée</option>
                        <option value="annulee" <?php echo isset($_GET['statut']) && $_GET['statut'] == 'annulee' ? 'selected' : ''; ?>>Annulée</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="fournisseur">Fournisseur</label>
                    <select name="fournisseur_id" id="fournisseur" class="form-control">
                        <option value="">Tous</option>
                        <?php foreach ($fournisseurs as $fournisseur): ?>
                            <option value="<?php echo $fournisseur['id']; ?>" <?php echo isset($_GET['fournisseur_id']) && $_GET['fournisseur_id'] == $fournisseur['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fournisseur['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="date_debut">Date début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?php echo isset($_GET['date_debut']) ? $_GET['date_debut'] : ''; ?>">
                </div>

                <div class="col-md-3 mb-3">
                    <label for="date_fin">Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?php echo isset($_GET['date_fin']) ? $_GET['date_fin'] : ''; ?>">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="index.php?controleur=commande&action=index" class="btn btn-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des commandes</h6>
            <?php if (Permissions::aPermission('creer_commandes')): ?>
                <a href="index.php?controleur=commande&action=creer" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Créer une nouvelle commande">
                    <i class="fas fa-plus"></i> Nouvelle commande
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Fournisseur</th>
                            <th>Créée par</th>
                            <th>Statut</th>
                            <th>Montant</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($commandes && $commandes->rowCount() > 0): ?>
                            <?php while ($commande = $commandes->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></td>
                                    <td><?php echo htmlspecialchars($commande['fournisseur_nom']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['utilisateur_nom']); ?></td>
                                    <td>
                                        <?php
                                        $statut_class = '';
                                        $statut_text = '';
                                        switch($commande['statut']) {
                                            case 'en_attente':
                                                $statut_class = 'warning';
                                                $statut_text = 'En attente';
                                                break;
                                            case 'validee':
                                                $statut_class = 'success';
                                                $statut_text = 'Validée';
                                                break;
                                            case 'annulee':
                                                $statut_class = 'danger';
                                                $statut_text = 'Annulée';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $statut_class; ?>"><?php echo $statut_text; ?></span>
                                    </td>
                                    <td class="text-end"><?php echo number_format($commande['montant_total'], 2); ?> Fr</td>
                                    <td>
                                        <?php if (Permissions::aPermission('voir_commandes')): ?>
                                            <a href="index.php?controleur=commande&action=voir&id=<?php echo $commande['id']; ?>" 
                                               class="btn btn-info btn-sm" 
                                               data-toggle="tooltip" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($commande['statut'] == 'en_attente'): ?>
                                            <?php if (Permissions::aPermission('modifier_commandes')): ?>
                                                <a href="index.php?controleur=commande&action=modifier&id=<?php echo $commande['id']; ?>" 
                                                   class="btn btn-warning btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="Modifier la commande">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>

                                            <?php if (Permissions::aPermission('annuler_commandes')): ?>
                                                <a href="index.php?controleur=commande&action=annuler&id=<?php echo $commande['id']; ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   data-toggle="tooltip" 
                                                   title="Annuler la commande"
                                                   onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande ? Cette action est irréversible.')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if (Permissions::aPermission('supprimer_commandes')): ?>
                                            <a href="index.php?controleur=commande&action=supprimer&id=<?php echo $commande['id']; ?>" 
                                               class="btn btn-outline-danger btn-sm" 
                                               data-toggle="tooltip" 
                                               title="Supprimer la commande"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ? Cette action est irréversible.')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucune commande trouvée</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "order": [[1, "desc"]]
    });
});
</script> 