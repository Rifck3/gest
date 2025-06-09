<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails du fournisseur</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations du fournisseur</h6>
                    <div class="dropdown no-arrow">
                        <a href="index.php?controleur=fournisseur&action=index" class="btn btn-secondary btn-sm">
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
                                    <td><?php echo $fournisseur['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Nom</th>
                                    <td><?php echo $fournisseur['nom']; ?></td>
                                </tr>
                                <tr>
                                    <th>Contact</th>
                                    <td><?php echo $fournisseur['contact'] ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Téléphone</th>
                                    <td><?php echo $fournisseur['telephone'] ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo $fournisseur['email'] ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Coordonnées</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Adresse</th>
                                    <td><?php echo nl2br($fournisseur['adresse']) ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Notes</th>
                                    <td><?php echo nl2br($fournisseur['notes']) ?: '<span class="text-muted">Aucune note</span>'; ?></td>
                                </tr>
                                <tr>
                                    <th>Date de création</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($fournisseur['date_creation'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Dernière modification</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($fournisseur['date_modification'])); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Produits associés</h5>
                            <?php if(count($produits) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom</th>
                                                <th>Catégorie</th>
                                                <th>Prix unitaire</th>
                                                <th>Quantité</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($produits as $produit): ?>
                                                <tr>
                                                    <td><?php echo $produit['id']; ?></td>
                                                    <td><?php echo $produit['nom']; ?></td>
                                                    <td><?php echo $produit['categorie_id'] ?? 'Non catégorisé'; ?></td>
                                                    <td class="text-end"><?php echo number_format($produit['prix_unitaire'], 2, ',', ' '); ?> Fr</td>
                                                    <td class="text-center"><?php echo $produit['quantite']; ?></td>
                                                    <td>
                                                        <a href="index.php?controleur=produit&action=voir&id=<?php echo $produit['id']; ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    Aucun produit n'est associé à ce fournisseur.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Derniers mouvements de stock</h5>
                            <?php if(count($mouvements) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Produit</th>
                                                <th>Type</th>
                                                <th>Quantité</th>
                                                <th>Utilisateur</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($mouvements as $mouvement): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y H:i', strtotime($mouvement['date_mouvement'])); ?></td>
                                                    <td><?php echo $mouvement['nom_produit']; ?></td>
                                                    <td>
                                                        <?php if($mouvement['type_mouvement'] == 'entree'): ?>
                                                            <span class="badge bg-success">Entrée</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Sortie</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center"><?php echo $mouvement['quantite']; ?></td>
                                                    <td><?php echo $mouvement['utilisateur']; ?></td>
                                                    <td>
                                                        <a href="index.php?controleur=mouvement&action=voir&id=<?php echo $mouvement['id']; ?>" class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye"></i> Voir
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    Aucun mouvement de stock n'est associé à ce fournisseur.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="index.php?controleur=fournisseur&action=modifier&id=<?php echo $fournisseur['id']; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="javascript:void(0);" onclick="confirmerSuppression(<?php echo $fournisseur['id']; ?>, '<?php echo addslashes($fournisseur['nom']); ?>')" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le fournisseur <span id="fournisseurName"></span> ?</p>
                <p class="text-danger">Cette action est irréversible. Assurez-vous qu'aucun produit n'est associé à ce fournisseur avant de le supprimer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonction pour confirmer la suppression
    function confirmerSuppression(id, nom) {
        document.getElementById('fournisseurName').textContent = nom;
        document.getElementById('deleteLink').href = 'index.php?controleur=fournisseur&action=supprimer&id=' + id;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<?php require_once 'vues/includes/footer.php'; ?>
