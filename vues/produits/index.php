<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-boxes"></i> Gestion des Produits</h2>
        <a href="index.php?controleur=produit&action=creer" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un produit
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
            <h6 class="m-0 font-weight-bold text-primary">Liste des produits</h6>
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
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Fournisseur</th>
                            <th>Prix unitaire</th>
                            <th>Qté</th>
                            <th>Qté min</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $produits->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr data-id="<?php echo $row['id']; ?>">
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['nom_categorie'] ?? 'Non défini'); ?></td>
                                <td><?php echo htmlspecialchars($row['nom_fournisseur'] ?? 'Non défini'); ?></td>
                                <td class="text-end"><?php echo number_format($row['prix_unitaire'], 2, ',', ' '); ?> Fr</td>
                                <td class="text-center"><?php echo $row['quantite']; ?></td>
                                <td class="text-center"><?php echo $row['quantite_min']; ?></td>
                                <td class="text-center">
                                    <?php if($row['quantite'] <= 0): ?>
                                        <span class="badge bg-danger">Rupture</span>
                                    <?php elseif($row['quantite'] <= $row['quantite_min']): ?>
                                        <span class="badge bg-warning text-dark">Stock faible</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">En stock</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" onclick="voirProduit(<?php echo $row['id']; ?>)" class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="modifierProduit(<?php echo $row['id']; ?>)" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="confirmerSuppression(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nom']); ?>')" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="ajouterMouvement(<?php echo $row['id']; ?>)" class="btn btn-sm btn-success" title="Ajouter un mouvement">
                                            <i class="fas fa-exchange-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le produit <span id="productName"></span> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera définitivement ce produit.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonctions pour gérer les actions
    function voirProduit(id) {
        window.location.href = 'index.php?controleur=produit&action=voir&id=' + id;
    }

    function modifierProduit(id) {
        window.location.href = 'index.php?controleur=produit&action=modifier&id=' + id;
    }

    function ajouterMouvement(id) {
        window.location.href = 'index.php?controleur=mouvement&action=ajouter&produit_id=' + id;
    }

    // Fonction pour confirmer la suppression
    function confirmerSuppression(id, nom) {
        document.getElementById('productName').textContent = nom;
        document.getElementById('deleteLink').href = 'index.php?controleur=produit&action=supprimer&id=' + id;
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
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
