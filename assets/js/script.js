// Script personnalisé pour l'application de gestion de stock

// Fonction pour initialiser les tooltips Bootstrap
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Fonction pour initialiser les popovers Bootstrap
function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
}

// Fonction pour confirmer la suppression
function confirmDelete(event, message) {
    if (!confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément ?')) {
        event.preventDefault();
    }
}

// Fonction pour vérifier si la quantité de sortie est valide
function validateStockOut() {
    const form = document.getElementById('stock-movement-form');
    if (form) {
        form.addEventListener('submit', function(event) {
            const movementType = document.getElementById('movement_type').value;
            if (movementType === 'out') {
                const productId = document.getElementById('product_id').value;
                const quantity = parseInt(document.getElementById('quantity').value);
                const availableStock = parseInt(document.getElementById('product_' + productId + '_stock').value);
                
                if (quantity > availableStock) {
                    event.preventDefault();
                    alert('La quantité de sortie ne peut pas dépasser le stock disponible (' + availableStock + ').');
                }
            }
        });
    }
}

// Fonction pour mettre à jour dynamiquement le stock disponible dans le formulaire de mouvement
function updateAvailableStock() {
    const productSelect = document.getElementById('product_id');
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            const productId = this.value;
            const stockInfo = document.getElementById('stock-info');
            if (productId && stockInfo) {
                const availableStock = document.getElementById('product_' + productId + '_stock').value;
                stockInfo.textContent = 'Stock disponible: ' + availableStock;
            }
        });
    }
}

// Fonction pour filtrer les tableaux
function setupTableFilter() {
    const tableFilter = document.getElementById('table-filter');
    if (tableFilter) {
        tableFilter.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            const table = document.querySelector('.table-filterable');
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                let found = false;
                const cells = row.querySelectorAll('td');
                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().indexOf(searchText) > -1) {
                        found = true;
                    }
                });
                
                if (found) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// Initialisation lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
    initPopovers();
    validateStockOut();
    updateAvailableStock();
    setupTableFilter();
    
    // Ajouter des gestionnaires d'événements pour les liens de suppression
    const deleteLinks = document.querySelectorAll('.delete-link');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            confirmDelete(event, this.getAttribute('data-confirm-message'));
        });
    });
});
