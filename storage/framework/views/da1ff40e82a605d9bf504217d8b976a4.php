

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
<div class="container">
    <h2 class="my-4">Modifier la Demande de Fonds</h2>
    <form method="POST" action="<?php echo e(route('demandes-fonds.update', $demande->id)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date">Date :</label>
                    <input type="date" name="date" class="form-control" value="<?php echo e($demande->date); ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date_reception">Date de Réception Salaire :</label>
                    <input type="date" name="date_reception" class="form-control" value="<?php echo e($demande->date_reception); ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mois">Mois :</label>
                    <select name="mois" class="form-select" required>
                        <option value="" disabled selected>-- Sélectionnez un mois --</option>
                        <option value="Janvier" <?php echo e($demande->mois == 'Janvier' ? 'selected' : ''); ?>>Janvier</option>
                        <option value="Février" <?php echo e($demande->mois == 'Février' ? 'selected' : ''); ?>>Février</option>
                        <option value="Mars" <?php echo e($demande->mois == 'Mars' ? 'selected' : ''); ?>>Mars</option>
                        <option value="Avril" <?php echo e($demande->mois == 'Avril' ? 'selected' : ''); ?>>Avril</option>
                        <option value="Mai" <?php echo e($demande->mois == 'Mai' ? 'selected' : ''); ?>>Mai</option>
                        <option value="Juin" <?php echo e($demande->mois == 'Juin' ? 'selected' : ''); ?>>Juin</option>
                        <option value="Juillet" <?php echo e($demande->mois == 'Juillet' ? 'selected' : ''); ?>>Juillet</option>
                        <option value="Aout" <?php echo e($demande->mois == 'Aout' ? 'selected' : ''); ?>>Août</option>
                        <option value="Septembre" <?php echo e($demande->mois == 'Septembre' ? 'selected' : ''); ?>>Septembre</option>
                        <option value="Octobre" <?php echo e($demande->mois == 'Octobre' ? 'selected' : ''); ?>>Octobre</option>
                        <option value="Novembre" <?php echo e($demande->mois == 'Novembre' ? 'selected' : ''); ?>>Novembre</option>
                        <option value="Decembre" <?php echo e($demande->mois == 'Decembre' ? 'selected' : ''); ?>>Décembre</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="annee">Année :</label>
                    <input type="number" name="annee" class="form-control" value="<?php echo e($demande->annee); ?>" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="poste_id">Poste/Service :</label>
                    <select name="poste_id" class="form-control" required>
                        <?php $__currentLoopData = $postes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poste): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($poste->id); ?>" <?php echo e($demande->poste_id == $poste->id ? 'selected' : ''); ?>><?php echo e($poste->nom); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="montant_disponible">Montant Disponible :</label>
                    <input type="number" id="montant_disponible" name="montant_disponible" class="form-control" value="0" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="solde">Solde :</label>
                    <input type="number" id="solde" name="solde" class="form-control" value="0" readonly>
                </div>
            </div>
            <input type="hidden" name="user_id" value="<?php echo e(Auth::user()->id); ?>">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="user_id">Utilisateur :</label>
                    <input type="text" id="user_id_display" class="form-control" value="<?php echo e(Auth::user()->name); ?>" readonly>
                    <input type="hidden" name="user_id" value="<?php echo e(Auth::user()->id); ?>">
                </div>
            </div>
            
            <input type="hidden" name="status" value="<?php echo e($demande->status); ?>">
        </div>
        <?php echo $__env->make('demandes._edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionner tous les champs d'entrée pertinents pour le calcul
        const netFields = document.querySelectorAll('.net');
        const reversFields = document.querySelectorAll('.revers');
        const totalCourantFields = document.querySelectorAll('.total_courant');
        const salaireAncienFields = document.querySelectorAll('.ancien_salaire');
        const totalDemandeFields = document.querySelectorAll('.total_demande');
        const totalAncienFields = document.querySelectorAll('.total_ancien');

        // Les champs totaux en bas
        const totalNetField = document.getElementById('total_net');
        const totalReversField = document.getElementById('total_revers');
        const totalCourantField = document.getElementById('total_courant');
        const totalSalaireAncienField = document.getElementById('total_salaire_ancien');
        const totalDemandeField = document.getElementById('total_demande');
        const totalAncienField = document.getElementById('total_ancien');

        // Champs pour Montant Disponible et Solde
        const montantDisponibleField = document.getElementById('montant_disponible');
        const soldeField = document.getElementById('solde');

        // Fonction pour recalculer les totaux
        function calculateTotals() {
            let totalNet = 0;
            let totalRevers = 0;
            let totalCourant = 0;
            let totalSalaireAncien = 0;
            let totalDemande = 0;
            let totalAncien = 0;

            netFields.forEach((field, index) => {
                const net = parseFloat(field.value) || 0;
                const revers = parseFloat(reversFields[index].value) || 0;

                // Calculer le Total mois courant
                const courant = net + revers;

                // Récupérer le salaire mois antérieur
                const ancien = parseFloat(salaireAncienFields[index].value) || 0;
                const demande = courant - ancien;

                // Assigner les valeurs calculées
                totalCourantFields[index].value = courant.toFixed(0);
                totalDemandeFields[index].value = demande.toFixed(0);

                // Mettre à jour les totaux
                totalNet += net;
                totalRevers += revers;
                totalCourant += courant;
                totalSalaireAncien += ancien;
                totalDemande += demande;
                totalAncien += ancien;
            });

            // Mettre à jour les champs de total
            totalNetField.value = totalNet.toFixed(0);
            totalReversField.value = totalRevers.toFixed(0);
            totalCourantField.value = totalCourant.toFixed(0);
            totalSalaireAncienField.value = totalSalaireAncien.toFixed(0);
            totalDemandeField.value = totalDemande.toFixed(0);
            totalAncienField.value = totalAncien.toFixed(0);
        }

        // Fonction pour calculer le solde
        function calculateSolde() {
            const totalCourant = parseFloat(totalCourantField.value) || 0;
            const montantDisponible = parseFloat(montantDisponibleField.value) || 0;
            const solde = totalCourant - montantDisponible;

            soldeField.value = solde.toFixed(0);
        }

        // Ajouter des écouteurs pour recalculer lorsque les valeurs changent
        netFields.forEach(field => field.addEventListener('input', calculateTotals));
        reversFields.forEach(field => field.addEventListener('input', calculateTotals));
        salaireAncienFields.forEach(field => field.addEventListener('input', calculateTotals));
        montantDisponibleField.addEventListener('input', calculateSolde);

        // Calculer les totaux au chargement de la page
        calculateTotals();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/demandes/edit.blade.php ENDPATH**/ ?>