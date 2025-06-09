<?php
// Vérifier les permissions
if (!Permissions::estAdmin()) {
    header('Location: index.php?controleur=utilisateur');
    exit;
}
?>

<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ajouter un utilisateur</h1>
    
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
                    <form action="index.php?controleur=utilisateur&action=creer" method="POST" id="createUserForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom_utilisateur">Nom d'utilisateur *</label>
                                <input type="text" class="form-control" id="nom_utilisateur" name="nom_utilisateur" required>
                                <small class="form-text text-muted">Le nom d'utilisateur doit contenir entre 4 et 20 caractères alphanumériques.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nom_complet">Nom complet *</label>
                                <input type="text" class="form-control" id="nom_complet" name="nom_complet" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mot_de_passe">Mot de passe *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirmer_mot_de_passe">Confirmer le mot de passe *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="role">Rôle *</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="gestionnaire">Gestionnaire</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="actif">Statut *</label>
                                <select class="form-select" id="actif" name="actif" required>
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="index.php?controleur=utilisateur&action=index" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        togglePasswordVisibility('mot_de_passe', 'togglePassword');
    });
    
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        togglePasswordVisibility('confirmer_mot_de_passe', 'toggleConfirmPassword');
    });
    
    function togglePasswordVisibility(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        
        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = '<i class="fas fa-eye-slash"></i>';
        } else {
            input.type = 'password';
            button.innerHTML = '<i class="fas fa-eye"></i>';
        }
    }
    
    // Form validation
    document.getElementById('createUserForm').addEventListener('submit', function(event) {
        const password = document.getElementById('mot_de_passe').value;
        const confirmPassword = document.getElementById('confirmer_mot_de_passe').value;
        
        if (password !== confirmPassword) {
            event.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }
        
        // Username validation
        const username = document.getElementById('nom_utilisateur').value;
        const usernameRegex = /^[a-zA-Z0-9]{4,20}$/;
        if (!usernameRegex.test(username)) {
            event.preventDefault();
            alert('Le nom d\'utilisateur doit contenir entre 4 et 20 caractères alphanumériques.');
            return false;
        }
    });
</script>

<?php require_once 'vues/includes/footer.php'; ?>
