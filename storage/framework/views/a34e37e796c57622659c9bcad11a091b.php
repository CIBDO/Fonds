

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Répondre au message</h1>
    
    <form action="<?php echo e(route('messages.reply', $message->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="mb-3">
            <label for="body" class="form-label">Corps du message</label>
            <textarea name="body" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="attachments" class="form-label">Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
    </form>

    <a href="<?php echo e(route('messages.index')); ?>" class="btn btn-secondary">Retour</a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views\messages\reply.blade.php ENDPATH**/ ?>