<?php require_once 'vues/includes/header.php'; ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tableau de bord</h1>
        <a href="index.php?controleur=rapport&action=index" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Générer un rapport
        </a>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row g-4 align-items-stretch">
        
        <!-- Valeur Stock Card (large) -->
        <div class="col-xl-8 col-md-12 mb-4 d-flex">
            <div class="card dashboard-card success h-100 w-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1 d-flex align-items-center">
                            Valeur du stock
                            <span class="ms-2" data-bs-toggle="tooltip" title="Valeur totale des produits en stock">
                                <i class="fas fa-question-circle text-muted"></i>
                            </span>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo number_format($stats['valeur_stock'], 2, ',', ' '); ?> F CFA
                        </div>
                    </div>
                    <div class="icon-badge">
                        <i class="fas fa-money-bill"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="index.php?controleur=rapport&action=index&rapport_type=stock_status" class="text-success">
                        <small>Voir les détails <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
        <!-- Produits Card (avec badge stock faible) -->
        <div class="col-xl-4 col-md-12 mb-4 d-flex">
            <div class="card dashboard-card primary h-100 w-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Produits en stock
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo $stats['total_produits']; ?>
                            <span class="badge bg-danger ms-2" title="Rupture de stock">
                                <i class="fas fa-times-circle"></i> <?php echo $stats['produits_rupture_stock']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="icon-badge">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="index.php?controleur=produit&action=index" class="text-primary">
                        <small>Voir les détails <i class="fas fa-arrow-right"></i></small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu du tableau de bord -->
    <div class="row">
        <!-- Graphique des mouvements de stock -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Mouvements de stock (6 derniers mois)</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="stockMovementDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="stockMovementDropdown">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="index.php?controleur=rapport&action=mouvements">Voir le rapport détaillé</a>
                            <a class="dropdown-item" href="index.php?controleur=mouvement&action=ajouter">Ajouter un mouvement</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="index.php?controleur=mouvement&action=index">Tous les mouvements</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="stockMovementsChart"></canvas>
                    </div>
                </div>
                <div class="card-footer small text-muted pt-2 pb-2">
                    <div class="d-flex justify-content-center">
                        <span class="mr-2 me-3" style="font-size: 0.8rem;">
                            <i class="fas fa-circle text-primary"></i> Entrées
                        </span>
                        <span class="mr-2" style="font-size: 0.8rem;">
                            <i class="fas fa-circle text-danger"></i> Sorties
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits par catégorie -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Produits par catégorie</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in" aria-labelledby="categoryDropdown">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="index.php?controleur=categorie&action=index">Gérer les catégories</a>
                            <a class="dropdown-item" href="index.php?controleur=produit&action=index">Voir tous les produits</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-pie">
                        <canvas id="productsByCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Espace pour d'autres éléments si nécessaire -->
    <div class="row">
        <!-- Section supprimée: Produits en stock faible -->
    </div>
