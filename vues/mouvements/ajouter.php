<?php require_once 'vues/includes/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ajouter un mouvement de stock</h1>
    
    <?php require_once 'vues/includes/alerts.php'; ?>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Détails du mouvement</h6>
                </div>
                <div class="card-body">
                    <form action="index.php?controleur=mouvement&action=enregistrer" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_mouvement">Type de mouvement</label>
                                <select class="form-control" id="type_mouvement" name="type_mouvement" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="entree">Entrée</option>
                                    <option value="sortie">Sortie</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="produit_id">Produit</label>
                                <select class="form-control" id="produit_id" name="produit_id" required>
                                    <option value="">Sélectionner un produit</option>
                                    <?php foreach($produits as $produit_item): ?>
                                    <option value="<?php echo $produit_item['id']; ?>" data-stock="<?php echo $produit_item['quantite']; ?>">
                                        <?php echo $produit_item['nom']; ?> (Stock: <?php echo $produit_item['quantite']; ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="stock_actuel" class="form-text text-muted"></small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantite">Quantité</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
                                <div id="error_quantite" class="invalid-feedback">
                                    La quantité doit être supérieure à 0 et ne peut pas dépasser le stock actuel pour les sorties.
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference">Référence (optionnel)</label>
                                <input type="text" class="form-control" id="reference" name="reference">
                                <small class="form-text text-muted">Par exemple: numéro de commande, bon de livraison, etc.</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="raison">Raison <span style="color:red">*</span></label>
                                <input type="text" class="form-control" id="raison" name="raison" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <label for="notes">Notes (optionnel)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                <a href="index.php?controleur=mouvement&action=index" class="btn btn-secondary">Annuler</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du formulaire
    const typeMouvement = document.getElementById('type_mouvement');
    const produitId = document.getElementById('produit_id');
    const quantite = document.getElementById('quantite');
    const stockActuel = document.getElementById('stock_actuel');
    const errorQuantite = document.getElementById('error_quantite');
    const fournisseurSection = document.querySelector('.fournisseur-section');
    const raisonSection = document.querySelector('.raison-section');
    
    // Afficher la section fournisseur seulement pour les entrées
    typeMouvement.addEventListener('change', function() {
        if(this.value === 'entree') {
            fournisseurSection.style.display = 'flex';
            raisonSection.style.display = 'none';
        } else if(this.value === 'sortie') {
            fournisseurSection.style.display = 'none';
            raisonSection.style.display = 'flex';
        } else {
            fournisseurSection.style.display = 'none';
            raisonSection.style.display = 'none';
        }
    });
    
    // Afficher le stock actuel lors de la sélection d'un produit
    produitId.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if(option.value) {
            const stockDisponible = option.getAttribute('data-stock');
            stockActuel.textContent = `Stock actuel: ${stockDisponible} unités`;
        } else {
            stockActuel.textContent = '';
        }
    });
    
    // Vérifier la quantité pour les sorties
    quantite.addEventListener('change', function() {
        if(typeMouvement.value === 'sortie' && produitId.value) {
            const option = produitId.options[produitId.selectedIndex];
            const stockDisponible = parseInt(option.getAttribute('data-stock'));
            const quantiteValeur = parseInt(this.value);
            
            if(quantiteValeur > stockDisponible) {
                this.classList.add('is-invalid');
                errorQuantite.style.display = 'block';
            } else {
                this.classList.remove('is-invalid');
                errorQuantite.style.display = 'none';
            }
        } else {
            this.classList.remove('is-invalid');
            errorQuantite.style.display = 'none';
        }
    });
});
</script>

<?php require_once 'vues/includes/footer.php'; ?>
