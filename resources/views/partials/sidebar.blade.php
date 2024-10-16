<div class="sidebar" id="sidebar">
            <div class="sidebar-inner slimscroll">
                <div id="sidebar-menu" class="sidebar-menu">
                    <ul>
                        <li class="menu-title">
                            <span>Menu Principal</span>
                        </li>
                        <li class="submenu active">
                            <a href="#"><i class="feather-grid"></i> <span> Tableu de Bord</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="#" class="active">Administrateur</a></li>
                                {{-- <li><a href="teacher-dashboard.html">Teacher Dashboard</a></li>
                                <li><a href="student-dashboard.html">Student Dashboard</a></li> --}}
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-building"></i> <span> Demandes de Fonds</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="{{route('demandes-fonds.create')}}"><i class="fas fa-"></i>Faire une DF</a></li>
                                <li><a href="{{route('demandes-fonds.index')}}">Liste des DF</a></li>
                                <li><a href="{{route('demandes-fonds.situation')}}">Suivi des DF</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span> Envoi de Fonds </span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="{{route('demandes-fonds.envois')}}">Envoyer de Fonds</a></li>
                                <li><a href="{{route('demandes-fonds.situation')}}">Situation des DF</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-building"></i> <span> Réception Paiement</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="#">Réception & Paiement</a></li>
                                {{-- <li><a href="add-department.html">Department Add</a></li>
                                <li><a href="edit-department.html">Department Edit</a></li> --}}
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-book-reader"></i> <span> Rapports Statistiques</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="{{route('demandes-fonds.index')}}">Rapport Mensuel des DF</a></li>
                                <li><a href="{{route('demandes-fonds.envois')}}">Rapport Mensuel des FE</a></li>
                                <li><a href="{{route('demandes-fonds.situation')}}">Tableau Comparatif des DF/Poste</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-clipboard"></i> <span> Messagerie</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="{{route('messages.index')}}">Boîte de Réception</a></li>
                                <li><a href="{{route('messages.sent')}}">Boîte d’Envoi</a></li>
                                <li><a href="{{route('messages.create')}}">Nouveau Message</a></li>
                                <li><a href="#">Brouillons</a></li>
                            </ul>
                        </li>
                        <li class="menu-title">
                            <span>Management</span>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-users"></i> <span>Gestion des Comptes</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="{{route('users.create')}}">Créer un Compte</a></li>
                                <li><a href="{{route('users.index')}}">Liste des Utilisateurs</a></li>
                            </ul>
                        </li>
                        <li class="menu-title">
                            <span>Paramètres</span>
                        </li>
                        <li class="submenu">
                            <a href="{{route('postes.index')}}"><i class="fas fa-users"></i> <span>Postes</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="{{route('postes.index')}}">Créer un Poste</a></li>
                              
                            </ul> 
                        </li>
                    </ul>
                </div>
            </div>
        </div>
