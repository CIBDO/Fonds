
<?php $__env->startSection('content'); ?>
<div class="content container-fluid">

                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-sub-header">
                                <h3 class="page-title">Bienvenue</h3>
                                <ul class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6 style="font-size: 18px; color: hsl(210, 79%, 45%); font-weight: bold;">Montant Demandé</h6>
                                        <h3><?php echo e(number_format($fondsDemandes, 0, '', ' ')); ?></h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="<?php echo e(asset('assets/img/icons/dash-icon-04.svg')); ?>" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6 style="font-size: 16px; color: hsl(210, 79%, 45%); font-weight: bold;">Recettes Douanieres</h6>
                                        <h3><?php echo e(number_format($fondsRecettes, 0, '', ' ')); ?></h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="<?php echo e(asset('assets/img/icons/dash-icon-02.svg')); ?>" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6 style="font-size: 18px; color: hsl(210, 79%, 45%); font-weight: bold;">Fonds à Envoyer</h6>
                                        <h3><?php echo e(number_format($fondsEnCours, 0, '', ' ')); ?></h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="<?php echo e(asset('assets/img/icons/dash-icon-03.svg')); ?>" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12 d-flex">
                        <div class="card bg-comman w-100">
                            <div class="card-body">
                                <div class="db-widgets d-flex justify-content-between align-items-center">
                                    <div class="db-info">
                                        <h6 style="font-size: 18px; color: hsl(210, 79%, 45%); font-weight: bold;">Fonds Envoyés</h6>
                                        <h3><?php echo e(number_format($paiementsEffectues, 0, '', ' ')); ?></h3>
                                    </div>
                                    <div class="db-icon">
                                        <img src="<?php echo e(asset('assets/img/icons/dash-icon-04.svg')); ?>" alt="Dashboard Icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h3 style="font-size: 20px; color: hsl(240, 26%, 92%); font-weight: bold; text-align: center;background-color: #574ae6; padding: 10px; border-radius: 20px;">Situation Financière</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Poste</th>
                                <th>Total Net</th>
                                <th>Total Revers</th>
                                <th>Total Courant</th>
                                <th>Total Ancien</th>
                                <th>Total Écart</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $demandesFonds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $demande): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($demande->poste->nom); ?></td>
                                    <td><?php echo e(number_format($demande->total_net, 0, '', ' ')); ?></td>
                                    <td><?php echo e(number_format($demande->total_revers, 0, '', ' ')); ?></td>
                                    <td><?php echo e(number_format($demande->total_courant, 0, '', ' ')); ?></td>
                                    <td><?php echo e(number_format($demande->total_ancien, 0, '', ' ')); ?></td>
                                    <td><?php echo e(number_format($demande->total_courant - $demande->total_ancien, 0, '', ' ')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            
             </div>
            
            <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>