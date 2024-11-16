
<div class="modal fade" id="addPosteModal" tabindex="-1" aria-labelledby="addPosteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPosteModalLabel">Ajouter un Poste</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('postes.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Libellé</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/c2251405c/public_html/tresor.dntcp.com/resources/views/postes/add.blade.php ENDPATH**/ ?>