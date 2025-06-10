<?php
// Assurez-vous qu'aucun contenu n'est envoyé avant les en-têtes HTTP
// Ce fichier ne doit contenir aucun espace ou retour à la ligne avant la balise PHP
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Gestion de Stock</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/variables.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/layout.css" rel="stylesheet">
    <link href="assets/css/components.css" rel="stylesheet">
    <link href="assets/css/utilities.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
    <link href="assets/css/notifications.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
    <script src="assets/js/sidebar.js" defer></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <script>
    try {
      if (localStorage.getItem('sidebarState') === 'toggled') {
        document.documentElement.classList.add('sidebar-toggled-instant');
      }
    } catch(e){}
    </script>
    <?php if(isset($_SESSION['user_id'])): ?>
    <?php require_once 'vues/includes/stats.php'; ?>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?controleur=tableau&action=index">
                <div class="sidebar-brand-icon">
                    <!-- Logo SVG personnalisé -->
                    <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:36px;height:36px;">
                      <rect x="4" y="20" width="12" height="24" rx="3" fill="#5c7cfa"/>
                      <rect x="18" y="8" width="12" height="36" rx="3" fill="#4263eb"/>
                      <rect x="32" y="28" width="12" height="16" rx="3" fill="#a5b4fc"/>
                      <circle cx="24" cy="24" r="6" fill="#fff" stroke="#5c7cfa" stroke-width="2"/>
                    </svg>
                </div>
                <div class="sidebar-brand-text mx-3">Gestion de Stock</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=tableau&action=index">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading px-3 py-2 text-white-50 text-uppercase text-xs">
                    Inventaire
                </div>

                <!-- Nav Item - Products -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=produit&action=index">
                        <i class="fas fa-fw fa-box"></i>
                        <span>Produits</span>
                    </a>
                </li>

                <!-- Nav Item - Categories -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=categorie&action=index">
                        <i class="fas fa-fw fa-list"></i>
                        <span>Catégories</span>
                    </a>
                </li>

                <!-- Nav Item - Suppliers -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=fournisseur&action=index">
                        <i class="fas fa-fw fa-truck"></i>
                        <span>Fournisseurs</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading px-3 py-2 text-white-50 text-uppercase text-xs">
                    Opérations
                </div>

                <!-- Nav Item - Stock Movements -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=mouvement&action=index">
                        <i class="fas fa-fw fa-exchange-alt"></i>
                        <span>Mouvements de stock</span>
                    </a>
                </li>

                <!-- Nav Item - Commandes -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=commande&action=index">
                        <i class="fas fa-fw fa-shopping-cart"></i>
                        <span>Commandes</span>
                    </a>
                </li>

                <!-- Nav Item - Reports -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=rapport&action=index">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Rapports</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=chatbot&action=index">
                        <i class="fas fa-robot"></i> Assistant
                    </a>
                </li>

                <?php if($_SESSION['role'] === 'admin'): ?>
                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading px-3 py-2 text-white-50 text-uppercase text-xs">
                    Administration
                </div>

                <!-- Nav Item - Users -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php?controleur=utilisateur&action=index">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="border-radius: 0 0 16px 16px;">
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto align-items-center">
                        <!-- Sidebar Toggle (Topbar) -->
                        <li class="nav-item">
                            <button id="sidebarToggleTop" type="button" class="btn btn-link rounded-circle mr-3">
                                <i class="fa fa-bars"></i>
                            </button>
                        </li>
                        <!-- Badge Fournisseurs -->
                        <li class="nav-item d-flex align-items-center flex-wrap">
                            <a class="nav-link position-relative" href="index.php?controleur=fournisseur&action=index">
                                <i class="fas fa-truck"></i>
                                <span class="badge bg-primary ms-1 d-none d-md-inline">Fournisseurs: <?php echo $stats['total_fournisseurs'] ?? 0; ?></span>
                                <span class="badge bg-primary ms-1 d-inline d-md-none" title="Fournisseurs">F</span>
                            </a>
                        </li>
                        <!-- Badge Produits en rupture de stock -->
                        <li class="nav-item d-flex align-items-center flex-wrap">
                            <a class="nav-link position-relative" href="index.php?controleur=produit&action=ruptureStock">
                                <i class="fas fa-times-circle text-danger"></i>
                                <span class="badge bg-danger ms-1 d-none d-md-inline">Rupture: <?php echo $stats['produits_rupture_stock'] ?? 0; ?></span>
                                <span class="badge bg-danger ms-1 d-inline d-md-none" title="Rupture">R</span>
                            </a>
                        </li>
                        <!-- Badge Stock faible -->
                        <li class="nav-item d-flex align-items-center flex-wrap">
                            <a class="nav-link position-relative" href="index.php?controleur=produit&action=stockFaible">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <span class="badge bg-warning ms-1 d-none d-md-inline">Stock faible: <?php echo $stats['produits_stock_faible'] ?? 0; ?></span>
                                <span class="badge bg-warning ms-1 d-inline d-md-none" title="Stock faible">S</span>
                            </a>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img class="img-profile rounded-circle me-2" src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Utilisateur'); ?>&background=5c7cfa&color=fff&size=48" style="width:36px;height:36px;object-fit:cover;">
                                <span class="d-none d-lg-inline text-gray-600 small fw-bold"> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Utilisateur'); ?> </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="index.php?controleur=utilisateur&action=profil"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?controleur=auth&action=deconnexion"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i> Déconnexion</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
    <?php else: ?>
    <!-- Login Page Wrapper -->
    <div class="container mt-5">
    <?php endif; ?>
