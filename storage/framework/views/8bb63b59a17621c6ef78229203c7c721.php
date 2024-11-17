<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreur !</strong> Veuillez corriger les erreurs suivantes :
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="container mt-5">
    <h3 class="mb-4" style="font-weight: bold; color: #007bff; font-family: 'Geologica', sans-serif;"><i class="fas fa-envelope"></i> Nouveau Message</h3>

    <form action="<?php echo e(route('messages.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label for="subject" class="form-label"><i class="fas fa-heading"></i> Objet</label>
            <input type="text" name="subject" class="form-control" placeholder="Entrez l'objet du message" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label"><i class="fas fa-align-left"></i> Corps du message</label>
            <textarea name="body" class="form-control" rows="5" placeholder="Écrivez votre message ici..." required></textarea>
        </div>

        <div class="mb-3">
            <label for="destinataires" class="form-label"><i class="fas fa-users"></i> Destinataires</label>
            
            <!-- Champ select qui agit comme un déclencheur -->
            <select class="form-select" id="destinataireTrigger" onclick="toggleCheckboxList()">
                <option value="" >Cliquez pour sélectionner des destinataires</option>
            </select>
            
            <?php if($users->isEmpty()): ?>
                <p class="text-muted">Aucun destinataire disponible.</p>
            <?php else: ?>
                <!-- Liste des cases à cocher, masquée initialement -->
                <div id="checkboxList" style="display: none; margin-top: 10px;">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <input type="checkbox" name="receiver_ids[]" value="<?php echo e($user->id); ?>" class="form-check-input" id="user-<?php echo e($user->id); ?>">
                            <label class="form-check-label" for="user-<?php echo e($user->id); ?>"><?php echo e($user->name); ?></label>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
        

        <div class="mb-3">
            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
            <small class="form-text text-muted">Vous pouvez joindre plusieurs fichiers.</small>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function toggleCheckboxList() {
        // Récupère l'élément de la liste des cases à cocher
        const checkboxList = document.getElementById('checkboxList');
        
        // Bascule la visibilité de la liste
        if (checkboxList.style.display === 'none' || checkboxList.style.display === '') {
            checkboxList.style.display = 'block';
        } else {
            checkboxList.style.display = 'none';
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/messages/create.blade.php ENDPATH**/ ?>