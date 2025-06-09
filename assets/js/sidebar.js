document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content');

    // Fonction pour gérer le toggle du menu
    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        content.classList.toggle('expanded');
        
        // Sauvegarder l'état du menu
        localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'collapsed' : 'expanded');
    }

    // Restaurer l'état du menu au chargement
    const savedState = localStorage.getItem('sidebarState');
    if (savedState === 'collapsed') {
        sidebar.classList.add('collapsed');
        content.classList.add('expanded');
    }

    // Écouteur d'événement pour le bouton toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Gestion du swipe sur mobile
    let touchStartX = 0;
    let isSwiping = false;

    document.addEventListener('touchstart', (e) => {
        touchStartX = e.touches[0].clientX;
        isSwiping = true;
    }, { passive: true });

    document.addEventListener('touchmove', (e) => {
        if (!isSwiping) return;
        
        const currentX = e.touches[0].clientX;
        const diff = currentX - touchStartX;
        
        if (Math.abs(diff) > 50) { // Augmenté le seuil pour éviter les faux positifs
            if (diff > 0 && sidebar.classList.contains('collapsed')) {
                // Swipe droite quand le menu est fermé
                toggleSidebar();
            } else if (diff < 0 && !sidebar.classList.contains('collapsed')) {
                // Swipe gauche quand le menu est ouvert
                toggleSidebar();
            }
            isSwiping = false;
        }
    }, { passive: true });

    document.addEventListener('touchend', () => {
        isSwiping = false;
    }, { passive: true });
});

    // Écouteur d'événement pour le bouton toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }

    // Gestion du swipe sur mobile
    document.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    });

    function handleSwipe() {
        const swipeDistance = touchEndX - touchStartX;
        const threshold = 100; // Distance minimale pour un swipe

        if (Math.abs(swipeDistance) >= threshold) {
            if (swipeDistance > 0 && sidebar.classList.contains('collapsed')) {
                // Swipe vers la droite -> ouvrir le menu
                toggleSidebar();
            } else if (swipeDistance < 0 && !sidebar.classList.contains('collapsed')) {
                // Swipe vers la gauche -> fermer le menu
                toggleSidebar();
            }
        }
    }

    // Gestion responsive
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
        } else {
            sidebar.classList.remove('collapsed');
            content.classList.remove('expanded');
        }
    }

    // Écouter les changements de taille d'écran
    window.addEventListener('resize', handleResize);

    // Vérifier la taille d'écran au chargement
    handleResize();
});
