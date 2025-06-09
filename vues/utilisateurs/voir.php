<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Détails de l'utilisateur</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de l'utilisateur</h6>
                    <div class="dropdown no-arrow">
                        <a href="index.php?controleur=utilisateur&action=index" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Informations générales</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">ID</th>
                                    <td><?php echo $utilisateur['id']; ?></td>
                                </tr>
                                <tr>
                                    <th>Nom d'utilisateur</th>
                                    <td><?php echo $utilisateur['nom_utilisateur']; ?></td>
                                </tr>
                                <tr>
                                    <th>Nom complet</th>
                                    <td><?php echo $utilisateur['nom_complet']; ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td><?php echo $utilisateur['email']; ?></td>
                                </tr>
                                <tr>
                                    <th>Téléphone</th>
                                    <td><?php echo $utilisateur['telephone'] ?: '<span class="text-muted">Non spécifié</span>'; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Informations de compte</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 35%">Rôle</th>
                                    <td>
                                        <?php if($utilisateur['role'] == 'admin'): ?>
                                            <span class="badge bg-primary">Administrateur</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Utilisateur standard</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        <?php if($utilisateur['actif'] == 1): ?>
                                            <span class="badge bg-success">Actif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de création</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($utilisateur['date_creation'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Dernière modification</th>
                                    <td><?php echo date('d/m/Y H:i', strtotime($utilisateur['date_modification'])); ?></td>
                                </tr>
                                <tr>
                                    <th>Dernière connexion</th>
                                    <td>
                                        <?php if(!empty($utilisateur['derniere_connexion'])): ?>
                                            <?php echo date('d/m/Y H:i', strtotime($utilisateur['derniere_connexion'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Jamais connecté</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if(isset($activites) && !empty($activites)): ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="mb-3">Activités récentes</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($activites as $activite): ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($activite['date_creation'])); ?></td>
                                                <td><?php echo $activite['action']; ?></td>
                                                <td><?php echo $activite['description']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <a href="index.php?controleur=utilisateur&action=modifier&id=<?php echo $utilisateur['id']; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            
                            <?php if($utilisateur['id'] != $_SESSION['user_id']): ?>
                                <a href="javascript:void(0);" onclick="confirmerSuppression(<?php echo $utilisateur['id']; ?>, '<?php echo addslashes($utilisateur['nom_utilisateur']); ?>')" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                                
                                <a href="index.php?controleur=utilisateur&action=reinitialiserMotDePasse&id=<?php echo $utilisateur['id']; ?>" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur?');">
                                    <i class="fas fa-key"></i> Réinitialiser le mot de passe
                                </a>
                                
                                <?php if($utilisateur['actif'] == 1): ?>
                                    <a href="index.php?controleur=utilisateur&action=changerStatut&id=<?php echo $utilisateur['id']; ?>&statut=0" class="btn btn-secondary" onclick="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur?');">
                                        <i class="fas fa-user-slash"></i> Désactiver
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controleur=utilisateur&action=changerStatut&id=<?php echo $utilisateur['id']; ?>&statut=1" class="btn btn-success" onclick="return confirm('Êtes-vous sûr de vouloir activer cet utilisateur?');">
                                        <i class="fas fa-user-check"></i> Activer
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
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
</script>

<?php require_once 'vues/includes/footer.php'; ?>
