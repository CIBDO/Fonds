 

<?php $__env->startSection('content'); ?>
<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-left">
                    <img class="img-fluid" src="<?php echo e(asset('assets/img/login.jpg')); ?>" alt="Logo">
                </div>
                <div class="login-right">
                    <div class="login-right-wrap">
                        <h1>Créer un Compte</h1>
                        <p class="account-subtitle">Saisir les informations de l'utilisateur</p>

                        <!-- Formulaire de création d'utilisateur -->
                        <form action="<?php echo e(route('users.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>

                            <!-- Affichage des erreurs -->
                            <?php if($errors->any()): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <!-- Champ Username -->
                            <div class="form-group">
                                <label>Prénoms & Nom <span class="login-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="<?php echo e(old('name')); ?>">
                                <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                            </div>

                            <!-- Champ Email -->
                            <div class="form-group">
                                <label>Email <span class="login-danger">*</span></label>
                                <input class="form-control" type="email" name="email" value="<?php echo e(old('email')); ?>">
                                <span class="profile-views"><i class="fas fa-envelope"></i></span>
                            </div>

                            <!-- Champ Password -->
                            <div class="form-group">
                                <label>Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-input" type="password" name="password">
                                <span class="profile-views feather-eye toggle-password"></span>
                            </div>

                            <!-- Champ Confirm Password -->
                            <div class="form-group">
                                <label>Confirmer Password <span class="login-danger">*</span></label>
                                <input class="form-control pass-confirm" type="password" name="password_confirmation">
                                <span class="profile-views feather-eye reg-toggle-password"></span>
                            </div>
                            <!-- Champ Rôle -->
                            <div class="form-group custom-select">
                                <label>Rôle <span class="login-danger">*</span></label>
                                <select class="select2" name="role">
                                    <option value="">Choisir un rôle</option>
                                    <option value="admin" <?php echo e(old('role') == 'admin' ? 'selected' : ''); ?>>Admin</option>
                                    <option value="tresorier" <?php echo e(old('role') == 'tresorier' ? 'selected' : ''); ?>>Trésorier</option>
                                    <option value="acct" <?php echo e(old('role') == 'acct' ? 'selected' : ''); ?>>ACCT</option>
                                    <option value="superviseur" <?php echo e(old('role') == 'superviseur' ? 'selected' : ''); ?>>Superviseur</option>
                                    <!-- Ajoutez d'autres rôles si nécessaire -->
                                </select>
                            </div>
                            <div class="form-group custom-select">
                                <label>Statut <span class="login-danger">*</span></label>
                                <select class="select2" name="active">
                                    <option value="1" <?php echo e(old('active') == '1' ? 'selected' : ''); ?>>Actif</option>
                                    <option value="0" <?php echo e(old('active') == '0' ? 'selected' : ''); ?>>Inactif</option>
                                    <span class="profile-views"><i class="fas fa-envelope"></i></span>
                                </select>
                            </div>

                            <div class="dont-have">Avez-vous dun compte <a href="<?php echo e(route('login')); ?>">Se Connecter</a></div>

                            <!-- Bouton Register -->
                            <div class="form-group mb-0">
                                <button class="btn btn-primary btn-block" type="submit">S'inscrire</button>
                            </div>
                        </form>

                        <div class="login-or">
                            <span class="or-line"></span>
                            <span class="span-or">or</span>
                        </div>

                        <!-- Social Login -->
                        <div class="social-login">
                            <a href="#"><i class="fab fa-google-plus-g"></i></a>
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/users/create.blade.php ENDPATH**/ ?>