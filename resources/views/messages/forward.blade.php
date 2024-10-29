@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h4>Transférer le message</h4>
    <form action="{{ route('messages.forward.store', $originalMessage->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="subject" class="form-label">Sujet</label>
            <input type="text" name="subject" class="form-control" value="FW: {{ $originalMessage->subject }}" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Message</label>
            <textarea name="body" class="form-control" rows="5" required>{{ $originalMessage->body }}</textarea>
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
            <label for="attachments" class="form-label">Ajouter des pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-success">Envoyer</button>
        <a href="{{ route('messages.show', $originalMessage->id) }}" class="btn btn-secondary">Annuler</a>
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
