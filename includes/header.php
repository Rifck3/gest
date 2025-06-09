<?php
// Début du document HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar {
            background-color: #0d6efd !important;
        }
        .navbar-toggler {
            display: block !important;
            border: 2px solid white !important;
            padding: 8px !important;
            margin-right: 15px !important;
            background-color: transparent !important;
        }
        .navbar-toggler i {
            color: white !important;
            font-size: 24px !important;
        }
        @media (max-width: 768px) {
            .navbar-toggler {
                position: relative !important;
                z-index: 1050 !important;
            }
        }
        #mainContent {
            padding-top: 80px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-warehouse me-2"></i>
                Gestion de Stock
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i>
                            Accueil
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-warehouse me-1"></i>
                            Stock
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="index.php?controller=stock&action=index">
                                    <i class="fas fa-list me-1"></i>
                                    Mouvements
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=stock&action=add_movement">
                                    <i class="fas fa-plus me-1"></i>
                                    Ajouter un mouvement
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=stock&action=low_stock">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Stock faible
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="index.php?controller=stock&action=report">
                                    <i class="fas fa-chart-bar me-1"></i>
                                    Rapport
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Rechercher...">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div id="mainContent">
        <div class="container">
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                        switch($_GET['success']) {
                            case 1:
                                echo "Opération réussie !";
                                break;
                            case 2:
                                echo "Mise à jour réussie !";
                                break;
                            case 3:
                                echo "Suppression réussie !";
                                break;
                            case 4:
                                echo "Profil mis à jour avec succès !";
                                break;
                            default:
                                echo "Opération réussie !";
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                        switch($_GET['error']) {
                            case 1:
                                echo "Une erreur s'est produite. Veuillez réessayer.";
                                break;
                            case 2:
                                echo "Élément non trouvé.";
                                break;
                            case 3:
                                echo "Erreur lors de la mise à jour.";
                                break;
                            case 4:
                                echo "Erreur lors de la suppression.";
                                break;
                            case 5:
                                echo "Impossible de supprimer cet élément car il est utilisé ailleurs.";
                                break;
                            case 6:
                                echo "Vous ne pouvez pas supprimer votre propre compte.";
                                break;
                            case 7:
                                echo "Erreur lors de la suppression de l'utilisateur.";
                                break;
                            case 8:
                                echo "Utilisateur non trouvé.";
                                break;
                            case 9:
                                echo "Mot de passe actuel incorrect.";
                                break;
                            case 10:
                                echo "Erreur lors de la mise à jour du profil.";
                                break;
                            default:
                                echo "Une erreur s'est produite.";
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
