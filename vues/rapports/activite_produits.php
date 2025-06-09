<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rapport: Activité des produits</h1>
    
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
                        <input type="hidden" name="action" value="activiteProduits">
                        
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
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="limite">Nombre de produits à afficher</label>
                                <select class="form-control" id="limite" name="limite">
                                    <option value="5" <?php echo (isset($_GET['limite']) && $_GET['limite'] == 5) ? 'selected' : ''; ?>>5</option>
                                    <option value="10" <?php echo (!isset($_GET['limite']) || $_GET['limite'] == 10) ? 'selected' : ''; ?>>10</option>
                                    <option value="20" <?php echo (isset($_GET['limite']) && $_GET['limite'] == 20) ? 'selected' : ''; ?>>20</option>
                                    <option value="50" <?php echo (isset($_GET['limite']) && $_GET['limite'] == 50) ? 'selected' : ''; ?>>50</option>
                                    <option value="100" <?php echo (isset($_GET['limite']) && $_GET['limite'] == 100) ? 'selected' : ''; ?>>100</option>
                                </select>
                            </div>
                            <div class="col-md-9 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter fa-sm"></i> Filtrer
                                </button>
                                <a href="index.php?controleur=rapport&action=activiteProduits" class="btn btn-secondary mr-2">
                                    <i class="fas fa-sync-alt fa-sm"></i> Réinitialiser
                                </a>
                                <a href="index.php?controleur=rapport&action=exporterCSV&type_rapport=activite_produits<?php echo isset($_GET['date_debut']) ? '&date_debut='.$_GET['date_debut'] : ''; ?><?php echo isset($_GET['date_fin']) ? '&date_fin='.$_GET['date_fin'] : ''; ?><?php echo isset($_GET['categorie_id']) ? '&categorie_id='.$_GET['categorie_id'] : ''; ?><?php echo isset($_GET['fournisseur_id']) ? '&fournisseur_id='.$_GET['fournisseur_id'] : ''; ?>" class="btn btn-success">
                                    <i class="fas fa-file-csv fa-sm"></i> Exporter en CSV
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphique d'activité des produits -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Produits les plus actifs</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="produitsBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tableau des produits les plus actifs -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails des produits les plus actifs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Produit</th>
                                    <th>Catégorie</th>
                                    <th>Fournisseur</th>
                                    <th>Total mouvements</th>
                                    <th>Entrées</th>
                                    <th>Sorties</th>
                                    <th>Stock actuel</th>
                                    <th>Stock minimum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($produits_actifs as $produit): ?>
                                <tr>
                                    <td><?php echo $produit['produit_id']; ?></td>
                                    <td><?php echo $produit['nom_produit']; ?></td>
                                    <td><?php echo $produit['nom_categorie']; ?></td>
                                    <td><?php echo $produit['nom_fournisseur']; ?></td>
                                    <td><?php echo $produit['total_mouvements']; ?></td>
                                    <td><?php echo $produit['total_entrees']; ?></td>
                                    <td><?php echo $produit['total_sorties']; ?></td>
                                    <td>
                                        <?php echo $produit['stock_actuel']; ?>
                                        <?php if($produit['stock_actuel'] <= 0): ?>
                                        <span class="badge badge-danger ml-1">Rupture</span>
                                        <?php elseif($produit['stock_actuel'] <= $produit['stock_min']): ?>
                                        <span class="badge badge-warning ml-1">Stock faible</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $produit['stock_min']; ?></td>
                                </tr>
                                <?php endforeach; ?>
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
    var produits = <?php echo json_encode($produits_actifs); ?>;
    
    // Préparer les données pour Chart.js
    var labels = [];
    var entreesData = [];
    var sortiesData = [];
    var totalData = [];
    
    produits.forEach(function(produit) {
        labels.push(produit.nom_produit);
        entreesData.push(produit.total_entrees);
        sortiesData.push(produit.total_sorties);
        totalData.push(produit.total_mouvements);
    });
    
    // Créer le graphique
    var ctx = document.getElementById('produitsBarChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Entrées',
                    data: entreesData,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Sorties',
                    data: sortiesData,
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
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
                        text: 'Produit'
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
