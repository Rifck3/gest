<?php
// Vérifier les permissions
if (!Permissions::estAdmin()) {
    header('Location: index.php?controleur=utilisateur');
    exit;
}

// Vérifier si l'utilisateur existe
if (!isset($utilisateur)) {
    header('Location: index.php?controleur=utilisateur');
    exit;
}
?>

<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Modifier un utilisateur</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Détails de l'utilisateur</h6>
                    <div class="dropdown no-arrow">
                        <a href="index.php?controleur=utilisateur&action=index" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="index.php?controleur=utilisateur&action=modifier" method="POST">
                        <input type="hidden" name="id" value="<?php echo $utilisateur['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom_utilisateur">Nom d'utilisateur *</label>
                                <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" value="<?php echo htmlspecialchars($utilisateur['nom_utilisateur']); ?>" required>
                                <small class="form-text text-muted">Le nom d'utilisateur doit contenir entre 4 et 20 caractères alphanumériques.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom_complet">Nom complet *</label>
                                <input type="text" class="form-control" id="nom_complet" name="nom_complet" value="<?php echo htmlspecialchars($utilisateur['nom_complet']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($utilisateur['email']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $utilisateur['telephone'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mot_de_passe">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role">Rôle *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="gestionnaire" <?php echo $utilisateur['role'] === 'gestionnaire' ? 'selected' : ''; ?>>Gestionnaire</option>
                                    <option value="admin" <?php echo $utilisateur['role'] === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="actif">Statut *</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" 
                                           <?php echo $utilisateur['actif'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="actif">
                                        Compte actif
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <a href="index.php?controleur=utilisateur&action=index" class="btn btn-secondary">Annuler</a>
                                <?php if ($utilisateur['id'] == $_SESSION['user_id']): ?>
                                    <a href="index.php?controleur=utilisateur&action=changerMotDePasse" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Changer mon mot de passe
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?controleur=utilisateur&action=reinitialiserMotDePasse&id=<?php echo $utilisateur['id']; ?>" class="btn btn-warning" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser le mot de passe de cet utilisateur?');">
                                        <i class="fas fa-key"></i> Réinitialiser le mot de passe
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation
    document.getElementById('updateUserForm').addEventListener('submit', function(event) {
        // Username validation
        const username = document.getElementById('nom_utilisateur').value;
        const usernameRegex = /^[a-zA-Z0-9]{4,20}$/;
        if (!usernameRegex.test(username)) {
            event.preventDefault();
            alert('Le nom d\'utilisateur doit contenir entre 4 et 20 caractères alphanumériques.');
            return false;
        }
        
        // Email validation
        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            event.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
            return false;
        }
    });
</script>

<?php require_once 'vues/includes/footer.php'; ?>
