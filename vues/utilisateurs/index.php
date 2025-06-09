<?php
// Vérifier les permissions
if (!Permissions::aPermission('gestion_utilisateurs')) {
    header('Location: index.php');
    exit;
}
?>

<?php require_once 'vues/includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des utilisateurs</h1>
        <?php if (Permissions::estAdmin()): ?>
        <a href="index.php?controleur=utilisateur&action=creer" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel utilisateur
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom d'utilisateur</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($utilisateur['nom_utilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['nom_complet']); ?></td>
                            <td><?php echo htmlspecialchars($utilisateur['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $utilisateur['role'] === 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                    <?php echo ucfirst($utilisateur['role']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $utilisateur['actif'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $utilisateur['actif'] ? 'Actif' : 'Inactif'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if (Permissions::estAdmin()): ?>
                                <a href="index.php?controleur=utilisateur&action=modifier&id=<?php echo $utilisateur['id']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($utilisateur['id'] != $_SESSION['user_id']): ?>
                                <a href="index.php?controleur=utilisateur&action=supprimer&id=<?php echo $utilisateur['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                                <?php endif; ?>
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
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <span id="userName"></span> ?</p>
                <p class="text-danger">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" id="deleteLink" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Fonction pour confirmer la suppression
    function confirmerSuppression(id, nom) {
        document.getElementById('userName').textContent = nom;
        document.getElementById('deleteLink').href = 'index.php?controleur=utilisateur&action=supprimer&id=' + id;
        
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
        table = document.getElementById('utilisateursTable');
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
