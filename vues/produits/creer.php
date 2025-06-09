<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-plus-circle"></i> Créer un nouveau produit</h2>
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
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations du produit</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?controleur=produit&action=enregistrer">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom du produit</label>
                        <input type="text" class="form-control" id="nom" name="nom" 
                               value="<?php echo isset($_SESSION['form_data']['nom']) ? htmlspecialchars($_SESSION['form_data']['nom']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="reference">Référence du produit <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="reference" name="reference" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="categorie_id" class="form-label">Catégorie</label>
                        <select class="form-control" id="categorie_id" name="categorie_id" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <?php foreach($categories as $categorie): ?>
                                <option value="<?php echo $categorie['id']; ?>" 
                                    <?php echo (isset($_SESSION['form_data']['categorie_id']) && $_SESSION['form_data']['categorie_id'] == $categorie['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categorie['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="fournisseur_id" class="form-label">Fournisseur</label>
                        <select class="form-control" id="fournisseur_id" name="fournisseur_id" required>
                            <option value="">Sélectionnez un fournisseur</option>
                            <?php foreach($fournisseurs as $fournisseur): ?>
                                <option value="<?php echo $fournisseur['id']; ?>" 
                                    <?php echo (isset($_SESSION['form_data']['fournisseur_id']) && $_SESSION['form_data']['fournisseur_id'] == $fournisseur['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($fournisseur['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="prix_unitaire" class="form-label">Prix unitaire (Fr)</label>
                        <input type="number" class="form-control" id="prix_unitaire" name="prix_unitaire" 
                               value="<?php echo isset($_SESSION['form_data']['prix_unitaire']) ? htmlspecialchars($_SESSION['form_data']['prix_unitaire']) : ''; ?>" 
                               step="0.01" min="0" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="quantite" class="form-label">Quantité initiale</label>
                        <input type="number" class="form-control" id="quantite" name="quantite" 
                               value="<?php echo isset($_SESSION['form_data']['quantite']) ? htmlspecialchars($_SESSION['form_data']['quantite']) : ''; ?>" 
                               min="0" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="quantite_min" class="form-label">Quantité minimale d'alerte</label>
                        <input type="number" class="form-control" id="quantite_min" name="quantite_min" 
                               value="<?php echo isset($_SESSION['form_data']['quantite_min']) ? htmlspecialchars($_SESSION['form_data']['quantite_min']) : ''; ?>" 
                               min="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="raison">Raison de l'entrée en stock <span style="color:red">*</span></label>
                    <input type="text" class="form-control" id="raison" name="raison" required>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Enregistrer le produit</button>
                        <a href="index.php?controleur=produit&action=index" class="btn btn-secondary">Annuler</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Ajouter des validations supplémentaires si nécessaire
</script>

<?php 
// Nettoyer les données du formulaire en session après affichage
unset($_SESSION['form_data']);
?>

<?php require_once 'vues/includes/footer.php'; ?>