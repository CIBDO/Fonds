@extends('layouts.master')

@section('content')
@if(session('success'))
       <div class="alert alert-success">
           {{ session('success') }}
       </div>
@endif

@if(session('error'))
       <div class="alert alert-danger">
           {{ session('error') }}
       </div>
@endif
@if (session('message_erreur'))
    <div class="alert alert-danger">
        {{ session('message_erreur') }}
    </div>
@endif
<div class="container">
    <h2 class="my-4" style="text-align: center; color: #ebf0f4; background-color:
     #3d5ee1; padding: 20px; border-radius: 10px; font-weight: bold; font-size: 22px; font-family:Georgia, 'Times New Roman', Times, serif">Demande de fonds</h2>
    <!-- Formulaire pour envoyer la demande de fonds -->
    <form method="POST" action="{{ route('demandes-fonds.store') }}" >
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
                    <input type="number" name="annee" class="form-control" value="{{ now()->format('Y') }}" required>
                </div>
            </div>
            <!-- Champ Service -->
            <div class="col-md-4">
                <div class="form-group">
                    <label for="poste">Poste/Service :</label>
                    <select name="poste_id" class="form-select" disabled>
                        <option value="" disabled>-- Sélectionnez un poste --</option>
                        @foreach($postes as $poste)
                            <option value="{{ $poste->id }}" {{ Auth::user()->poste_id == $poste->id ? 'selected' : '' }}>
                                {{ $poste->nom }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="poste_id" value="{{ Auth::user()->poste_id }}">
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
        @include('demandes._form')
        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" id="total_net" name="total_net" value="0">
        <input type="hidden" id="total_revers" name="total_revers" value="0">
        <input type="hidden" id="total_courant" name="total_courant" value="0">

        <!-- Champs pour Montant Disponible et Solde -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="montant_disponible">Recettes en Douanes :</label>
                    <input type="text" id="montant_disponible" name="montant_disponible" class="form-control" value="0" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="solde">Montant de la Demande:</label>
                    <input type="text" id="solde" name="solde" class="form-control" value="0" readonly>
                </div>
            </div>

        </div>
        <div class="alert alert-info" style="margin-bottom: 20px;">
            <strong>Important !</strong> Veuillez vérifier toutes les informations avant de soumettre la demande. Après soumission, vous ne pourrez plus modifier ces informations.
        </div>

        <div class="button-container" style="text-align: center; margin-top: 20px;">
            <button type="submit" class="submit-button">Soumettre la demande</button>
        </div>
        {{-- <script>
            document.querySelector('form').addEventListener('submit', function(e) {
                // Sélectionner tous les champs numériques
                const numericFields = document.querySelectorAll('input[type="text"], .net, .revers, .total_courant, .ancien_salaire, .total_demande');

                numericFields.forEach(field => {
                    // Enlever le formatage avant l'envoi
                    field.value = field.value.replace(/\s/g, '').replace(',', '.');
                });
            });
            </script> --}}
    </form>
</div>

{{-- <script>
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

</script> --}}

 {{-- <script>
document.addEventListener('DOMContentLoaded', function () {
    // Fonction pour formater les nombres
    function formatNumber(value) {
        // Supprimer tout ce qui n'est pas un chiffre
        let number = value.toString().replace(/[^\d]/g, '');
        // Formatter avec les espaces comme séparateurs de milliers
        return number.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    // Fonction pour enlever le formatage
    function unformatNumber(value) {
        return value.toString().replace(/\s/g, '');
    }

    // Appliquer le formatage à tous les champs numériques
    function applyFormatting(element) {
        if (element.value) {
            let unformatted = unformatNumber(element.value);
            let formatted = formatNumber(unformatted);
            element.value = formatted;
        }
    }

    // Sélectionner tous les champs d'entrée numériques
    const numericInputs = document.querySelectorAll('input[type="number"]');

    numericInputs.forEach(input => {
        // Changer le type en "text" pour permettre le formatage
        input.type = 'text';

        // Formatage initial
        applyFormatting(input);

        // Gérer la saisie
        input.addEventListener('input', function(e) {
            let cursorPosition = e.target.selectionStart;
            let oldLength = e.target.value.length;

            applyFormatting(e.target);

            // Ajuster la position du curseur
            let newLength = e.target.value.length;
            let newPosition = cursorPosition + (newLength - oldLength);
            e.target.setSelectionRange(newPosition, newPosition);
        });

        // Nettoyer avant la soumission du formulaire
        input.form.addEventListener('submit', function(e) {
            numericInputs.forEach(input => {
                input.value = unformatNumber(input.value);
            });
        });
    });

    // Recalculer les totaux avec le formatage
    function calculateTotals() {
        let totalNet = 0;
        let totalRevers = 0;
        let totalCourant = 0;
        let totalSalaireAncien = 0;
        let totalDemande = 0;

        document.querySelectorAll('.net').forEach((field, index) => {
            const net = parseFloat(unformatNumber(field.value)) || 0;
            const revers = parseFloat(unformatNumber(document.querySelectorAll('.revers')[index].value)) || 0;
            const ancien = parseFloat(unformatNumber(document.querySelectorAll('.ancien_salaire')[index].value)) || 0;

            const courant = net + revers;
            const demande = courant - ancien;

            document.querySelectorAll('.total_courant')[index].value = formatNumber(courant.toString());
            document.querySelectorAll('.total_demande')[index].value = formatNumber(demande.toString());

            totalNet += net;
            totalRevers += revers;
            totalCourant += courant;
            totalSalaireAncien += ancien;
            totalDemande += demande;
        });

        // Mettre à jour les totaux avec formatage
        document.getElementById('total_net').value = formatNumber(totalNet.toString());
        document.getElementById('total_revers').value = formatNumber(totalRevers.toString());
        document.getElementById('total_courant').value = formatNumber(totalCourant.toString());
        document.getElementById('total_salaire_ancien').value = formatNumber(totalSalaireAncien.toString());
        document.getElementById('total_demande').value = formatNumber(totalDemande.toString());

        // Calculer le solde
        const montantDisponible = parseFloat(unformatNumber(document.getElementById('montant_disponible').value)) || 0;
        const solde = totalCourant - montantDisponible;
        document.getElementById('solde').value = formatNumber(solde.toString());
    }

    // Ajouter les écouteurs d'événements pour les calculs
    document.querySelectorAll('.net, .revers, .ancien_salaire').forEach(input => {
        input.addEventListener('input', calculateTotals);
    });

    document.getElementById('montant_disponible').addEventListener('input', calculateTotals);

    // Calculer les totaux initiaux
    calculateTotals();
});

</script>  --}}
@endsection
