<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <div class="row">
        <!-- Barre latérale de navigation -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm rounded">
                <a href="<?php echo e(route('messages.index')); ?>" class="list-group-item list-group-item-action active">
                    <i class="fas fa-inbox"></i> Boîte de Réception
                </a>
                <a href="<?php echo e(route('messages.sent')); ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-paper-plane"></i> Boîte d'Envoi
                </a>
                <a href="<?php echo e(route('messages.create')); ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-edit"></i> Nouveau Message
                </a>
            </div>
        </div>

        <!-- Boîte de réception -->
        <div class="col-md-9">
            <h2 class="mb-4">Boîte de Réception</h2>

            <?php if($messages->isEmpty()): ?>
                <div class="alert alert-info">Aucun message dans votre boîte de réception.</div>
            <?php else: ?>
                <div class="list-group shadow-sm">
                    <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('messages.show', $message->id)); ?>" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 <?php echo e($message->status == 'unread' ? 'bg-light' : ''); ?>">

                            <!-- Détails du message -->
                            <div>
                                <h5 class="mb-1">
                                    <?php echo e($message->subject); ?>

                                    <?php if($message->attachments->isNotEmpty()): ?>
                                        <i class="fas fa-paperclip ms-2 text-muted"></i>
                                    <?php endif; ?>
                                </h5>
                                <p class="text-muted mb-1 small">
                                    <strong>De :</strong> <?php echo e($message->sender->name ?? 'Expéditeur inconnu'); ?>

                                </p>
                                <p class="text-muted mb-1 small">
                                    <strong>À :</strong> 
                                    <?php $__currentLoopData = $message->recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-secondary"><?php echo e($recipient->name); ?></span><?php echo e(!$loop->last ? ', ' : ''); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </p>
                                <p class="text-muted small">
                                    <strong>Envoyé le :</strong> <?php echo e($message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i:s') : 'Non spécifiée'); ?>

                                </p>
                            </div>

                            <!-- Statut de lecture -->
                            <span class="badge <?php echo e($message->status == 'unread' ? 'bg-warning' : 'bg-success'); ?>">
                                <?php echo e($message->status == 'unread' ? 'Non lu' : 'Lu'); ?>

                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="pagination">
    <?php echo e($messages->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/messages/inbox.blade.php ENDPATH**/ ?>