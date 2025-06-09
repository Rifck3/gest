<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-exclamation-triangle"></i> Produits en Stock Faible</h2>
        <a href="index.php?controleur=produit&action=index" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>
    
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
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Produits nécessitant un réapprovisionnement</h6>
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un produit...">
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="produitsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Fournisseur</th>
                            <th>Prix unitaire</th>
                            <th>Quantité</th>
                            <th>Stock Min</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($produits as $row): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['nom']; ?></td>
                                <td><?php echo $row['nom_categorie'] ?? 'Non défini'; ?></td>
                                <td><?php echo $row['nom_fournisseur'] ?? 'Non défini'; ?></td>
                                <td class="text-end"><?php echo number_format($row['prix_unitaire'], 2, ',', ' '); ?> Fr</td>
                                <td class="text-center"><?php echo $row['quantite']; ?></td>
                                <td class="text-center"><?php echo $row['quantite_min']; ?></td>
                                <td class="text-center">
                                    <?php if($row['quantite'] <= 0): ?>
                                        <span class="badge bg-danger">Rupture</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Stock faible</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="index.php?controleur=produit&action=voir&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="index.php?controleur=produit&action=modifier&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controleur=mouvement&action=ajouter&produit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success" title="Ajouter un mouvement">
                                            <i class="fas fa-exchange-alt"></i>
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

<script>
    // Recherche dans le tableau
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterTable();
    });
    
    document.getElementById('searchButton').addEventListener('click', function() {
        filterTable();
    });
    
    function filterTable() {
        var input, filter, table, tr, td, i, j, txtValue, found;
        input = document.getElementById('searchInput');
        filter = input.value.toUpperCase();
        table = document.getElementById('produitsTable');
        tr = table.getElementsByTagName('tr');
        
        for (i = 1; i < tr.length; i++) {
            found = false;
            td = tr[i].getElementsByTagName('td');
            
            for (j = 0; j < td.length - 1; j++) { // Exclure la colonne Actions
                if (td[j]) {
                    txtValue = td[j].textContent || td[j].innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            
            if (found) {
                tr[i].style.display = '';
            } else {
                tr[i].style.display = 'none';
            }
        }
    }
</script>

<?php require_once 'vues/includes/footer.php'; ?>
