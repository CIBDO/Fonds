<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
       <div class="alert alert-success">
           <?php echo e(session('success')); ?>

       </div>
<?php endif; ?>

<?php if(session('error')): ?>
       <div class="alert alert-danger">
           <?php echo e(session('error')); ?>

       </div>
<?php endif; ?>
<div class="container">
    <h2 class="my-4" style="text-align: center; color: #ebf0f4; background-color:
     #3d5ee1; padding: 20px; border-radius: 10px; font-weight: bold; font-size: 22px; font-family:Georgia, 'Times New Roman', Times, serif">Demande de fonds</h2>
    <!-- Formulaire pour envoyer la demande de fonds -->
    <form method="POST" action="<?php echo e(route('demandes-fonds.store')); ?>">
        <?php echo csrf_field(); ?>
        <!-- En-tête avec la date, le mois et l'année -->
        <div class="row mb-4">
            <!-- Les trois premiers champs sur la même ligne -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date">Date :</label>
                    <input type="date" name="date" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date_reception">Date de Réception Salaire :</label>
                    <input type="date" name="date_reception" class="form-control" value="<?php echo e(now()->format('Y-m-d')); ?>" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mois">Mois :</label>
                    <select name="mois" class="form-select" required>
                        <option value="" disabled selected>-- Sélectionnez un mois --</option>
                        <option value="Janvier">Janvier</option>
                        <option value="Fevrier">Février</option>
                        <option value="Mars">Mars</option>
                        <option value="Avril">Avril</option>
                        <option value="Mai">Mai</option>
                        <option value="Juin">Juin</option>
                        <option value="Juillet">Juillet</option>
                        <option value="Aout">Août</option>
                        <option value="Septembre">Septembre</option>
                        <option value="Octobre">Octobre</option>
                        <option value="Novembre">Novembre</option>
                        <option value="Decembre">Décembre</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Champ Année -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="annee">Année :</label>
                    <input type="number" name="annee" class="form-control" value="<?php echo e(now()->format('Y')); ?>" required>
                </div>
            </div>

            <!-- Champ Service -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="poste">Poste/Service :</label>
                    <select name="poste_id" class="form-select" disabled>
                        <option value="" disabled>-- Sélectionnez un poste --</option>
                        <?php $__currentLoopData = $postes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poste): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($poste->id); ?>" <?php echo e(Auth::user()->poste_id == $poste->id ? 'selected' : ''); ?>>
                                <?php echo e($poste->nom); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="hidden" name="poste_id" value="<?php echo e(Auth::user()->poste_id); ?>">
                </div>
            </div>

            <!-- Champ Utilisateur connecté -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="user">Agent Traitant :</label>
                    <input type="text" class="form-control" value="<?php echo e(Auth::user()->name); ?>" readonly>
                </div>
            </div>

            <input type="hidden" name="status" value="en_attente">
        </div>

        <!-- Tableau des catégories de salariés -->
        <?php echo $__env->make('demandes._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <input type="hidden" name="user_id" value="<?php echo e(Auth::user()->id); ?>">
        <input type="hidden" id="total_net" name="total_net" value="0">
        <input type="hidden" id="total_revers" name="total_revers" value="0">
        <input type="hidden" id="total_courant" name="total_courant" value="0">

        <!-- Champs pour Montant Disponible et Solde -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="montant_disponible">Recettes en Douanes :</label>
                    <input type="number" id="montant_disponible" name="montant_disponible" class="form-control" value="0" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="solde">Montant de la Demande:</label>
                    <input type="number" id="solde" name="solde" class="form-control" value="0" readonly>
                </div>
            </div>

        </div>

        <!-- Bouton d'envoi -->
        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button type="submit" class="submit-button">Soumettre la demande</button>
        </div>

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

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\BDO\Desktop\Fonds\resources\views/demandes/create.blade.php ENDPATH**/ ?>