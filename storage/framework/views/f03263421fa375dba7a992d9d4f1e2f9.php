

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

<div class="demande-group-form">
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                <input type="text" id="search-poste" class="form-control" placeholder="Rechercher par poste ...">
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="form-group">
                <input type="text" id="search-mois" class="form-control" placeholder="Rechercher par mois ...">
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="form-group">
                <input type="text" id="search-montant" class="form-control" placeholder="Rechercher par montant ...">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="search-student-btn">
                <button type="button" class="btn btn-primary">Rechercher</button>
            </div>
        </div>
    </div>
</div>

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
                    <table class="table border-0 star-student table-hover table-center mb-0 datatable table-striped">
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
                                <td><?php echo e(number_format($demande->total_courant, 0, ',', ' ')); ?></td>
                                <td><?php echo e($demande->created_at); ?></td>
                                <td><?php echo e($demande->status); ?></td>
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
                    <?php echo e($demandeFonds->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchPoste = document.getElementById('search-poste');
        const searchMois = document.getElementById('search-mois');
        const searchMontant = document.getElementById('search-montant');
        const demandesTableBody = document.getElementById('demandes-table-body');

        const filterRows = () => {
            const posteFilter = searchPoste.value.toLowerCase().trim();
            const moisFilter = searchMois.value.toLowerCase().trim();
            // Assurez-vous de retirer tous les espaces et les virgules pour une comparaison numérique
            const montantFilter = searchMontant.value.replace(/\s+/g, '').replace(/,/g, '');

            const rows = demandesTableBody.querySelectorAll('tr');

            rows.forEach(row => {
                const cols = row.querySelectorAll('td');
                const mois = cols[0].textContent.toLowerCase().trim();
                const poste = cols[2].textContent.toLowerCase().trim();
                // Retirez également ici les espaces et les virgules
                const montant = cols[3].textContent.replace(/\s+/g, '').replace(/,/g, '');

                const montantValue = parseInt(montant, 10) || 0;
                const montantFilterValue = parseInt(montantFilter, 10);

                const matchPoste = poste.includes(posteFilter);
                const matchMois = mois.includes(moisFilter);
                // Modifiez la condition pour gérer correctement les cas où le filtre de montant est vide
                const matchMontant = montantFilter === '' || montantValue <= montantFilterValue;

                row.style.display = (matchPoste && matchMois && matchMontant) ? '' : 'none';
            });
        };

        searchPoste.addEventListener('input', filterRows);
        searchMois.addEventListener('input', filterRows);
        searchMontant.addEventListener('input', filterRows);
    });
</script>
<?php $__env->stopSection(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/demandes/index.blade.php ENDPATH**/ ?>