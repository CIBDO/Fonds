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
    <h2 class="my-4">Modifier la Demande de Fonds</h2>
    <form method="POST" action="{{ route('demandes-fonds.update', $demande->id) }}">
        @csrf
        @method('PUT')

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date">Date :</label>
                    <input type="date" name="date" class="form-control" value="{{ $demande->date }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="date_reception">Date de Réception Salaire :</label>
                    <input type="date" name="date_reception" class="form-control" value="{{ $demande->date_reception }}" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="mois">Mois :</label>
                    <select name="mois" class="form-select" required>
                        <option value="" disabled selected>-- Sélectionnez un mois --</option>
                        <option value="Janvier" {{ $demande->mois == 'Janvier' ? 'selected' : '' }}>Janvier</option>
                        <option value="Février" {{ $demande->mois == 'Février' ? 'selected' : '' }}>Février</option>
                        <option value="Mars" {{ $demande->mois == 'Mars' ? 'selected' : '' }}>Mars</option>
                        <option value="Avril" {{ $demande->mois == 'Avril' ? 'selected' : '' }}>Avril</option>
                        <option value="Mai" {{ $demande->mois == 'Mai' ? 'selected' : '' }}>Mai</option>
                        <option value="Juin" {{ $demande->mois == 'Juin' ? 'selected' : '' }}>Juin</option>
                        <option value="Juillet" {{ $demande->mois == 'Juillet' ? 'selected' : '' }}>Juillet</option>
                        <option value="Aout" {{ $demande->mois == 'Aout' ? 'selected' : '' }}>Août</option>
                        <option value="Septembre" {{ $demande->mois == 'Septembre' ? 'selected' : '' }}>Septembre</option>
                        <option value="Octobre" {{ $demande->mois == 'Octobre' ? 'selected' : '' }}>Octobre</option>
                        <option value="Novembre" {{ $demande->mois == 'Novembre' ? 'selected' : '' }}>Novembre</option>
                        <option value="Decembre" {{ $demande->mois == 'Decembre' ? 'selected' : '' }}>Décembre</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="annee">Année :</label>
                    <input type="number" name="annee" class="form-control" value="{{ $demande->annee }}" required>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="poste_id">Poste/Service :</label>
                    <select name="poste_id" class="form-control" required>
                        @foreach($postes as $poste)
                            <option value="{{ $poste->id }}" {{ $demande->poste_id == $poste->id ? 'selected' : '' }}>{{ $poste->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="montant_disponible">Recettes Douanières :</label>
                    <input type="text" id="montant_disponible" name="montant_disponible" class="form-control" value="0" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="solde">Solde :</label>
                    <input type="text" id="solde" name="solde" class="form-control" value="0" readonly>
                </div>
            </div>
            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="user_id">Utilisateur :</label>
                    <input type="text" id="user_id_display" class="form-control" value="{{ Auth::user()->name }}" readonly>
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                </div>
            </div>

            <input type="hidden" name="status" value="{{ $demande->status }}">
        </div>
        @include('demandes._edit')

        <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            // Sélectionner tous les champs numériques
            const numericFields = document.querySelectorAll('input[type="text"], .net, .revers, .total_courant, .ancien_salaire, .total_demande');

            numericFields.forEach(field => {
                // Enlever le formatage avant l'envoi
                field.value = field.value.replace(/\s/g, '').replace(',', '.');
            });
        });
        </script>

    </form>
</div>

@endsection
