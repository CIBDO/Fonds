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
                    <li class="submenu {{ request()->routeIs('demandes-fonds.create') || request()->routeIs('demandes-fonds.situation') || request()->routeIs('demandes-fonds.index') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-coins"></i>
                            <span>Demandes de Fonds</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('demandes-fonds.create') }}" class="{{ request()->routeIs('demandes-fonds.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i>Effectuer une Demande </a></li>
                            <li><a href="{{ route('demandes-fonds.index') }}" class="{{ request()->routeIs('demandes-fonds.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Liste des Demandes</a></li>
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
                            <li><a href="{{ route('demandes-fonds.envois') }}" class="{{ request()->routeIs('demandes-fonds.envois') ? 'active' : '' }}">
                                <i class="fas fa-send"></i>Envoyer des Fonds</a></li>
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
                            {{-- <li><a href="{{ route('demandes-fonds.etat-avant-envoi') }}" class="{{ request()->routeIs('demandes-fonds.etat-avant-envoi') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice"></i>Situation Avant Envoi</a></li> --}}
                            <li><a href="{{ route('demandes-fonds.etat-detaille-avant-envoi') }}" class="{{ request()->routeIs('demandes-fonds.etat-detaille-avant-envoi') ? 'active' : '' }}">
                                <i class="fas fa-file-excel"></i>Situation Mensuelle détaillée</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Rapports Statistiques -->
                @if (Auth::user()->hasAnyRole(['admin', 'acct', 'superviseur']))
                    <li class="submenu {{ request()->routeIs('demandes-fonds.consolide') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-chart-area"></i>
                            <span>Rapports</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('demandes-fonds.consolide') }}" class="{{ request()->routeIs('demandes-fonds.consolide') ? 'active' : '' }}">
                                <i class="fas fa-chart-bar"></i> Etat detaille/Poste</a></li>
                            <li><a href="{{ route('demandes-fonds.consolide-detaille') }}" class="{{ request()->routeIs('demandes-fonds.consolide-detaille') ? 'active' : '' }}">
                                <i class="fas fa-table"></i> Etat Détaillé/Personnel</a></li>
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

                <!-- MODULE PCS - SECTION POSTES (Saisie uniquement) -->
                @if ((Auth::user()->peut_saisir_pcs || Auth::user()->poste_id) && !Auth::user()->peut_valider_pcs && !Auth::user()->hasRole('acct'))
                    <li class="menu-title">
                        <span>PCS (UEMOA/AES)</span>
                    </li>

                    <!-- Déclarations PCS -->
                    <li class="submenu {{ request()->routeIs('pcs.declarations.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <span>Déclarations PCS</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            @if (Auth::user()->peut_saisir_pcs || Auth::user()->poste_id)
                                <li><a href="{{ route('pcs.declarations.create') }}" class="{{ request()->routeIs('pcs.declarations.create') ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>Nouvelle Déclaration</a></li>
                            @endif
                            <li><a href="{{ route('pcs.declarations.index') }}" class="{{ request()->routeIs('pcs.declarations.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Mes Déclarations</a></li>
                        </ul>
                    </li>

                    <!-- Autres Demandes -->
                    <li class="submenu {{ request()->routeIs('pcs.autres-demandes.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-folder-open"></i>
                            <span>Autres Demandes</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            @if (Auth::user()->peut_saisir_pcs || Auth::user()->poste_id)
                                <li><a href="{{ route('pcs.autres-demandes.create') }}" class="{{ request()->routeIs('pcs.autres-demandes.create') ? 'active' : '' }}">
                                    <i class="fas fa-plus-circle"></i>Nouvelle Demande</a></li>
                            @endif
                            <li><a href="{{ route('pcs.autres-demandes.index') }}" class="{{ request()->routeIs('pcs.autres-demandes.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Mes Demandes</a></li>
                        </ul>
                    </li>
                @endif

                <!-- MODULE PCS - SECTION ACCT (Validation & États Consolidés) -->
                @if (Auth::user()->peut_valider_pcs || Auth::user()->hasRole('acct'))
                    <li class="menu-title">
                        <span>PCS - ACCT</span>
                    </li>

                    <!-- Validation PCS -->
                    <li class="submenu {{ request()->routeIs('pcs.declarations.*') || request()->routeIs('pcs.autres-demandes.index') || request()->routeIs('pcs.autres-demandes.show') || request()->routeIs('pcs.autres-demandes.statistiques') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-check-double"></i>
                            <span>Validation PCS</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('pcs.declarations.index') }}" class="{{ request()->routeIs('pcs.declarations.index') || request()->routeIs('pcs.declarations.show') ? 'active' : '' }}">
                                <i class="fas fa-file-invoice-dollar"></i>Déclarations PCS</a></li>
                            <li><a href="{{ route('pcs.autres-demandes.index') }}" class="{{ request()->routeIs('pcs.autres-demandes.index') || request()->routeIs('pcs.autres-demandes.show') ? 'active' : '' }}">
                                <i class="fas fa-folder-open"></i>Autres Demandes</a></li>
                            <li><a href="{{ route('pcs.autres-demandes.statistiques') }}" class="{{ request()->routeIs('pcs.autres-demandes.statistiques') ? 'active' : '' }}">
                                <i class="fas fa-chart-pie"></i>Statistiques</a></li>
                        </ul>
                    </li>

                    <!-- États Consolidés - Interface Dynamique Unique -->
                    <li class="{{ request()->routeIs('pcs.etats-consolides.*') ? 'active' : '' }}">
                        <a href="{{ route('pcs.etats-consolides.index') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>États Consolidés</span>
                        </a>
                    </li>

                    <!-- Déstockages PCS -->
                    <li class="submenu {{ request()->routeIs('pcs.destockages.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-cash-register"></i>
                            <span>Déstockages</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('pcs.destockages.collecte') }}" class="{{ request()->routeIs('pcs.destockages.collecte') ? 'active' : '' }}">
                                <i class="fas fa-coins"></i>Vue de Collecte</a></li>
                            <li><a href="{{ route('pcs.destockages.create') }}" class="{{ request()->routeIs('pcs.destockages.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i>Nouveau Déstockage</a></li>
                            <li><a href="{{ route('pcs.destockages.index') }}" class="{{ request()->routeIs('pcs.destockages.index') || request()->routeIs('pcs.destockages.show') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Liste des Déstockages</a></li>
                            <li><a href="{{ route('pcs.destockages.etats') }}" class="{{ request()->routeIs('pcs.destockages.etats') ? 'active' : '' }}">
                                <i class="fas fa-chart-line"></i>États et Rapports</a></li>
                        </ul>
                    </li>
                @endif

                <!-- MODULE PCS - ADMIN (Gestion complète) -->
                @if (Auth::user()->hasRole('admin'))
                    <!-- Bureaux de Douanes -->
                    <li class="submenu {{ request()->routeIs('pcs.bureaux.*') ? 'active' : '' }}">
                        <a href="#" class="submenu-toggle">
                            <i class="fas fa-building"></i>
                            <span>Bureaux de Douanes</span>
                            <span class="menu-arrow fas fa-chevron-right"></span>
                        </a>
                        <ul class="submenu-list">
                            <li><a href="{{ route('pcs.bureaux.create') }}" class="{{ request()->routeIs('pcs.bureaux.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i>Nouveau Bureau</a></li>
                            <li><a href="{{ route('pcs.bureaux.index') }}" class="{{ request()->routeIs('pcs.bureaux.index') ? 'active' : '' }}">
                                <i class="fas fa-list"></i>Liste des Bureaux</a></li>
                        </ul>
                    </li>
                @endif

                <!-- MODULE TRIE - COTISATIONS CCIM -->
                <li class="menu-title">
                    <span> FONDS DE GARANTIE TRIE</span>
                </li>

                <!-- Bureaux TRIE -->
                <li class="submenu {{ request()->routeIs('trie.bureaux.*') ? 'active' : '' }}">
                    <a href="#" class="submenu-toggle">
                        <i class="fas fa-building"></i>
                        <span>Bureaux TRIE</span>
                        <span class="menu-arrow fas fa-chevron-right"></span>
                    </a>
                    <ul class="submenu-list">
                        <li><a href="{{ route('trie.bureaux.index') }}" class="{{ request()->routeIs('trie.bureaux.index') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>Liste des Bureaux</a></li>
                    </ul>
                </li>

                <!-- Cotisations TRIE -->
                <li class="submenu {{ request()->routeIs('trie.cotisations.*') ? 'active' : '' }}">
                    <a href="#" class="submenu-toggle">
                        <i class="fas fa-coins"></i>
                        <span>Cotisations TRIE</span>
                        <span class="menu-arrow fas fa-chevron-right"></span>
                    </a>
                    <ul class="submenu-list">
                        @if (Auth::user()->poste_id)
                            <li><a href="{{ route('trie.cotisations.create') }}" class="{{ request()->routeIs('trie.cotisations.create') ? 'active' : '' }}">
                                <i class="fas fa-plus-circle"></i>Nouvelle Cotisation</a></li>
                        @endif
                        <li><a href="{{ route('trie.cotisations.index') }}" class="{{ request()->routeIs('trie.cotisations.index') || request()->routeIs('trie.cotisations.show') ? 'active' : '' }}">
                            <i class="fas fa-list"></i>Liste des Cotisations</a></li>
                    </ul>
                </li>

                <!-- États TRIE (ACCT uniquement) -->
                @if (Auth::user()->hasAnyRole(['admin', 'acct']))
                    <li class="{{ request()->routeIs('trie.etats.*') ? 'active' : '' }}">
                        <a href="{{ route('trie.etats.index') }}">
                            <i class="fas fa-chart-line"></i>
                            <span>États et Rapports</span>
                        </a>
                    </li>
                @endif

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
    'use strict';

    /**
     * Gestionnaire de sidebar professionnel
     * Gère les sous-menus de manière optimisée et accessible
     */
    class SidebarManager {
        constructor() {
            this.init();
        }

        init() {
            this.cleanupSubmenuStates();
            this.setupSubmenuToggles();
            this.setupAutoOpenActiveSubmenus();
            this.setupActiveSubmenuLinks();
        }

        /**
         * Nettoie les états des sous-menus pour éviter les conflits
         */
        cleanupSubmenuStates() {
            // Fermer tous les sous-menus au démarrage
            const allSubmenus = document.querySelectorAll('.submenu');
            allSubmenus.forEach(submenu => {
                const submenuList = submenu.querySelector('.submenu-list');
                const arrow = submenu.querySelector('.menu-arrow');

                if (submenuList && !submenu.classList.contains('active')) {
                    submenuList.classList.remove('show');
                }
                if (arrow && !submenu.classList.contains('active')) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            });
        }

        /**
         * Configure les événements de basculement des sous-menus
         */
        setupSubmenuToggles() {
            const submenuToggles = document.querySelectorAll('.submenu-toggle');

            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', this.handleSubmenuToggle.bind(this));
            });
        }

        /**
         * Gère le clic sur un bouton de sous-menu
         * @param {Event} e - L'événement de clic
         */
        handleSubmenuToggle(e) {
            e.preventDefault();

            const toggle = e.currentTarget;
            const parentLi = toggle.closest('.submenu');
            const submenuList = parentLi?.querySelector('.submenu-list');
            const arrow = toggle.querySelector('.menu-arrow');

            if (!parentLi || !submenuList) {
                console.warn('Structure de sous-menu invalide détectée');
                return;
            }

            this.closeAllOtherSubmenus(toggle);
            this.toggleCurrentSubmenu(parentLi, submenuList, arrow);
        }

        /**
         * Ferme tous les autres sous-menus sauf celui spécifié
         * @param {Element} currentToggle - Le bouton de basculement actuel
         */
        closeAllOtherSubmenus(currentToggle) {
            const allToggles = document.querySelectorAll('.submenu-toggle');

            allToggles.forEach(toggle => {
                if (toggle !== currentToggle) {
                    const parentLi = toggle.closest('.submenu');
                    const submenuList = parentLi?.querySelector('.submenu-list');
                    const arrow = toggle.querySelector('.menu-arrow');

                    submenuList?.classList.remove('show');
                    parentLi?.classList.remove('active');
                    if (arrow) {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                }
            });
        }

        /**
         * Bascule l'état du sous-menu actuel
         * @param {Element} parentLi - L'élément parent du sous-menu
         * @param {Element} submenuList - La liste du sous-menu
         * @param {Element|null} arrow - La flèche du sous-menu
         */
        toggleCurrentSubmenu(parentLi, submenuList, arrow) {
            const isOpen = submenuList.classList.contains('show');

            if (isOpen) {
                this.closeSubmenu(parentLi, submenuList, arrow);
            } else {
                this.openSubmenu(parentLi, submenuList, arrow);
            }
        }

        /**
         * Ouvre un sous-menu
         * @param {Element} parentLi - L'élément parent du sous-menu
         * @param {Element} submenuList - La liste du sous-menu
         * @param {Element|null} arrow - La flèche du sous-menu
         */
        openSubmenu(parentLi, submenuList, arrow) {
            submenuList.classList.add('show');
            parentLi.classList.add('active');
            if (arrow) {
                arrow.style.transform = 'rotate(90deg)';
            }
        }

        /**
         * Ferme un sous-menu
         * @param {Element} parentLi - L'élément parent du sous-menu
         * @param {Element} submenuList - La liste du sous-menu
         * @param {Element|null} arrow - La flèche du sous-menu
         */
        closeSubmenu(parentLi, submenuList, arrow) {
            submenuList.classList.remove('show');
            parentLi.classList.remove('active');
            if (arrow) {
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        /**
         * Configure l'ouverture automatique des sous-menus actifs
         * N'ouvre qu'un seul sous-menu à la fois pour éviter les conflits
         */
        setupAutoOpenActiveSubmenus() {
            const activeSubmenus = document.querySelectorAll('.submenu.active');

            // N'ouvrir qu'un seul sous-menu actif (le premier trouvé)
            // pour éviter les conflits entre plusieurs sous-menus actifs
            if (activeSubmenus.length > 0) {
                const primarySubmenu = activeSubmenus[0];
                const submenuList = primarySubmenu.querySelector('.submenu-list');
                const arrow = primarySubmenu.querySelector('.menu-arrow');

                if (submenuList) {
                    this.openSubmenu(primarySubmenu, submenuList, arrow);
                }
            }
        }

        /**
         * Configure la gestion des liens actifs dans les sous-menus
         * Améliore la précision pour éviter les conflits
         */
        setupActiveSubmenuLinks() {
            const submenuLinks = document.querySelectorAll('.submenu-list a');
            const activeLinks = [];

            // Collecter tous les liens actifs d'abord
            submenuLinks.forEach(link => {
                if (link.classList.contains('active')) {
                    activeLinks.push(link);
                }
            });

            // N'ouvrir qu'un seul sous-menu (le premier lien actif trouvé)
            // pour éviter les conflits entre plusieurs sous-menus
            if (activeLinks.length > 0) {
                const primaryLink = activeLinks[0];
                const parentSubmenu = primaryLink.closest('.submenu');
                const submenuList = parentSubmenu?.querySelector('.submenu-list');
                const arrow = parentSubmenu?.querySelector('.menu-arrow');

                if (parentSubmenu && submenuList) {
                    this.openSubmenu(parentSubmenu, submenuList, arrow);
                }
            }
        }
    }

    // Initialiser le gestionnaire de sidebar
    new SidebarManager();
});
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
