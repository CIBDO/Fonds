

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h4>Transférer le message</h4>
    <form action="<?php echo e(route('messages.forward.store', $originalMessage->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label for="subject" class="form-label">Sujet</label>
            <input type="text" name="subject" class="form-control" value="FW: <?php echo e($originalMessage->subject); ?>" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Message</label>
            <textarea name="body" class="form-control" rows="5" required><?php echo e($originalMessage->body); ?></textarea>
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
            <label for="attachments" class="form-label">Ajouter des pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-success">Envoyer</button>
        <a href="<?php echo e(route('messages.show', $originalMessage->id)); ?>" class="btn btn-secondary">Annuler</a>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/messages/forward.blade.php ENDPATH**/ ?>