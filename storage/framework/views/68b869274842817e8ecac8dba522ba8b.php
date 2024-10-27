<div class="header">
            <div class="header-left">
                <a href="<?php echo e(route('login')); ?>" class="logo">
                    <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="Logo">
                </a>
                <a href="<?php echo e(route('login')); ?>" class="logo logo-small">
                    <img src="<?php echo e(asset('assets/img/logo-small.png')); ?>" alt="Logo" width="30" height="30">
                </a>
            </div>
            <div class="menu-toggle">
                <a href="javascript:void(0);" id="toggle_btn">
                    <i class="fas fa-bars"></i>
                </a>
            </div>

            <div class="top-nav-search">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item dropdown noti-dropdown language-drop me-2">
                    <div class="dropdown-menu ">
                        <div class="noti-content">
                            <div>
                                <a class="dropdown-item" href="javascript:;"><i class="flag flag-bl me-2"></i>Francais</a>
                            </div>
                        </div>
                    </div>
                </li>   
                <li class="nav-item dropdown noti-dropdown me-2">
                    <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                        <img src="<?php echo e(asset('assets/img/icons/header-icon-05.svg')); ?>" alt="">
                        <span class="badge rounded-pill bg-danger notification-count"><?php echo e(auth()->user()->unreadNotifications->count()); ?></span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="javascript:void(0)" class="clear-noti" id="markAllAsRead"> Tout marquer comme lu </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                <?php $__empty_1 = true; $__currentLoopData = auth()->user()->unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <li class="notification-message" data-notification-id="<?php echo e($notification->id); ?>">
                                        <a href="#" class="notification-link" data-url="<?php echo e($notification->data['url'] ?? '#'); ?>">
                                            <div class="media d-flex">
                                                <div class="media-body">
                                                    <p class="noti-details fw-bold text-primary">
                                                        <?php if($notification->type === 'App\Notifications\DemandeFondsNotification'): ?>
                                                            <span class="text-danger">Demande de fonds:</span>
                                                            <p><?php echo e($notification->data['message']); ?></p>
                                                            <p>Montant: <?php echo e($notification->data['montant']); ?> FCFA</p>
                                                        <?php elseif($notification->type === 'App\Notifications\DemandeFondsStatusNotification'): ?>
                                                            <span class="text-warning">Mise à jour du statut:</span>
                                                            <p><?php echo e($notification->data['message']); ?></p>
                                                        <?php elseif($notification->type === 'App\Notifications\MessageSent'): ?>
                                                            <span class="text-success">Nouveau message:</span>
                                                            <p>Expéditeur: <?php echo e($notification->data['sender_name']); ?></p>
                                                            <p>Objet: <?php echo e($notification->data['subject']); ?></p>
                                                        <?php endif; ?>
                                                    </p>
                                                    <p class="noti-time">
                                                        <span class="notification-time text-muted">
                                                            <?php echo e($notification->created_at->diffForHumans()); ?>

                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <li class="notification-message">
                                        <p class="text-center text-muted py-3">Aucune notification non lue</p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="<?php echo e(route('demandes-fonds.situation')); ?>">Voir toutes les Notifications</a>
                        </div>
                    </div>
                </li>
                       
                <li class="nav-item zoom-screen me-2">
                    <a href="#" class="nav-link header-nav-list win-maximize">
                        <img src="<?php echo e(asset('assets/img/icons/header-icon-04.svg')); ?>" alt="">
                    </a>
                </li>
                <li class="nav-item dropdown has-arrow new-user-menus">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="<?php echo e(asset('assets/img/profiles/Avatar-01.png')); ?>" width="31" alt="<?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?>">
                            <div class="user-text">
                                <h6><?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?></h6>
                                <p class="text-muted mb-0"><?php echo e(Auth::check() ? Auth::user()->role : 'N/A'); ?></p>
                            </div>
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="user-header">
                            <div class="avatar avatar-sm">
                                <img src="<?php echo e(asset('assets/img/profiles/Avatar-01.png')); ?>" alt="User Image" class="avatar-img rounded-circle">
                            </div>
                            <div class="user-text">
                                <h6><?php echo e(Auth::check() ? Auth::user()->name : 'Guest'); ?></h6>
                                <p class="text-muted mb-0"><?php echo e(Auth::check() ? Auth::user()->role : 'N/A'); ?></p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="<?php echo e(Auth::check() ? route('profile.edit') : '#'); ?>">Mon Profil</a>
                        
                        <?php if(Auth::check()): ?>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                <?php echo csrf_field(); ?>
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <?php else: ?>
                            <a class="dropdown-item" href="<?php echo e(route('login')); ?>">Login</a>
                            <a class="dropdown-item" href="<?php echo e(route('register')); ?>">Register</a>
                        <?php endif; ?>
                    </div>
                </li>
                
                

            </ul>

        </div>
<?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/partials/header.blade.php ENDPATH**/ ?>