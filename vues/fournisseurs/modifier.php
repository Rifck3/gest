<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Modifier un fournisseur</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Détails du fournisseur</h6>
                    <div class="dropdown no-arrow">
                        <a href="index.php?controleur=fournisseur&action=index" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left fa-sm"></i> Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="index.php?controleur=fournisseur&action=mettreAJour" method="POST">
                        <input type="hidden" name="id" value="<?php echo $fournisseur['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom">Nom du fournisseur *</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $fournisseur['nom']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact">Personne de contact</label>
                                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $fournisseur['contact'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" value="<?php echo $fournisseur['telephone'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $fournisseur['email'] ?? ''; ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="adresse">Adresse</label>
                                <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo $fournisseur['adresse'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes">Notes (optionnel)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $fournisseur['notes'] ?? ''; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <a href="index.php?controleur=fournisseur&action=index" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vues/includes/footer.php'; ?>
