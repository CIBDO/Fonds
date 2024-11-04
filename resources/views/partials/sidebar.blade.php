<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Menu Principal</span>
                </li>
                
                <!-- Tableau de Bord -->
                @if (Auth::user()->hasRole('admin'))
                    <li><a href="{{ route('dashboard.admin') }}"><i class="feather-grid"></i> Administrateur</a></li>
                @elseif (Auth::user()->hasRole('tresorier'))
                    <li><a href="{{ route('dashboard.tresorier') }}"><i class="feather-grid"></i> Tableau de Bord Trésorier</a></li>
                @elseif (Auth::user()->hasRole('acct'))
                    <li><a href="{{ route('dashboard.acct') }}"><i class="feather-grid"></i> Tableau de Bord ACCT</a></li>
                @endif

                <!-- Demandes de Fonds -->
                @if (Auth::user()->hasAnyRole(['tresorier', 'admin']))
                    <li class="submenu">
                        <a href="#"><i class="fas fa-building"></i> <span> Demandes de Fonds</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('demandes-fonds.create') }}">Faire une DF</a></li>
                            <li><a href="{{ route('demandes-fonds.index') }}">Liste des DF</a></li>
                            <li><a href="{{ route('demandes-fonds.situation') }}">Suivi des DF</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Envoi de Fonds -->
                @if (Auth::user()->hasAnyRole(['acct', 'admin']))
                    <li class="submenu">
                        <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span> Envoi de Fonds</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('demandes-fonds.index') }}">Liste des DF</a></li>
                            <li><a href="{{ route('demandes-fonds.envois') }}">Envoyer des Fonds</a></li>
                            <li><a href="{{ route('demandes-fonds.situation') }}">Situation des DF</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Réception Paiement -->
                @if (Auth::user()->hasAnyRole(['tresorier', 'admin', 'acct']))
                    <li class="submenu">
                        <a href="#"><i class="fas fa-building"></i> <span> Réception Paiement</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="#">Réception & Paiement</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Rapports Statistiques -->
                @if (Auth::user()->hasAnyRole(['admin', 'acct']))
                    <li class="submenu">
                        <a href="#"><i class="fas fa-book-reader"></i> <span> Rapports Statistiques</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('demandes-fonds.situationDF') }}">Rapport Mensuel des DF</a></li>
                            <li><a href="{{ route('demandes-fonds.situationFE') }}">Rapport Mensuel des FE</a></li>
                            <li><a href="{{ route('demandes-fonds.recap') }}">Tableau Détaillé des DF</a></li>
                            <li><a href="{{ route('demandes-fonds.detail') }}">Situation Globale des DF</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Messagerie -->
                @if (Auth::user()->hasAnyRole(['tresorier', 'admin', 'acct','superviseur','direction']))
                    <li class="submenu">
                        <a href="#"><i class="fas fa-clipboard"></i> <span> Messagerie</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('messages.index') }}">Boîte de Réception</a></li>
                            <li><a href="{{ route('messages.sent') }}">Boîte d’Envoi</a></li>
                            <li><a href="{{ route('messages.create') }}">Nouveau Message</a></li>
                            <li><a href="#">Brouillons</a></li>
                        </ul>
                    </li>
                @endif

                <li class="menu-title">
                    <span>Management</span>
                </li>

                <!-- Gestion des Comptes -->
                @if (Auth::user()->hasAnyRole(['admin']))
                    <li class="submenu">
                        <a href="#"><i class="fas fa-users"></i> <span> Gestion des Comptes</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('users.create') }}">Créer un Compte</a></li>
                            <li><a href="{{ route('users.index') }}">Liste des Utilisateurs</a></li>
                        </ul>
                    </li>
                @endif

                <!-- Paramètres -->
                @if (Auth::user()->hasAnyRole(['admin']))
                    <li class="menu-title">
                        <span>Paramètres</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-building"></i> <span>Postes</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="{{ route('postes.index') }}">Liste des Postes</a></li>
                            <li><a href="{{ route('postes.create') }}">Créer un Poste</a></li>
                        </ul>
                        
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
