<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rapport: Mouvements de stock</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Filtrer les résultats</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="index.php">
                        <input type="hidden" name="controleur" value="rapport">
                        <input type="hidden" name="action" value="mouvementsStock">
                        
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
                                <label for="type_mouvement">Type de mouvement</label>
                                <select class="form-control" id="type_mouvement" name="type_mouvement">
                                    <option value="">Tous</option>
                                    <option value="entree" <?php echo (isset($_GET['type_mouvement']) && $_GET['type_mouvement'] == 'entree') ? 'selected' : ''; ?>>Entrées</option>
                                    <option value="sortie" <?php echo (isset($_GET['type_mouvement']) && $_GET['type_mouvement'] == 'sortie') ? 'selected' : ''; ?>>Sorties</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="produit_id">Produit</label>
                                <select class="form-control select2" id="produit_id" name="produit_id">
                                    <option value="">Tous les produits</option>
                                    <?php 
                                    $stmt = $produit->lireTous();
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['produit_id']) && $_GET['produit_id'] == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo $row['nom']; ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="categorie_id">Catégorie</label>
                                <select class="form-control" id="categorie_id" name="categorie_id">
                                    <option value="">Toutes les catégories</option>
                                    <?php foreach($categories as $categorie): ?>
                                    <option value="<?php echo $categorie['id']; ?>" <?php echo (isset($_GET['categorie_id']) && $_GET['categorie_id'] == $categorie['id']) ? 'selected' : ''; ?>>
                                        <?php echo $categorie['nom']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="fournisseur_id">Fournisseur</label>
                                <select class="form-control" id="fournisseur_id" name="fournisseur_id">
                                    <option value="">Tous les fournisseurs</option>
                                    <?php foreach($fournisseurs as $fournisseur): ?>
                                    <option value="<?php echo $fournisseur['id']; ?>" <?php echo (isset($_GET['fournisseur_id']) && $_GET['fournisseur_id'] == $fournisseur['id']) ? 'selected' : ''; ?>>
                                        <?php echo $fournisseur['nom']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter fa-sm"></i> Filtrer
                                </button>
                                <a href="index.php?controleur=rapport&action=mouvementsStock" class="btn btn-secondary mr-2">
                                    <i class="fas fa-sync-alt fa-sm"></i> Réinitialiser
                                </a>
                                <a href="index.php?controleur=rapport&action=exporterCSV&type_rapport=mouvements_stock<?php echo isset($_GET['date_debut']) ? '&date_debut='.$_GET['date_debut'] : ''; ?><?php echo isset($_GET['date_fin']) ? '&date_fin='.$_GET['date_fin'] : ''; ?><?php echo isset($_GET['type_mouvement']) ? '&type_mouvement='.$_GET['type_mouvement'] : ''; ?><?php echo isset($_GET['produit_id']) ? '&produit_id='.$_GET['produit_id'] : ''; ?><?php echo isset($_GET['categorie_id']) ? '&categorie_id='.$_GET['categorie_id'] : ''; ?><?php echo isset($_GET['fournisseur_id']) ? '&fournisseur_id='.$_GET['fournisseur_id'] : ''; ?>" class="btn btn-success">
                                    <i class="fas fa-file-csv fa-sm"></i> Exporter en CSV
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
                    <h6 class="m-0 font-weight-bold text-primary">Résumé</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Nombre de mouvements</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($mouvements); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total des entrées</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_entrees; ?> unités</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Total des sorties</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_sorties; ?> unités</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique des mouvements -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Évolution des mouvements</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="mouvementsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center align-middle">Date</th>
                                    <th class="text-center align-middle">Type</th>
                                    <th class="text-center align-middle">Produit</th>
                                    <th class="text-center align-middle">Catégorie</th>
                                    <th class="text-center align-middle">Quantité</th>
                                    <th class="text-center align-middle">Référence</th>
                                    <th class="text-center align-middle">Fournisseur</th>
                                    <th class="text-center align-middle">Utilisateur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($mouvements)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Aucun mouvement de stock trouvé</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($mouvements as $mouvement): ?>
                                <tr>
                                    <td class="text-center align-middle"><?php echo date('d/m/Y H:i', strtotime($mouvement['date_mouvement'])); ?></td>
                                    <td class="text-center align-middle">
                                        <?php if($mouvement['type_mouvement'] == 'entree'): ?>
                                            <span class="badge bg-success px-3 py-2">Entrée</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger px-3 py-2">Sortie</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($mouvement['nom_produit'] ?? ''); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($mouvement['nom_categorie'] ?? ''); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($mouvement['quantite'] ?? '0'); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($mouvement['reference'] ?? ''); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($mouvement['nom_fournisseur'] ?? ''); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($mouvement['utilisateur'] ?? ''); ?></td>
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

<!-- Script pour le graphique -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique
    var donnees = <?php echo json_encode($donnees_graphique); ?>;
    
    // Préparer les données pour Chart.js
    var labels = [];
    var entreesData = [];
    var sortiesData = [];
    
    donnees.forEach(function(item) {
        labels.push(item.jour);
        entreesData.push(item.total_entrees);
        sortiesData.push(item.total_sorties);
    });
    
    // Créer le graphique
    var ctx = document.getElementById('mouvementsChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Entrées',
                    data: entreesData,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointRadius: 3
                },
                {
                    label: 'Sorties',
                    data: sortiesData,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(220, 53, 69, 1)',
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantité'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            }
        }
    });
});
</script>

<?php require_once 'vues/includes/footer.php'; ?>
