<?php $__currentLoopData = $postes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poste): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="editPosteModal<?php echo e($poste->id); ?>" tabindex="-1" aria-labelledby="editPosteModalLabel<?php echo e($poste->id); ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPosteModalLabel<?php echo e($poste->id); ?>">Modifier le Poste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('postes.update', $poste)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="mb-3">
                        <label for="nom<?php echo e($poste->id); ?>" class="form-label">Libellé</label>
                        <input type="text" id="nom<?php echo e($poste->id); ?>" name="nom" class="form-control" value="<?php echo e($poste->nom); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/postes/edit.blade.php ENDPATH**/ ?>