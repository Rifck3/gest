<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rapport: Activité des fournisseurs</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <!-- Graphique d'activité des fournisseurs -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Fournisseurs les plus actifs</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="fournisseursBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphique de répartition des produits par fournisseur -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des produits par fournisseur</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="fournisseursPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tableau des fournisseurs les plus actifs -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails des fournisseurs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center align-middle">Fournisseur</th>
                                    <th class="text-center align-middle">Total mouvements</th>
                                    <th class="text-center align-middle">Entrées</th>
                                    <th class="text-center align-middle">Sorties</th>
                                    <th class="text-center align-middle">Nombre de produits</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($fournisseurs_stats)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Aucune activité trouvée</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach($fournisseurs_stats as $stat): ?>
                                <tr>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($stat['nom'] ?? ''); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($stat['total_mouvements'] ?? '0'); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($stat['total_entrees'] ?? '0'); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($stat['total_sorties'] ?? '0'); ?></td>
                                    <td class="text-center align-middle"><?php echo htmlspecialchars($stat['nombre_produits'] ?? '0'); ?></td>
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

<!-- Script pour les graphiques -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour les graphiques
    var fournisseurs = <?php echo json_encode($fournisseurs_stats); ?>;
    
    // Préparer les données pour le graphique en barres
    var labels = [];
    var entreesData = [];
    var mouvementsData = [];
    var produitsData = [];
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
    
    // Générer des couleurs supplémentaires si nécessaire
    function generateColors(count) {
        var colors = [];
        for(var i = 0; i < count; i++) {
            var r = Math.floor(Math.random() * 200);
            var g = Math.floor(Math.random() * 200);
            var b = Math.floor(Math.random() * 200);
            colors.push(`rgba(${r}, ${g}, ${b}, 0.8)`);
        }
        return colors;
    }
    
    // Trier les fournisseurs par nombre de mouvements
    fournisseurs.sort((a, b) => (b.total_mouvements || 0) - (a.total_mouvements || 0));
    
    fournisseurs.forEach(function(fournisseur) {
        labels.push(fournisseur.nom);
        entreesData.push(fournisseur.total_entrees || 0);
        mouvementsData.push(fournisseur.total_mouvements || 0);
        produitsData.push(fournisseur.nombre_produits || 0);
    });
    
    // Ajouter des couleurs supplémentaires si nécessaire
    if (fournisseurs.length > backgroundColors.length) {
        var additionalColors = generateColors(fournisseurs.length - backgroundColors.length);
        backgroundColors = backgroundColors.concat(additionalColors);
    }
    
    // Créer le graphique en barres
    var ctx1 = document.getElementById('fournisseursBarChart').getContext('2d');
    var myBarChart = new Chart(ctx1, {
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
                    label: 'Total mouvements',
                    data: mouvementsData,
                    backgroundColor: 'rgba(0, 123, 255, 0.8)',
                    borderColor: 'rgba(0, 123, 255, 1)',
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
                        text: 'Fournisseur'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
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
    
    // Créer le graphique en camembert
    var ctx2 = document.getElementById('fournisseursPieChart').getContext('2d');
    var myPieChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: produitsData,
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
                    labels: {
                        boxWidth: 15,
                        padding: 10,
                        font: {
                            size: 11
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var label = context.label || '';
                            var value = context.raw || 0;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} produits (${percentage}%)`;
                        }
                    }
                }
            },
        },
    });
});
</script>

<?php require_once 'vues/includes/footer.php'; ?>
