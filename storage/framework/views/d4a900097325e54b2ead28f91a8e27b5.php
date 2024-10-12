

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h1>Boîte d'Envoi</h1>
    <?php if($messages->isEmpty()): ?>
        <p class="text-muted">Aucun message envoyé.</p>
    <?php else: ?>
        <ul class="list-group">
            <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5><?php echo e($message->subject); ?></h5>
                        <p><?php echo e(Str::limit($message->body, 100)); ?></p>
                        <small>De: <?php echo e($message->sender->name ?? 'Expéditeur inconnu'); ?></small><br>
                        <small>À: 
                            <?php $__currentLoopData = $message->recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-info"><?php echo e($recipient->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </small>
                    </div>
                    <a href="<?php echo e(route('messages.show', $message->id)); ?>" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/messages/sent.blade.php ENDPATH**/ ?>