

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-reply-all"></i> Répondre à tous</h2>

    <!-- Affichage des erreurs de validation -->
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Liste des destinataires -->
    <p><strong>À :</strong> 
        <?php $__currentLoopData = $message->recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span class="badge bg-secondary"><?php echo e($recipient->name); ?></span><?php echo e(!$loop->last ? ', ' : ''); ?>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </p>

    <!-- Formulaire de réponse à tous -->
    <form action="<?php echo e(route('messages.replyAll', $message->id)); ?>" method="POST" enctype="multipart/form-data" class="shadow-sm p-4 rounded bg-light">
        <?php echo csrf_field(); ?>

        <!-- Corps du message -->
        <div class="mb-3">
            <label for="body" class="form-label"><i class="fas fa-envelope"></i> Corps du message</label>
            <textarea name="body" class="form-control" rows="5" placeholder="Écrivez votre réponse ici..." required></textarea>
        </div>

        <!-- Champ de pièces jointes avec indicateur de fichiers sélectionnés -->
        <div class="mb-3">
            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple id="attachmentInput">
            <small class="form-text text-muted">Sélectionnez plusieurs fichiers si nécessaire (formats: jpg, jpeg, png, pdf, doc).</small>
            <div id="fileList" class="mt-2 text-muted small"></div>
        </div>

        <!-- Boutons de soumission et retour -->
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Envoyer la réponse à tous
            </button>
            <a href="<?php echo e(route('messages.index')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </form>
</div>

<!-- JavaScript pour afficher le nom des fichiers joints -->
 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/messages/replyAll.blade.php ENDPATH**/ ?>