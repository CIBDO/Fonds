<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>Menu Principal</span>
                </li>
                
                <!-- Tableau de Bord -->
                <?php if(Auth::user()->hasRole('admin')): ?>
                    <li><a href="<?php echo e(route('dashboard.admin')); ?>"><i class="feather-grid"></i> Administrateur</a></li>
                <?php elseif(Auth::user()->hasRole('tresorier')): ?>
                    <li><a href="<?php echo e(route('dashboard.tresorier')); ?>"><i class="feather-grid"></i> Tableau de Bord Trésorier</a></li>
                <?php elseif(Auth::user()->hasRole('acct')): ?>
                    <li><a href="<?php echo e(route('dashboard.acct')); ?>"><i class="feather-grid"></i> Tableau de Bord ACCT</a></li>
                <?php endif; ?>

                <!-- Demandes de Fonds -->
                <?php if(Auth::user()->hasAnyRole(['tresorier', 'admin'])): ?>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-building"></i> <span> Demandes de Fonds</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="<?php echo e(route('demandes-fonds.create')); ?>">Faire une DF</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.index')); ?>">Liste des DF</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.situation')); ?>">Suivi des DF</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Envoi de Fonds -->
                <?php if(Auth::user()->hasAnyRole(['acct', 'admin'])): ?>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-chalkboard-teacher"></i> <span> Envoi de Fonds</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="<?php echo e(route('demandes-fonds.index')); ?>">Liste des DF</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.envois')); ?>">Envoyer des Fonds</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.situation')); ?>">Situation des DF</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Réception Paiement -->
                <?php if(Auth::user()->hasAnyRole(['tresorier', 'admin', 'acct'])): ?>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-building"></i> <span> Réception Paiement</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="#">Réception & Paiement</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Rapports Statistiques -->
                <?php if(Auth::user()->hasAnyRole(['admin', 'acct'])): ?>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-book-reader"></i> <span> Rapports Statistiques</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="<?php echo e(route('demandes-fonds.situationDF')); ?>">Rapport Mensuel des DF</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.situationFE')); ?>">Rapport Mensuel des FE</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.recap')); ?>">Tableau Détaillé des DF</a></li>
                            <li><a href="<?php echo e(route('demandes-fonds.detail')); ?>">Situation Globale des DF</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Messagerie -->
                <?php if(Auth::user()->hasAnyRole(['tresorier', 'admin', 'acct','superviseur','direction'])): ?>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-clipboard"></i> <span> Messagerie</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="<?php echo e(route('messages.index')); ?>">Boîte de Réception</a></li>
                            <li><a href="<?php echo e(route('messages.sent')); ?>">Boîte d’Envoi</a></li>
                            <li><a href="<?php echo e(route('messages.create')); ?>">Nouveau Message</a></li>
                            <li><a href="#">Brouillons</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="menu-title">
                    <span>Management</span>
                </li>

                <!-- Gestion des Comptes -->
                <?php if(Auth::user()->hasAnyRole(['admin'])): ?>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-users"></i> <span> Gestion des Comptes</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="<?php echo e(route('users.create')); ?>">Créer un Compte</a></li>
                            <li><a href="<?php echo e(route('users.index')); ?>">Liste des Utilisateurs</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- Paramètres -->
                <?php if(Auth::user()->hasAnyRole(['admin'])): ?>
                    <li class="menu-title">
                        <span>Paramètres</span>
                    </li>
                    <li class="submenu">
                        <a href="#"><i class="fas fa-building"></i> <span>Postes</span> <span class="menu-arrow"></span></a>
                        <ul>
                            <li><a href="<?php echo e(route('postes.index')); ?>">Liste des Postes</a></li>
                            <li><a href="<?php echo e(route('postes.create')); ?>">Créer un Poste</a></li>
                        </ul>
                        
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/partials/sidebar.blade.php ENDPATH**/ ?>