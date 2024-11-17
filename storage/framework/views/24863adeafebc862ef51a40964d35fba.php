<?php $__env->startSection('content'); ?>
<?php if(session('error')): ?>
    <div class="alert alert-danger">
        <?php echo e(session('error')); ?>

    </div>
<?php endif; ?>

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes de fonds</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes de fonds</li>
            </ul>
        </div>
    </div>
</div>
<form action="<?php echo e(route('demandes-fonds.envois')); ?>" method="GET">
    <div class="demande-group-form">
        <div class="row">
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="<?php echo e(request('poste')); ?>">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois ..." value="<?php echo e(request('mois')); ?>">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="date" name="date_debut" class="form-control" placeholder="Date de début" value="<?php echo e(request('date_debut')); ?>">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="form-group">
                    <input type="date" name="date_fin" class="form-control" placeholder="Date de fin" value="<?php echo e(request('date_fin')); ?>">
                </div>
            </div>
            <div class="col-lg-2">
                <div class="search-student-btn">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-table">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="demandes-table" class="table border-0 table-hover table-center mb-0 datatable table-striped">
                        <thead>
                            <tr>
                                <th>Mois</th>
                                <th>Date de réception</th>
                                <th>Poste</th>
                                <th>Fonds Demandés</th>
                                <th>Date de création</th>
                                <th>Statut</th>
                                <th style="text-align: center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $demandeFonds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($demande->mois); ?></td>
                                <td><?php echo e($demande->date_reception); ?></td>
                                <td><?php echo e($demande->poste->nom); ?></td>
                                <td><?php echo e(number_format($demande->total_courant, 0, ',', ' ')); ?></td>
                                <td><?php echo e($demande->created_at); ?></td>
                                <td>
                                    <?php if($demande->status === 'en_attente'): ?>
                                    <span class="status-en-attente">En attente</span>
                                <?php elseif($demande->status === 'approuve'): ?>
                                    <span class="status-approuve">Approuvé</span>
                                <?php elseif($demande->status === 'rejete'): ?>
                                    <span class="status-rejete">Rejeté</span>
                                <?php else: ?>
                                    <span><?php echo e($demande->status); ?></span>
                                <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="<?php echo e(route('demandes-fonds.show', $demande->id)); ?>" class="btn btn-sm bg-success-light me-2">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm bg-primary-light" data-bs-toggle="modal" data-bs-target="#approveModal-<?php echo e($demande->id); ?>">
                                            <i class="feather-check"></i>Valider
                                        </button>
                                        <button type="button" class="btn btn-sm bg-danger-light " data-bs-toggle="modal" data-bs-target="#rejectModal-<?php echo e($demande->id); ?>">
                                            <i class="feather-X"></i>Rejeter
                                        </button>
                                        <a href="<?php echo e(route('demande-fonds.generate.pdf', $demande->id)); ?>" class="btn btn-sm bg-info-light">
                                            <i class="feather-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal pour approuver la demande -->
                            <div class="modal fade" id="approveModal-<?php echo e($demande->id); ?>" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approuver la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?php echo e(route('demandes-fonds.update-status', $demande->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-body">
                                                <input type="hidden" name="status" value="approuve">
                                                <div class="form-group">
                                                    <label for="date_envois">Date d'envoi :</label>
                                                    <input type="date" name="date_envois" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="montant">Montant :</label>
                                                    <input type="number" name="montant" class="form-control" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="observation">Observation :</label>
                                                    <textarea name="observation" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Soumettre</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal pour rejeter la demande -->
                            <div class="modal fade" id="rejectModal-<?php echo e($demande->id); ?>" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Rejeter la demande</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?php echo e(route('demandes-fonds.update-status', $demande->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-body">
                                                <input type="hidden" name="status" value="rejete">
                                                <div class="form-group">
                                                    <label for="date_envois">Date d'envoi :</label>
                                                    <input type="date" name="date_envois" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="observation">Raison du rejet :</label>
                                                    <textarea name="observation" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Soumettre</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Fonction pour gérer l'affichage des champs
        function toggleFields(status) {
            const montantField = document.getElementById('montant-field');
            const observationField = document.getElementById('observation-field');

            if (status === 'approuve') {
                montantField.style.display = 'block';
                observationField.style.display = 'block';
            } else {
                montantField.style.display = 'none';
                observationField.style.display = 'none';
            }
        }

        // Écouter les changements sur le select du statut
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                const selectedStatus = this.value;
                toggleFields(selectedStatus);
            });
        });

        // Initialiser l'état des champs lors du chargement de la page
        statusSelects.forEach(select => {
            toggleFields(select.value);
        });
    });
</script>
<?php $__env->startSection('add-js'); ?>
    <!-- Inclure les fichiers DataTables CSS et JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#demandes-table').DataTable({
                order: [[1, 'desc']],  // Classe par date en ordre décroissant
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json"  // Traduction en français
                },
                paging: true,
                searching: true,
                ordering: true,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
                pageLength: 10
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/demandes/envois.blade.php ENDPATH**/ ?>