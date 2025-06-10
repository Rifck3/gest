document.addEventListener('DOMContentLoaded', function() {
    // Sélection des éléments
    const sidebarToggle = document.getElementById('sidebarToggleTop');
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.getElementById('content-wrapper');
    const html = document.documentElement;

    // Vérifier si les éléments existent
    if (!sidebarToggle || !sidebar || !contentWrapper) {
        console.error('Éléments manquants pour la barre latérale');
        return;
    }

    // Fonction pour basculer la barre latérale
    function toggleSidebar() {
        sidebar.classList.toggle('toggled');
        // Seulement sur desktop, on décale le contenu
        if (window.innerWidth >= 1024) {
            contentWrapper.classList.toggle('expanded');
        }
        // Sauvegarder l'état
        const isToggled = sidebar.classList.contains('toggled');
        localStorage.setItem('sidebarState', isToggled ? 'toggled' : 'expanded');
    }

    // Appliquer l'état au chargement
    const savedState = localStorage.getItem('sidebarState');
    if (savedState === 'toggled') {
        sidebar.classList.add('toggled');
        if (window.innerWidth >= 1024) {
            contentWrapper.classList.add('expanded');
        }
    } else {
        sidebar.classList.remove('toggled');
        contentWrapper.classList.remove('expanded');
    }

    // Synchroniser l'état CSS instantané
    if (html.classList.contains('sidebar-toggled-instant')) {
        html.classList.remove('sidebar-toggled-instant');
    }

    // Ajouter l'événement click au bouton
    sidebarToggle.addEventListener('click', function(e) {
        e.preventDefault();
        toggleSidebar();
    });

    // Gestion du swipe sur mobile
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeThreshold = 50;
        const swipeDistance = touchEndX - touchStartX;

        if (Math.abs(swipeDistance) > swipeThreshold) {
            if (swipeDistance > 0) {
                // Swipe droite
                if (sidebar.classList.contains('toggled')) {
                    toggleSidebar();
                }
            } else {
                // Swipe gauche
                if (!sidebar.classList.contains('toggled')) {
                    toggleSidebar();
                }
            }
        }
    }

    // Gérer le resize pour retirer le décalage sur mobile
    window.addEventListener('resize', function() {
        if (window.innerWidth < 1024) {
            contentWrapper.classList.remove('expanded');
        } else if (sidebar.classList.contains('toggled')) {
            contentWrapper.classList.add('expanded');
        }
    });

    // Debug : log pour vérifier l'exécution sur chaque page
    console.log('Sidebar JS chargé et prêt.');
});
