<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rapport: État du stock</h1>
    
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
                        <input type="hidden" name="action" value="etatStock">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
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
                            <div class="col-md-4 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="stock_faible" name="stock_faible" value="1" <?php echo (isset($_GET['stock_faible'])) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="stock_faible">
                                        Afficher uniquement les produits en stock faible
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <button type="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-filter fa-sm"></i> Filtrer
                                </button>
                                <a href="index.php?controleur=rapport&action=etatStock" class="btn btn-secondary mr-2">
                                    <i class="fas fa-sync-alt fa-sm"></i> Réinitialiser
                                </a>
                                <a href="index.php?controleur=rapport&action=exporterCSV&type_rapport=etat_stock<?php echo isset($_GET['categorie_id']) ? '&categorie_id='.$_GET['categorie_id'] : ''; ?><?php echo isset($_GET['fournisseur_id']) ? '&fournisseur_id='.$_GET['fournisseur_id'] : ''; ?><?php echo isset($_GET['stock_faible']) ? '&stock_faible='.$_GET['stock_faible'] : ''; ?>" class="btn btn-success">
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
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Nombre de produits</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nombre_produits; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Valeur totale du stock</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($valeur_totale, 2, ',', ' '); ?> Fr</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Produits en stock faible</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nombre_stock_faible; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Produits en rupture</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php 
                                                $rupture = 0;
                                                foreach($produits as $produit) {
                                                    if($produit['quantite'] <= 0) {
                                                        $rupture++;
                                                    }
                                                }
                                                echo $rupture;
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique de répartition par catégorie -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Répartition par catégorie</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie">
                                        <canvas id="categoriesPieChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Valeur du stock par catégorie</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="categoriesBarChart"></canvas>
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
                    <h6 class="m-0 font-weight-bold text-primary">Liste des produits</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Catégorie</th>
                                    <th>Fournisseur</th>
                                    <th>Prix unitaire</th>
                                    <th>Qté</th>
                                    <th>Qté min</th>
                                    <th>Valeur</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($produits as $produit): ?>
                                <tr data-id="<?php echo $produit['id']; ?>">
                                    <td><?php echo $produit['id']; ?></td>
                                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($produit['nom_categorie'] ?? 'Non défini'); ?></td>
                                    <td><?php echo htmlspecialchars($produit['nom_fournisseur'] ?? 'Non défini'); ?></td>
                                    <td class="text-end"><?php echo number_format($produit['prix_unitaire'], 2, ',', ' '); ?> Fr</td>
                                    <td class="text-center"><?php echo $produit['quantite']; ?></td>
                                    <td class="text-center"><?php echo $produit['quantite_min']; ?></td>
                                    <td class="text-end"><?php echo number_format($produit['quantite'] * $produit['prix_unitaire'], 2, ',', ' '); ?> Fr</td>
                                    <td class="text-center">
                                        <?php if($produit['quantite'] <= 0): ?>
                                            <span class="badge bg-danger">Rupture</span>
                                        <?php elseif($produit['quantite'] <= $produit['quantite_min']): ?>
                                            <span class="badge bg-warning text-dark">Stock faible</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">En stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="index.php?controleur=produit&action=voir&id=<?php echo $produit['id']; ?>" class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="index.php?controleur=produit&action=modifier&id=<?php echo $produit['id']; ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
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

<!-- Script pour les graphiques -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique en camembert
    var donnees = <?php echo json_encode($produits_par_categorie); ?>;
    
    // Préparer les données pour Chart.js
    var labels = [];
    var data = [];
    var backgroundColors = [
        'rgba(78, 115, 223, 0.8)',
        'rgba(28, 200, 138, 0.8)',
        'rgba(54, 185, 204, 0.8)',
        'rgba(246, 194, 62, 0.8)',
        'rgba(231, 74, 59, 0.8)',
        'rgba(133, 135, 150, 0.8)',
        'rgba(105, 153, 255, 0.8)',
        'rgba(255, 105, 180, 0.8)',
        'rgba(50, 205, 50, 0.8)',
        'rgba(255, 165, 0, 0.8)'
    ];
    
    var valeurs = [];
    
    donnees.forEach(function(item, index) {
        labels.push(item.nom);
        data.push(item.total);
        valeurs.push(item.valeur);
    });
    
    // Créer le graphique en camembert
    var ctx1 = document.getElementById('categoriesPieChart').getContext('2d');
    var myPieChart = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                hoverBackgroundColor: backgroundColors.map(color => color.replace('0.8', '1')),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            return label + ': ' + value + ' produits';
                        }
                    }
                }
            },
        },
    });
    
    // Créer le graphique en barres
    var ctx2 = document.getElementById('categoriesBarChart').getContext('2d');
    var myBarChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Valeur (Fr)",
                backgroundColor: backgroundColors,
                hoverBackgroundColor: backgroundColors.map(color => color.replace('0.8', '1')),
                borderColor: "#4e73df",
                data: valeurs,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Valeur (Fr)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Catégorie'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            var value = context.raw || 0;
                            return label + ': ' + new Intl.NumberFormat('fr-FR').format(value) + ' Fr';
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require_once 'vues/includes/footer.php'; ?>
