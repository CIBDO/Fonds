<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul class="menu-list">
                <li class="menu-title">
                    <span>Menu Principal</span>
                </li>

                <!-- Tableau de Bord -->
                @if (Auth::user()->hasRole('admin'))
                    <li class="menu-item {{ request()->routeIs('dashboard.admin') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.admin') }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>
                @elseif (Auth::user()->hasRole('tresorier'))
                    <li class="menu-item {{ request()->routeIs('dashboard.tresorier') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.tresorier') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>
                @elseif (Auth::user()->hasRole('acct'))
                    <li class="menu-item {{ request()->routeIs('dashboard.acct') ? 'active' : '' }}">
                        <a href="{{ route('dashboard.acct') }}">
                            <i class="fas fa-calculator"></i>
                            <span>Tableau de Bord</span>
                        </a>
                    </li>
                @endif

                                <!-- Demandes de Fonds -->
                @if (Auth::user()->hasAnyRole(['tresorier', 'admin']))
                    <li class="submenu {{ request()->routeIs('demandes-fonds.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-coins"></i>
                            <span>Demandes de Fonds</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('demandes-fonds.create') }}" class="{{ request()->routeIs('demandes-fonds.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i>Faire une Demande de Fonds</a></li>
                            <li><a href="{{ route('demandes-fonds.index') }}" class="{{ request()->routeIs('demandes-fonds.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Liste des Demandes de Fonds</a></li>
                            <li><a href="{{ route('demandes-fonds.situation') }}" class="{{ request()->routeIs('demandes-fonds.situation') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i>Situation des Demandes</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Envoi de Fonds -->
                @if (Auth::user()->hasAnyRole(['acct', 'admin']))
                    <li class="submenu {{ request()->routeIs('demandes-fonds.envois') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-paper-plane"></i>
                            <span>Envoi de Fonds</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('demandes-fonds.index') }}" class="{{ request()->routeIs('demandes-fonds.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Liste des Demandes de Fonds</a></li>
                            <li><a href="{{ route('demandes-fonds.envois') }}" class="{{ request()->routeIs('demandes-fonds.envois') ? 'active' : '' }}">
                                <i class="fas fa-send"></i>Envoyer des Fonds</a></li>
                            <li><a href="{{ route('demandes-fonds.situation') }}" class="{{ request()->routeIs('demandes-fonds.situation') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie"></i>Situation des Demandes</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Impression et Situation Mensuelle -->
                @if (Auth::user()->hasAnyRole(['admin', 'acct', 'superviseur']))
                    <li class="submenu {{ request()->routeIs('demandes-fonds.situation-mensuelle') || request()->routeIs('demandes-fonds.etat-avant-envoi') || request()->routeIs('demandes-fonds.etat-detaille-avant-envoi') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-print"></i>
                            <span>Impression</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('demandes-fonds.situation-mensuelle') }}" class="{{ request()->routeIs('demandes-fonds.situation-mensuelle') ? 'active' : '' }}">
                                <i class="fas fa-file-alt"></i>Situation Mensuelle</a></li>
                            <li><a href="{{ route('demandes-fonds.etat-avant-envoi') }}" class="{{ request()->routeIs('demandes-fonds.etat-avant-envoi') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice"></i>Situation Avant Envoi</a></li>
                            <li><a href="{{ route('demandes-fonds.etat-detaille-avant-envoi') }}" class="{{ request()->routeIs('demandes-fonds.etat-detaille-avant-envoi') ? 'active' : '' }}">
                                <i class="fas fa-file-excel"></i>État Détaillé Avant Envoi</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Rapports Statistiques -->
                @if (Auth::user()->hasAnyRole(['admin', 'acct']))
                    <li class="submenu">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-chart-area"></i>
                            <span>Rapports & Statistiques</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('demandes-fonds.situationDF') }}">
                                <i class="fas fa-file-alt"></i>Rapport Mensuel DF</a></li>
                            <li><a href="{{ route('demandes-fonds.situationFE') }}">
                                <i class="fas fa-file-invoice"></i>Rapport Mensuel FE</a></li>
                            <li><a href="{{ route('demandes-fonds.recap') }}">
                                <i class="fas fa-table"></i>Tableau Détaillé DF</a></li>
                            <li><a href="{{ route('demandes-fonds.detail') }}">
                                <i class="fas fa-globe"></i>Situation Globale DF</a></li>
                            <li><a href="{{ route('demandes-fonds.fonctionnaires') }}">
                                <i class="fas fa-users"></i>Situation Personnel</a></li>
                            <li><a href="{{ route('demandes-fonds.totaux-par-mois') }}">
                                <i class="fas fa-calendar-alt"></i>Demandes par Mois</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Messagerie -->
                <li class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <a href="{{ route('messages.index') }}">
                        <i class="fas fa-envelope"></i>
                        <span>Messagerie</span>
                    </a>
                </li>

                <li class="menu-title">
                    <span>Management</span>
                </li>

                <!-- Gestion des Comptes -->
                @if (Auth::user()->hasAnyRole(['admin']))
                    <li class="submenu {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-users-cog"></i>
                            <span>Gestion des Comptes</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('users.create') }}" class="{{ request()->routeIs('users.create') ? 'active' : '' }}">
                                <i class="fas fa-user-plus"></i>Créer un Compte</a></li>
                            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.index') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>Liste des Utilisateurs</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Paramètres -->
                @if (Auth::user()->hasAnyRole(['admin']))
                    <li class="menu-title">
                        <span>Paramètres</span>
                    </li>
                    <li class="submenu {{ request()->routeIs('postes.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Postes</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('postes.index') }}" class="{{ request()->routeIs('postes.index') ? 'active' : '' }}">
                                <i class="fas fa-list-ul"></i>Liste des Postes</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des sous-menus
    const submenuToggles = document.querySelectorAll('.submenu-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();

            const parentLi = this.closest('.submenu');
            const submenuList = parentLi.querySelector('.submenu-list');
            const arrow = this.querySelector('.menu-arrow');

            // Fermer tous les autres sous-menus
            submenuToggles.forEach(otherToggle => {
                if (otherToggle !== this) {
                    const otherParent = otherToggle.closest('.submenu');
                    const otherSubmenu = otherParent.querySelector('.submenu-list');
                    const otherArrow = otherToggle.querySelector('.menu-arrow');

                    otherSubmenu.classList.remove('show');
                    otherParent.classList.remove('active');
                    otherArrow.style.transform = 'rotate(0deg)';
                }
            });

            // Toggle le sous-menu actuel
            if (submenuList.classList.contains('show')) {
                submenuList.classList.remove('show');
                parentLi.classList.remove('active');
                arrow.style.transform = 'rotate(0deg)';
            } else {
                submenuList.classList.add('show');
                parentLi.classList.add('active');
                arrow.style.transform = 'rotate(90deg)';
            }
        });
    });

    // Ouvrir automatiquement le sous-menu si un item est actif
    const activeSubmenus = document.querySelectorAll('.submenu.active');
    activeSubmenus.forEach(submenu => {
        const submenuList = submenu.querySelector('.submenu-list');
        const arrow = submenu.querySelector('.menu-arrow');

        if (submenuList) {
            submenuList.classList.add('show');
            if (arrow) {
                arrow.style.transform = 'rotate(90deg)';
            }
        }
    });

    // Gestion des liens actifs dans les sous-menus
    const submenuLinks = document.querySelectorAll('.submenu-list a');
    submenuLinks.forEach(link => {
        if (link.classList.contains('active')) {
            const parentSubmenu = link.closest('.submenu');
            const submenuList = parentSubmenu.querySelector('.submenu-list');
            const arrow = parentSubmenu.querySelector('.menu-arrow');

            parentSubmenu.classList.add('active');
            submenuList.classList.add('show');
            if (arrow) {
                arrow.style.transform = 'rotate(90deg)';
            }
        }
    });

    // Animation de survol pour les icônes
    const menuItems = document.querySelectorAll('.sidebar-menu a');
    menuItems.forEach(item => {
        const icon = item.querySelector('i');

        item.addEventListener('mouseenter', function() {
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });

        item.addEventListener('mouseleave', function() {
            if (icon && !this.closest('li').classList.contains('active')) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Effet de ripple sur les clics
    const menuLinks = document.querySelectorAll('.sidebar-menu a');
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Créer l'effet ripple
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 1;
            `;

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});

// CSS pour l'animation ripple
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

{{-- <div class="d-flex flex-column h-100 bg-white shadow rounded-3 p-3">
    <!-- Bouton Composer -->
    <button class="btn btn-primary w-100 mb-4 fw-bold" data-bs-toggle="modal" data-bs-target="#composeModal">
        <i class="fas fa-pen"></i> Composer
    </button>

    <!-- Navigation principale -->
    <nav class="nav flex-column mb-4">
        <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.index') ? 'active fw-bold text-primary' : 'text-dark' }}">
            <i class="fas fa-inbox me-2"></i> Inbox
            <span class="badge bg-light text-dark float-end">21</span>
        </a>
        <a href="{{ route('messages.sent') }}" class="nav-link {{ request()->routeIs('messages.sent') ? 'active fw-bold text-primary' : 'text-dark' }}">
            <i class="fas fa-paper-plane me-2"></i> Sent
        </a>
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-file-alt me-2"></i> Draft
            <span class="badge bg-warning text-dark float-end">2</span>
        </a>
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-star me-2"></i> Starred
        </a>
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-exclamation-circle me-2"></i> Spam
            <span class="badge bg-danger float-end">4</span>
        </a>
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-trash me-2"></i> Trash
        </a>
    </nav>

    <!-- Labels -->
    <div class="mt-auto">
        <div class="fw-bold text-muted small mb-2">LABELS</div>
        <div class="d-flex flex-column gap-2">
            <span class="d-flex align-items-center"><span class="badge bg-success me-2" style="width:12px;height:12px;"></span> Personnel</span>
            <span class="d-flex align-items-center"><span class="badge bg-primary me-2" style="width:12px;height:12px;"></span> Société</span>
            <span class="d-flex align-items-center"><span class="badge bg-warning me-2" style="width:12px;height:12px;"></span> Important</span>
            <span class="d-flex align-items-center"><span class="badge bg-info me-2" style="width:12px;height:12px;"></span> Privé</span>
        </div>
    </div>
</div> --}}

<!-- Modale de composition (à placer dans create.blade.php ou layouts/master) -->
{{--
<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
  ...
</div>
--}}
