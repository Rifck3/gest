<?php
// Vérification des permissions
if (!isset($_SESSION['user_id']) || !Permissions::aPermission('creer_commandes')) {
    header('Location: index.php?controleur=tableau&action=index');
    exit();
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouvelle commande</h1>
        <div>
            <a href="index.php?controleur=commande&action=index" class="btn btn-secondary" data-toggle="tooltip" title="Retourner à la liste des commandes">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <button type="submit" form="formCommande" class="btn btn-primary" data-toggle="tooltip" title="Enregistrer la commande">
                <i class="fas fa-save"></i> Enregistrer
            </button>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de la commande</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?controleur=commande&action=enregistrer" id="formCommande">
                <!-- Informations générales -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fournisseur_id">Fournisseur *</label>
                            <select name="fournisseur_id" id="fournisseur_id" class="form-control" required>
                                <option value="">Sélectionnez un fournisseur</option>
                                <?php foreach ($fournisseurs as $fournisseur): ?>
                                    <option value="<?php echo $fournisseur['id']; ?>">
                                        <?php echo htmlspecialchars($fournisseur['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="commentaire">Commentaire</label>
                            <textarea name="commentaire" id="commentaire" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Liste des produits -->
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Produits</h6>
                        <button type="button" class="btn btn-primary btn-sm" id="ajouterProduit" data-toggle="tooltip" title="Ajouter un produit à la commande">
                            <i class="fas fa-plus"></i> Ajouter un produit
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tableProduits">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                        <th>Total</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Les lignes de produits seront ajoutées ici dynamiquement -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                                        <td colspan="2"><span id="totalCommande">0.00</span> Fr</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Template pour une ligne de produit -->
<template id="templateLigneProduit">
    <tr>
        <td>
            <select name="produits[INDEX][id]" class="form-control select-produit" required>
                <option value="">Sélectionnez un produit</option>
                <?php foreach ($produits as $produit): ?>
                    <option value="<?php echo $produit['id']; ?>" 
                            data-prix="<?php echo $produit['prix_unitaire']; ?>">
                        <?php echo htmlspecialchars($produit['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <input type="number" name="produits[INDEX][quantite]" class="form-control quantite" min="1" value="1" required>
        </td>
        <td>
            <input type="number" name="produits[INDEX][prix_unitaire]" class="form-control prix-unitaire" step="0.01" min="0" required>
        </td>
        <td class="text-end">
            <span class="total-ligne">0.00</span> Fr
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm supprimer-ligne" data-toggle="tooltip" title="Supprimer ce produit">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
$(document).ready(function() {
    let indexLigne = 0;

    // Initialiser les tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Fonction pour ajouter une ligne de produit
    function ajouterLigne() {
        const template = document.getElementById('templateLigneProduit');
        const clone = template.content.cloneNode(true);
        
        // Remplacer INDEX par l'index actuel
        clone.querySelectorAll('[name*="INDEX"]').forEach(element => {
            element.name = element.name.replace('INDEX', indexLigne);
        });
        
        document.querySelector('#tableProduits tbody').appendChild(clone);
        indexLigne++;
        
        // Initialiser les événements pour la nouvelle ligne
        initialiserLigne($('#tableProduits tbody tr:last'));
    }

    // Fonction pour initialiser les événements d'une ligne
    function initialiserLigne($ligne) {
        const $select = $ligne.find('.select-produit');
        const $quantite = $ligne.find('.quantite');
        const $prixUnitaire = $ligne.find('.prix-unitaire');
        
        // Mettre à jour le prix unitaire lors de la sélection du produit
        $select.on('change', function() {
            const prix = $(this).find(':selected').data('prix');
            $prixUnitaire.val(prix);
            calculerTotalLigne($ligne);
        });
        
        // Recalculer lors du changement de quantité ou de prix
        $quantite.on('input', function() {
            calculerTotalLigne($ligne);
        });
        
        $prixUnitaire.on('input', function() {
            calculerTotalLigne($ligne);
        });
        
        // Supprimer la ligne
        $ligne.find('.supprimer-ligne').on('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')) {
                $ligne.remove();
                calculerTotalCommande();
            }
        });
    }

    // Fonction pour calculer le total d'une ligne
    function calculerTotalLigne($ligne) {
        const quantite = parseFloat($ligne.find('.quantite').val()) || 0;
        const prixUnitaire = parseFloat($ligne.find('.prix-unitaire').val()) || 0;
        const total = quantite * prixUnitaire;
        
        $ligne.find('.total-ligne').text(total.toFixed(2));
        calculerTotalCommande();
    }

    // Fonction pour calculer le total de la commande
    function calculerTotalCommande() {
        let total = 0;
        $('.total-ligne').each(function() {
            total += parseFloat($(this).text()) || 0;
        });
        $('#totalCommande').text(total.toFixed(2));
    }

    // Ajouter une ligne lors du clic sur le bouton
    $('#ajouterProduit').on('click', function() {
        ajouterLigne();
    });

    // Validation du formulaire avant soumission
    $('#formCommande').on('submit', function(e) {
        if ($('#tableProduits tbody tr').length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un produit à la commande.');
            return false;
        }
        return true;
    });
});
</script> 