<?php $__env->startSection('content'); ?>
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-paper-plane"></i> Boîte d'Envoi</h2>

    <?php if($messages->isEmpty()): ?>
        <div class="alert alert-secondary text-center" role="alert">
            <i class="fas fa-inbox"></i> Aucun message envoyé.
        </div>
    <?php else: ?>
        <ul class="list-group shadow-sm">
            <?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="list-group-item d-flex justify-content-between align-items-center <?php echo e($message->status == 'unread' ? 'bg-light' : ''); ?>">
                    <div>
                        <!-- Sujet et contenu abrégé du message -->
                        <h5 class="mb-1">
                            <i class="fas fa-envelope<?php echo e($message->attachments->isNotEmpty() ? '-open-text' : ''); ?>"></i> 
                            <?php echo e($message->subject); ?>

                        </h5>
                        <p class="text-muted mb-2"><?php echo e(Str::limit($message->body, 100)); ?></p>
                        
                        <!-- Expéditeur et destinataires avec icône d'enveloppe pour chacun -->
                        <small>
                            <strong>De:</strong> <?php echo e($message->sender->name ?? 'Expéditeur inconnu'); ?><br>
                            <strong>À:</strong> 
                            <?php $__currentLoopData = $message->recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-secondary me-1">
                                    <i class="fas fa-user"></i> <?php echo e($recipient->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </small>

                        <!-- Icône pour pièce jointe si présente -->
                        <?php if($message->attachments->isNotEmpty()): ?>
                            <span class="badge bg-success mt-2">
                                <i class="fas fa-paperclip"></i> Pièce jointe
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Boutons d'action pour chaque message -->
                    <div class="d-flex align-items-center">
                        <a href="<?php echo e(route('messages.show', $message->id)); ?>" class="btn btn-outline-primary btn-sm me-2">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                       
                    </div>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
</div>

<div class="pagination">
    <?php echo e($messages->links()); ?>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/messages/sent.blade.php ENDPATH**/ ?>