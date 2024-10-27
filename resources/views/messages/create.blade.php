@extends('layouts.master')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erreur !</strong> Veuillez corriger les erreurs suivantes :
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="container mt-5">
    <h3 class="mb-4" style="font-weight: bold; color: #007bff; font-family: 'Geologica', sans-serif;"><i class="fas fa-envelope"></i> Nouveau Message</h3>

    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="subject" class="form-label"><i class="fas fa-heading"></i> Objet</label>
            <input type="text" name="subject" class="form-control" placeholder="Entrez l'objet du message" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label"><i class="fas fa-align-left"></i> Corps du message</label>
            <textarea name="body" class="form-control" rows="5" placeholder="Écrivez votre message ici..." required></textarea>
        </div>

        <div class="mb-3">
            <label for="destinataires" class="form-label"><i class="fas fa-users"></i> Destinataires</label>
            
            <!-- Champ select qui agit comme un déclencheur -->
            <select class="form-select" id="destinataireTrigger" onclick="toggleCheckboxList()">
                <option value="" >Cliquez pour sélectionner des destinataires</option>
            </select>
            
            @if($users->isEmpty())
                <p class="text-muted">Aucun destinataire disponible.</p>
            @else
                <!-- Liste des cases à cocher, masquée initialement -->
                <div id="checkboxList" style="display: none; margin-top: 10px;">
                    @foreach($users as $user)
                        <div>
                            <input type="checkbox" name="receiver_ids[]" value="{{ $user->id }}" class="form-check-input" id="user-{{ $user->id }}">
                            <label class="form-check-label" for="user-{{ $user->id }}">{{ $user->name }}</label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        

        <div class="mb-3">
            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
            <small class="form-text text-muted">Vous pouvez joindre plusieurs fichiers.</small>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function toggleCheckboxList() {
        // Récupère l'élément de la liste des cases à cocher
        const checkboxList = document.getElementById('checkboxList');
        
        // Bascule la visibilité de la liste
        if (checkboxList.style.display === 'none' || checkboxList.style.display === '') {
            checkboxList.style.display = 'block';
        } else {
            checkboxList.style.display = 'none';
        }
    }
</script>
@endsection