<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails du mouvement de stock</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du mouvement</h6>
                    <div class="dropdown no-arrow">
                        <a href="index.php?controleur=mouvement&action=index" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Informations générales</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">ID</th>
                                    <td><?php echo $mouvement['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <?php if($mouvement['type_mouvement'] == 'entree'): ?>
                                        <span class="badge badge-success">Entrée</span>
                                        <?php else: ?>
                                        <span class="badge badge-danger">Sortie</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($mouvement['date_mouvement'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Utilisateur</th>
                                    <td><?php echo $mouvement['utilisateur']; ?></td>
                                </tr>
                                <tr>
                                    <th>Référence</th>
                                    <td><?php echo $mouvement['reference'] ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Raison</th>
                                    <td><?php echo $mouvement['raison'] ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Détails du produit</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Produit</th>
                                    <td>
                                        <a href="index.php?controleur=produit&action=voir&id=<?php echo $mouvement['produit_id']; ?>">
                                            <?php echo $mouvement['nom_produit']; ?>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Quantité</th>
                                    <td><?php echo $mouvement['quantite']; ?></td>
                                </tr>
                                <tr>
                                    <th>Fournisseur</th>
                                    <td>
                                        <?php if(!empty($mouvement['nom_fournisseur'])): ?>
                                        <a href="index.php?controleur=fournisseur&action=voir&id=<?php echo $mouvement['fournisseur_id']; ?>">
                                            <?php echo $mouvement['nom_fournisseur']; ?>
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted">Non applicable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if(!empty($mouvement['notes'])): ?>
                                <tr>
                                    <th>Notes</th>
                                    <td><?php echo nl2br(htmlspecialchars($mouvement['notes'])); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vues/includes/footer.php'; ?>
