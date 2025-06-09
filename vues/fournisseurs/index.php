<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-truck"></i> Gestion des Fournisseurs</h2>
        <a href="index.php?controleur=fournisseur&action=creer" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un fournisseur
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
            <h6 class="m-0 font-weight-bold text-primary">Liste des fournisseurs</h6>
            <div class="input-group" style="width: 300px;">
                <input type="text" class="form-control" id="searchInput" placeholder="Rechercher un fournisseur...">
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="fournisseursTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Contact</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Adresse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($fournisseurs as $row): ?>
                            <tr data-id="<?php echo $row['id']; ?>">
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact'] ?? 'Non spécifié'); ?></td>
                                <td><?php echo htmlspecialchars($row['telephone'] ?? 'Non spécifié'); ?></td>
                                <td><?php echo htmlspecialchars($row['email'] ?? 'Non spécifié'); ?></td>
                                <td><?php echo htmlspecialchars($row['adresse'] ?? 'Non spécifié'); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="javascript:void(0);" onclick="modifierFournisseur(<?php echo $row['id']; ?>)" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="confirmerSuppression(<?php echo $row['id']; ?>, '<?php echo addslashes($row['nom']); ?>')" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le fournisseur <span id="fournisseurName"></span> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera définitivement ce fournisseur.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonction pour modifier un fournisseur
    function modifierFournisseur(id) {
        window.location.href = 'index.php?controleur=fournisseur&action=modifier&id=' + id;
    }

    // Fonction pour confirmer la suppression
    function confirmerSuppression(id, nom) {
        document.getElementById('fournisseurName').textContent = nom;
        document.getElementById('deleteLink').href = 'index.php?controleur=fournisseur&action=supprimer&id=' + id;
        
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
        table = document.getElementById('fournisseursTable');
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
