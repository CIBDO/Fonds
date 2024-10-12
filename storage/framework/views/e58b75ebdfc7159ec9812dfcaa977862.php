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
                                
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-building"></i> <span> Demandes de Fonds</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?php echo e(route('demandes-fonds.create')); ?>">Faire une DF</a></li>
                                <li><a href="<?php echo e(route('demandes-fonds.index')); ?>">Liste des DF</a></li>
                                <li><a href="#">Suivi des DF</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span> Envoi de Fonds </span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="#">Approuver les DF</a></li>
                                <li><a href="#">Envoyer de Fonds</a></li>
                                <li><a href="#">Situation des DF</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-building"></i> <span> Réception Paiement</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="#">Réception & Paiement</a></li>
                                
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-book-reader"></i> <span> Rapports Statistiques</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="#">Rapport Mensuel des DF</a></li>
                                <li><a href="add-subject.html">Rapport Mensuel des FE</a></li>
                                <li><a href="edit-subject.html">Tableau Comparatif des DF/Poste</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="#"><i class="fas fa-clipboard"></i> <span> Messagerie</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?php echo e(route('messages.index')); ?>">Boîte de Réception</a></li>
                                <li><a href="<?php echo e(route('messages.sent')); ?>">Boîte d’Envoi</a></li>
                                <li><a href="<?php echo e(route('messages.create')); ?>">Nouveau Message</a></li>
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
                                <li><a href="<?php echo e(route('users.create')); ?>">Créer un Compte</a></li>
                                <li><a href="<?php echo e(route('users.index')); ?>">Liste des Utilisateurs</a></li>
                            </ul>
                        </li>
                        <li class="menu-title">
                            <span>Paramètres</span>
                        </li>
                        <li class="submenu">
                            <a href="<?php echo e(route('postes.index')); ?>"><i class="fas fa-users"></i> <span>Postes</span> <span
                                    class="menu-arrow"></span></a>
                            <ul>
                                <li><a href="<?php echo e(route('postes.index')); ?>">Créer un Poste</a></li>
                              
                            </ul> 
                        </li>
                    </ul>
                </div>
            </div>
        </div>
<?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>