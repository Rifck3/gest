<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Rapports</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sélectionnez un rapport</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Mouvements de stock</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rapport</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="index.php?controleur=rapport&action=mouvementsStock" class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-eye fa-sm"></i> Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                État du stock</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rapport</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="index.php?controleur=rapport&action=etatStock" class="btn btn-success btn-sm btn-block">
                                        <i class="fas fa-eye fa-sm"></i> Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Activité des produits</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rapport</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="index.php?controleur=rapport&action=activiteProduits" class="btn btn-info btn-sm btn-block">
                                        <i class="fas fa-eye fa-sm"></i> Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-lg-3 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Activité des fournisseurs</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rapport</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="index.php?controleur=rapport&action=activiteFournisseurs" class="btn btn-warning btn-sm btn-block">
                                        <i class="fas fa-eye fa-sm"></i> Voir le rapport
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vues/includes/footer.php'; ?>
