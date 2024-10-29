

<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?>
<div class="alert alert-danger">
    <ul>
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
</div>
<?php endif; ?>
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-envelope"></i> <?php echo e($message->subject); ?></h4>
            <span class="badge bg-<?php echo e($message->status == 'unread' ? 'warning' : 'success'); ?>">
                <?php echo e($message->status == 'unread' ? 'Non lu' : 'Lu'); ?>

            </span>
        </div>

        <div class="card-body">
            <!-- Informations sur l'expéditeur et les destinataires -->
            <div class="d-flex align-items-center mb-3">
                <img src="<?php echo e(asset('assets/img/profiles/' . ($message->sender->avatar ?: 'Avatar-01.png'))); ?>" alt="Avatar"
                     class="rounded-circle" style="width: 50px; height: 50px;">
                <div class="ms-3">
                    <strong>De:</strong> <?php echo e($message->sender->name ?? 'Expéditeur inconnu'); ?><br>
                    <strong>À:</strong> 
                    <?php $__currentLoopData = $message->recipients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-secondary"><?php echo e($recipient->name); ?></span><?php echo e(!$loop->last ? ', ' : ''); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            
            <p><strong>Date et Heure d'envoi :</strong> 
                <?php echo e($message->created_at ? $message->created_at->format('d/m/Y H:i:s') : 'Non défini'); ?>

            </p>
            
            <p><strong>Date et Heure de réception :</strong> 
                <?php echo e($recipient && $recipient->pivot->received_at ? 
                    \Carbon\Carbon::parse($recipient->pivot->received_at)->format('d/m/Y H:i:s') : 'Non défini'); ?>

            </p>

            <!-- Corps du message -->
            <p class="mb-4"><?php echo e($message->body); ?></p>

            <!-- Gestion des pièces jointes -->
            <?php if($message->attachments->isNotEmpty()): ?>
                <h5><i class="fas fa-paperclip"></i> Pièces jointes</h5>
                <ul class="list-group list-group-flush mb-3">
                    <?php $__currentLoopData = $message->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?php echo e($attachment->file_name); ?></span>
                            <div>
                                <a href="<?php echo e(route('attachments.download', $attachment->id)); ?>" class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                                <button class="btn btn-outline-info btn-sm" onclick="previewAttachment('<?php echo e(Storage::url($attachment->filepath)); ?>')">
                                    <i class="fas fa-eye"></i> Aperçu
                                </button>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php else: ?>
                <p class="text-muted"><i class="fas fa-paperclip"></i> Aucune pièce jointe</p>
            <?php endif; ?>
        </div>

        <!-- Pied de page avec les boutons d'action -->
        
        <!-- Pied de page avec les boutons d'action -->
        <div class="card-footer text-end">
            <a href="<?php echo e(route('messages.reply', $message->id)); ?>" class="btn btn-primary">
                <i class="fas fa-reply"></i> Répondre
            </a>
            <a href="<?php echo e(route('messages.replyAllForm', $message->id)); ?>" class="btn btn-warning">
                <i class="fas fa-reply-all"></i> Répondre à tous
            </a>
            
            <a href="<?php echo e(route('messages.forward', $message->id)); ?>" class="btn btn-success">
                <i class="fas fa-share"></i> Transférer
            </a>
            <a href="<?php echo e(route('messages.sent')); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la Boîte d'Envoi
            </a>
        </div>

    </div>
</div>

<!-- Modal d'aperçu des pièces jointes -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel"><i class="fas fa-eye"></i> Aperçu de la pièce jointe</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="attachmentPreview" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function previewAttachment(url) {
    document.getElementById('attachmentPreview').src = url;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/messages/show.blade.php ENDPATH**/ ?>