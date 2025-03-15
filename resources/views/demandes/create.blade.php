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
    <form method="POST" action="{{ route('demandes-fonds.store') }}" id="demandeForm">
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
       
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('demandeForm');

        form.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        // ... autres scripts existants ...
    });
</script>
@endsection