</div>
<!-- End of Page Content -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Débogage des données
    console.log('Données des mouvements:', <?php echo json_encode($donnees_graphiques['mouvements_6mois']); ?>);
    console.log('Données des catégories:', <?php echo json_encode($donnees_graphiques['produits_par_categorie']); ?>);

    // Définition des couleurs
    const primaryColor = '#4e73df';
    const successColor = '#1cc88a';
    const infoColor = '#36b9cc';
    const warningColor = '#f6c23e';
    const dangerColor = '#e74a3b';
    
    // Graphique des mouvements de stock
    if (document.getElementById('stockMovementsChart')) {
        const stockMovementsCtx = document.getElementById('stockMovementsChart').getContext('2d');
        
        // Tableau vide par défaut pour éviter les erreurs
        let moisLabels = [];
        let entreesData = [];
        let sortiesData = [];
        
        // Utiliser les données seulement si elles existent
        <?php if (!empty($donnees_graphiques['mouvements_6mois'])): ?>
            moisLabels = [<?php 
                $mois = [];
                foreach($donnees_graphiques['mouvements_6mois'] as $mois_data) {
                    $mois[] = "'" . $mois_data['mois'] . "'";
                }
                echo implode(', ', $mois);
            ?>];
            
            entreesData = [<?php 
                $entrees = [];
                foreach($donnees_graphiques['mouvements_6mois'] as $mois_data) {
                    $entrees[] = $mois_data['entrees'];
                }
                echo implode(', ', $entrees);
            ?>];
            
            sortiesData = [<?php 
                $sorties = [];
                foreach($donnees_graphiques['mouvements_6mois'] as $mois_data) {
                    $sorties[] = $mois_data['sorties'];
                }
                echo implode(', ', $sorties);
            ?>];
        <?php endif; ?>

        const stockMovementsChart = new Chart(stockMovementsCtx, {
            type: 'bar',
            data: {
                labels: moisLabels,
                datasets: [
                    {
                        label: 'Entrées',
                        data: entreesData,
                        backgroundColor: 'rgba(78, 115, 223, 0.8)',
                        borderColor: primaryColor,
                        borderWidth: 1,
                        maxBarThickness: 30,
                        borderRadius: 3
                    },
                    {
                        label: 'Sorties',
                        data: sortiesData,
                        backgroundColor: 'rgba(231, 74, 59, 0.8)',
                        borderColor: dangerColor,
                        borderWidth: 1,
                        maxBarThickness: 30,
                        borderRadius: 3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label = label + ': ';
                                }
                                // Afficher les valeurs comme des entiers sans décimale
                                label += Math.round(context.parsed.y);
                                return label;
                            }
                        },
                        titleFont: {
                            size: 10
                        },
                        bodyFont: {
                            size: 10
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 10
                            },
                            callback: function(value) {
                                if (Math.floor(value) === value) {
                                    return value;
                                }
                                return null;
                            }
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique des produits par catégorie
    if (document.getElementById('productsByCategoryChart')) {
        const productsByCategoryCtx = document.getElementById('productsByCategoryChart').getContext('2d');
        
        // Tableau vide par défaut pour éviter les erreurs
        let categoryLabels = [];
        let categoryData = [];
        
        // Utiliser les données seulement si elles existent
        <?php if (!empty($donnees_graphiques['produits_par_categorie'])): ?>
            categoryLabels = [<?php 
                $labels = [];
                foreach($donnees_graphiques['produits_par_categorie'] as $cat) {
                    $labels[] = "'" . $cat['nom'] . "'";
                }
                echo implode(', ', $labels);
            ?>];
            
            categoryData = [<?php 
                $data = [];
                foreach($donnees_graphiques['produits_par_categorie'] as $cat) {
                    $data[] = $cat['total'];
                }
                echo implode(', ', $data);
            ?>];
        <?php endif; ?>
        
        const productsByCategoryChart = new Chart(productsByCategoryCtx, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: [
                    primaryColor,
                    successColor,
                    infoColor,
                    warningColor,
                    dangerColor,
                    '#5a5c69',  // gris
                    '#2e59d9',  // bleu foncé
                    '#17a673',  // vert foncé
                    '#2c9faf',  // cyan foncé
                    '#e0a800',  // jaune foncé
                    '#e02d1b'   // rouge foncé
                ]
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    }
});
</script>

<style>
/* Style pour la timeline */
.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item-marker {
    position: absolute;
    left: -1.5rem;
    width: 1.5rem;
    height: 1.5rem;
    text-align: center;
}

.timeline-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 1.5rem;
    width: 1.5rem;
    background-color: #fff;
    border-radius: 100%;
    border: 0.125rem solid #dee2e6;
}

.timeline-item:not(:last-child):after {
    content: '';
    position: absolute;
    left: -0.84375rem;
    top: 1.5rem;
    bottom: 0;
    border-left: 0.125rem solid #dee2e6;
}

.chart-pie {
    position: relative;
    height: 15rem;
    width: 100%;
}

.opacity-50 {
    opacity: 0.5;
}
</style>

<?php require_once 'vues/includes/footer.php'; ?>
