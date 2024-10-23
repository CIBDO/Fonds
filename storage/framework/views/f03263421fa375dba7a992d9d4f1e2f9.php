

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes</li>
            </ul>
        </div>
    </div>
</div>

<form action="<?php echo e(route('demandes-fonds.index')); ?>" method="GET">
    <div class="demande-group-form">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="poste" class="form-control" placeholder="Rechercher par poste ..." value="<?php echo e(request('poste')); ?>">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                    <input type="text" name="mois" class="form-control" placeholder="Rechercher par mois ..." value="<?php echo e(request('mois')); ?>">
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="form-group">
                    <input type="text" name="total_courant" class="form-control" placeholder="Rechercher par montant ..." value="<?php echo e(request('total_courant')); ?>">
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
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Demandes</h3>
                        </div>
                        <div class="col-auto text-end float-end ms-auto download-grp">
                            <a href="#" class="btn btn-outline-gray me-2 active"><i class="feather-list"></i></a>
                            <a href="#" class="btn btn-outline-gray me-2"><i class="feather-grid"></i></a>
                            <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-download"></i> Download</a>
                            <a href="<?php echo e(route('demandes-fonds.create')); ?>" class="btn btn-primary"><i class="fas fa-plus"></i></a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="demandes-table" class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
                        <thead class="student-thread">
                            <tr>
                                <th>Mois</th>
                                <th>Date de Réception</th>
                                <th>Poste</th>
                                <th>Montant demandé</th>
                                <th>Date de la demande</th>
                                <th>Statut</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="demandes-table-body">
                            <?php $__currentLoopData = $demandeFonds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($demande->mois); ?></td>
                                <td><?php echo e($demande->date_reception); ?></td>
                                <td><?php echo e($demande->poste->nom); ?></td>
                                <td><?php echo e(number_format($demande->solde, 0, ',', ' ')); ?></td>
                                <td><?php echo e($demande->created_at); ?></td>
                                <td> <?php if($demande->status === 'en_attente'): ?>
                                    <span class="status-en-attente">En attente</span>
                                <?php elseif($demande->status === 'approuve'): ?>
                                    <span class="status-approuve">Approuvé</span>
                                <?php elseif($demande->status === 'rejete'): ?>
                                    <span class="status-rejete">Rejeté</span>
                                <?php else: ?>
                                    <span><?php echo e($demande->status); ?></span>
                                <?php endif; ?></td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a href="<?php echo e(route('demandes-fonds.show', $demande->id)); ?>" class="btn btn-sm bg-success-light me-2">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('demandes-fonds.edit', $demande->id)); ?>" class="btn btn-sm bg-danger-light">
                                            <i class="feather-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('demande-fonds.generate.pdf', $demande->id)); ?>" class="btn btn-sm bg-info-light">
                                            <i class="feather-printer"></i> 
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>   
                        </tbody>
                    </table>   
                </div>
            </div>
        </div>
    </div>

    <div class="pagination-wrapper">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Ajout de la classe Bootstrap pour la pagination -->
                <?php echo e($demandeFonds->links('pagination::bootstrap-4')); ?>

            </ul>
        </nav>
    </div>
</div>
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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/demandes/index.blade.php ENDPATH**/ ?>