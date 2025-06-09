<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Mouvements de stock</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Filtrer les résultats</h6>
                    <div class="dropdown no-arrow">
                        <a href="index.php?controleur=mouvement&action=ajouter" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus fa-sm"></i> Nouveau mouvement
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="index.php">
                        <input type="hidden" name="controleur" value="mouvement">
                        <input type="hidden" name="action" value="index">
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="date_debut">Date de début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d', strtotime('-30 days')); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="date_fin">Date de fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="type">Type de mouvement</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">Tous</option>
                                    <option value="entree" <?php echo (isset($_GET['type']) && $_GET['type'] == 'entree') ? 'selected' : ''; ?>>Entrées</option>
                                    <option value="sortie" <?php echo (isset($_GET['type']) && $_GET['type'] == 'sortie') ? 'selected' : ''; ?>>Sorties</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="produit_id">Produit</label>
                                <select class="form-control" id="produit_id" name="produit_id">
                                    <option value="">Tous les produits</option>
                                    <?php foreach($produits as $produit_item): ?>
                                    <option value="<?php echo $produit_item['id']; ?>" <?php echo (isset($_GET['produit_id']) && $_GET['produit_id'] == $produit_item['id']) ? 'selected' : ''; ?>>
                                        <?php echo $produit_item['nom']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter fa-sm"></i> Filtrer
                                </button>
                                <a href="index.php?controleur=mouvement&action=index" class="btn btn-secondary">
                                    <i class="fas fa-redo fa-sm"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Liste des mouvements</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Raison</th>
                                    <th>Utilisateur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($mouvements)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucun mouvement de stock trouvé</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($mouvements as $mouvement): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($mouvement['date_mouvement'])); ?></td>
                                    <td>
                                        <?php if($mouvement['type_mouvement'] == 'entree'): ?>
                                            <span class="badge bg-success">Entrée</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Sortie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="index.php?controleur=produit&action=voir&id=<?php echo $mouvement['produit_id']; ?>">
                                            <?php echo htmlspecialchars($mouvement['nom_produit'] ?? 'Produit inconnu'); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $mouvement['quantite']; ?></td>
                                    <td><?php echo htmlspecialchars($mouvement['raison'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($mouvement['utilisateur'] ?? ''); ?></td>
                                    <td>
                                        <a href="index.php?controleur=mouvement&action=modifier&id=<?php echo $mouvement['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controleur=mouvement&action=supprimer&id=<?php echo $mouvement['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce mouvement ?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vues/includes/footer.php'; ?>
