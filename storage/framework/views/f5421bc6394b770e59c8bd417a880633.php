

<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="<?php echo e(route('messages.index')); ?>" class="list-group-item list-group-item-action active">Boîte de Réception</a>
                <a href="<?php echo e(route('messages.sent')); ?>" class="list-group-item list-group-item-action">Boîte d'Envoi</a>
                
                <a href="<?php echo e(route('messages.create')); ?>" class="list-group-item list-group-item-action">Nouveau Message</a>
            </div>
        </div>
        <div class="col-md-9">
            <h2>Boîte de Réception</h2>
            <div class="list-group">
                <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('messages.show', $message->id)); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo e($message->status == 'unread' ? 'bg-light' : ''); ?>">
                        <div>
                            <h5 class="mb-1"><?php echo e($message->subject); ?></h5>
                            <p class="mb-1">
                                <strong>De:</strong> <?php echo e($message->sender->name ?? 'Expéditeur inconnu'); ?><br>
                                <strong>À:</strong> 
                                <?php $__currentLoopData = $message->recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-secondary"><?php echo e($recipient->name); ?></span><?php echo e(!$loop->last ? ', ' : ''); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <br>
                                <strong>Date et Heure d'envoi :</strong> <?php echo e($message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i:s') : 'Non spécifiée'); ?>


                            </p>
                            <?php if($message->attachments->isNotEmpty()): ?>
                                <span class="badge bg-info">Pièce jointe</span>
                            <?php endif; ?>
                        </div>
                        <span class="badge bg-secondary"><?php echo e($message->status == 'unread' ? 'Non lu' : 'Lu'); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views\messages\inbox.blade.php ENDPATH**/ ?>