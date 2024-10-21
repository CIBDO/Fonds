

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Notifications</h1>

    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="alert alert-info">
            <?php echo e($notification->data['message']); ?> <!-- Afficher le contenu de la notification -->
            <a href="<?php echo e(route('markAsRead', $notification->id)); ?>" class="btn btn-sm btn-primary">Marquer comme lu</a>
            <a href="<?php echo e(route('deleteNotification', $notification->id)); ?>" class="btn btn-sm btn-danger">Supprimer</a>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views\messages\notification.blade.php ENDPATH**/ ?>