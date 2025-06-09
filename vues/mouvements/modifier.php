<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Modifier le mouvement de stock</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Modifier le mouvement</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?controleur=mouvement&action=mettreAJour">
                        <input type="hidden" name="id" value="<?php echo $mouvement['id']; ?>">
                        
                        <div class="form-group">
                            <label for="produit_id">Produit</label>
                            <select class="form-control" id="produit_id" name="produit_id" required>
                                <?php foreach($produits as $produit): ?>
                                <option value="<?php echo $produit['id']; ?>" <?php echo ($mouvement['produit_id'] == $produit['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($produit['nom']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="type_mouvement">Type de mouvement</label>
                            <select class="form-control" id="type_mouvement" name="type_mouvement" required>
                                <option value="entree" <?php echo ($mouvement['type_mouvement'] == 'entree') ? 'selected' : ''; ?>>Entrée</option>
                                <option value="sortie" <?php echo ($mouvement['type_mouvement'] == 'sortie') ? 'selected' : ''; ?>>Sortie</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantite">Quantité</label>
                            <input type="number" class="form-control" id="quantite" name="quantite" value="<?php echo htmlspecialchars($mouvement['quantite']); ?>" required min="1">
                        </div>
                        
                        <div class="form-group">
                            <label for="raison">Raison <span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="raison" name="raison" value="<?php echo htmlspecialchars($mouvement['raison']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="index.php?controleur=mouvement&action=index" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vues/includes/footer.php'; ?> 