@extends('layouts.master')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
       @endforeach
          </ul>
         </div>
    @endif
<div class="container">
    <h2 class="my-4">Demande de fonds</h2>
    <!-- Formulaire pour envoyer la demande de fonds -->
    <form method="POST" action="{{ route('demandes-fonds.store') }}">
        @csrf
        <!-- En-tête avec la date, le mois et l'année -->
        <div class="row mb-4">
            <!-- Les trois premiers champs sur la même ligne -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date">Date :</label>
                    <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date_reception">Date de Réception Salaire :</label>
                    <input type="date" name="date_reception" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mois">Mois :</label>
                    <input type="text" name="mois" class="form-control" placeholder="Ex : Septembre" required>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <!-- Champ Année -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="annee">Année :</label>
                    <input type="number" name="annee" class="form-control" value="{{ now()->format('Y') }}" required>
                </div>
            </div>
        
            <!-- Champ Service -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="poste">Poste/Service :</label>
                    <select name="poste_id" class="form-control" required>
                        <option value="" disabled selected>-- Sélectionnez un poste --</option>
                        @foreach($postes as $poste)
                            <option value="{{ $poste->id }}">{{ $poste->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        
            <!-- Champ Utilisateur connecté -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="user">Agent Traitant :</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                </div>
            </div>
            
            <input type="hidden" name="status" value="en_attente">
        </div>

        <!-- Tableau des catégories de salariés -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Salaire Net</th>
                    <th>Revers/ Salaire</th>
                    <th>Total mois courant</th>
                    <th>Salaire mois antérieur</th>
                    <th>Ecart</th>
                </tr>
            </thead>
            <tbody>
                <!-- Fonctionnaires BCS -->
                <tr>
                    <td>Fonctionnaires BCS</td>
                    <td><input type="number" name="fonctionnaires_bcs_net" class="form-control net"></td>
                    <td><input type="number" name="fonctionnaires_bcs_revers" class="form-control revers"></td>
                    <td><input type="number" name="fonctionnaires_bcs_total_courant" class="form-control total_courant"></td>
                    <td><input type="number" name="fonctionnaires_bcs_salaire_ancien" class="form-control ancien_salaire"></td>
                    <td><input type="number" name="fonctionnaires_bcs_total_demande" class="form-control total_demande" readonly></td>
                </tr>

                <!-- Personnel Collectivité Santé -->
                <tr>
                    <td>Personnel Collectivité Santé</td>
                    <td><input type="number" name="collectivite_sante_net" class="form-control net"></td>
                    <td><input type="number" name="collectivite_sante_revers" class="form-control revers"></td>
                    <td><input type="number" name="collectivite_sante_total_courant" class="form-control total_courant"></td>
                    <td><input type="number" name="collectivite_sante_salaire_ancien" class="form-control ancien_salaire"></td>
                    <td><input type="number" name="collectivite_sante_total_demande" class="form-control total_demande" readonly></td>
                </tr>

                <!-- Personnel Collectivité Éducation -->
                <tr>
                    <td>Personnel Collectivité Éducation</td>
                    <td><input type="number" name="collectivite_education_net" class="form-control net"></td>
                    <td><input type="number" name="collectivite_education_revers" class="form-control revers"></td>
                    <td><input type="number" name="collectivite_education_total_courant" class="form-control total_courant"></td>
                    <td><input type="number" name="collectivite_education_salaire_ancien" class="form-control ancien_salaire"></td>
                    <td><input type="number" name="collectivite_education_total_demande" class="form-control total_demande" readonly></td>
                </tr>

                <!-- Personnels Saisonniers -->
                <tr>
                    <td>Personnels Saisonniers</td>
                    <td><input type="number" name="personnels_saisonniers_net" class="form-control net"></td>
                    <td><input type="number" name="personnels_saisonniers_revers" class="form-control revers"></td>
                    <td><input type="number" name="personnels_saisonniers_total_courant" class="form-control total_courant"></td>
                    <td><input type="number" name="personnels_saisonniers_salaire_ancien" class="form-control ancien_salaire"></td>
                    <td><input type="number" name="personnels_saisonniers_total_demande" class="form-control total_demande" readonly></td>
                </tr>

                <!-- EPN -->
                <tr>
                    <td>EPN</td>
                    <td><input type="number" name="epn_net" class="form-control net"></td>
                    <td><input type="number" name="epn_revers" class="form-control revers"></td>
                    <td><input type="number" name="epn_total_courant" class="form-control total_courant"></td>
                    <td><input type="number" name="epn_salaire_ancien" class="form-control ancien_salaire"></td>
                    <td><input type="number" name="epn_total_demande" class="form-control total_demande" readonly></td>
                </tr>

                <!-- Ligne de total automatique -->
                <tr>
                    <td>Total</td>
                    <td><input type="number" name="total_net" class="form-control" id="total_net" readonly></td>
                    <td><input type="number" name="total_revers" class="form-control" id="total_revers" readonly></td>
                    <td><input type="number" name="total_courant" class="form-control" id="total_courant" readonly></td>
                    <td><input type="number" name="total_salaire_ancien" class="form-control" id="total_salaire_ancien" readonly></td>
                    <td><input type="number" name="total_demande" class="form-control" id="total_demande" readonly></td>
                </tr>
            </tbody>
        </table>
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <!-- Bouton d'envoi -->
        <button type="submit" class="btn btn-primary">Soumettre la demande</button>
    </form>
</div>

<!-- Script pour calculer automatiquement les totaux -->
{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionner tous les champs d'entrée pertinents pour le calcul
        const netFields = document.querySelectorAll('.net');
        const reversFields = document.querySelectorAll('.revers');
        const totalCourantFields = document.querySelectorAll('.total_courant');
        const salaireAncienFields = document.querySelectorAll('.ancien_salaire');
        const totalDemandeFields = document.querySelectorAll('.total_demande');

        // Les champs totaux en bas
        const totalNetField = document.getElementById('total_net');
        const totalReversField = document.getElementById('total_revers');
        const totalCourantField = document.getElementById('total_courant');
        const totalSalaireAncienField = document.getElementById('total_salaire_ancien');
        const totalDemandeField = document.getElementById('total_demande');

        // Fonction pour recalculer les totaux
        function calculateTotals() {
            let totalNet = 0;
            let totalRevers = 0;
            let totalCourant = 0;
            let totalSalaireAncien = 0;
            let totalDemande = 0;

            netFields.forEach((field, index) => {
                const net = parseFloat(field.value) || 0;
                const revers = parseFloat(reversFields[index].value) || 0;
                const courant = parseFloat(totalCourantFields[index].value) || 0;
                const ancien = parseFloat(salaireAncienFields[index].value) || 0;
                const demande = courant - ancien;

                totalNet += net;
                totalRevers += revers;
                totalCourant += courant;
                totalSalaireAncien += ancien;
                totalDemande += demande;

                totalDemandeFields[index].value = demande.toFixed(0);
            });

            totalNetField.value = totalNet.toFixed(0);
            totalReversField.value = totalRevers.toFixed(0);
            totalCourantField.value = totalCourant.toFixed(0);
            totalSalaireAncienField.value = totalSalaireAncien.toFixed(0);
            totalDemandeField.value = totalDemande.toFixed(0);
        }

        // Ajouter des écouteurs pour recalculer lorsque les valeurs changent
        netFields.forEach(field => field.addEventListener('input', calculateTotals));
        reversFields.forEach(field => field.addEventListener('input', calculateTotals));
        totalCourantFields.forEach(field => field.addEventListener('input', calculateTotals));
        salaireAncienFields.forEach(field => field.addEventListener('input', calculateTotals));

        // Calculer les totaux au chargement de la page
        calculateTotals();
    });
</script> --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sélectionner tous les champs d'entrée pertinents pour le calcul
        const netFields = document.querySelectorAll('.net');
        const reversFields = document.querySelectorAll('.revers');
        const totalCourantFields = document.querySelectorAll('.total_courant');
        const salaireAncienFields = document.querySelectorAll('.ancien_salaire');
        const totalDemandeFields = document.querySelectorAll('.total_demande');

        // Les champs totaux en bas
        const totalNetField = document.getElementById('total_net');
        const totalReversField = document.getElementById('total_revers');
        const totalCourantField = document.getElementById('total_courant');
        const totalSalaireAncienField = document.getElementById('total_salaire_ancien');
        const totalDemandeField = document.getElementById('total_demande');

        // Fonction pour recalculer les totaux
        function calculateTotals() {
            let totalNet = 0;
            let totalRevers = 0;
            let totalCourant = 0;
            let totalSalaireAncien = 0;
            let totalDemande = 0;

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
            });

            // Mettre à jour les champs de total
            totalNetField.value = totalNet.toFixed(0);
            totalReversField.value = totalRevers.toFixed(0);
            totalCourantField.value = totalCourant.toFixed(0);
            totalSalaireAncienField.value = totalSalaireAncien.toFixed(0);
            totalDemandeField.value = totalDemande.toFixed(0);
        }

        // Ajouter des écouteurs pour recalculer lorsque les valeurs changent
        netFields.forEach(field => field.addEventListener('input', calculateTotals));
        reversFields.forEach(field => field.addEventListener('input', calculateTotals));
        salaireAncienFields.forEach(field => field.addEventListener('input', calculateTotals));

        // Calculer les totaux au chargement de la page
        calculateTotals();
    });
</script>

@endsection
