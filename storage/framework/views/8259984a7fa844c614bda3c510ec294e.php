

<?php $__env->startSection('content'); ?>
<div class="container">
    <h1>Notifications</h1>

    <?php if($notifications->isEmpty()): ?>
        <p>Aucune notification.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item">
                    <strong><?php echo e($notification->data['sujet']); ?></strong><br>
                    <?php echo e($notification->data['contenu']); ?><br>
                    <small>Envoyé par : <?php echo e($notification->data['sender_id']); ?></small>
                    <a href="<?php echo e(route('messages.show', $notification->data['message_id'])); ?>" class="btn btn-primary btn-sm">Voir le message</a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views\notification.blade.php ENDPATH**/ ?>