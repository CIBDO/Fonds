@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-reply"></i> Répondre au message</h2>

    <!-- Formulaire de réponse -->
    <form action="{{ route('messages.reply', $message->id) }}" method="POST" enctype="multipart/form-data" class="shadow-sm p-4 rounded bg-light">
        @csrf

        <!-- Corps du message -->
        <div class="mb-3">
            <label for="body" class="form-label"><i class="fas fa-envelope"></i> Corps du message</label>
            <textarea name="body" class="form-control" rows="5" placeholder="Écrivez votre réponse ici..." required></textarea>
        </div>

        <!-- Champ de pièces jointes avec indicateur de fichiers sélectionnés -->
        <div class="mb-3">
            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple id="attachmentInput">
            <small class="form-text text-muted">Sélectionnez plusieurs fichiers si nécessaire (formats: jpg, jpeg, png, pdf, doc).</small>
            <div id="fileList" class="mt-2 text-muted small"></div>
        </div>

        <!-- Boutons de soumission et retour -->
        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Envoyer la réponse
            </button>
            <a href="{{ route('messages.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </form>
</div>

<!-- JavaScript pour afficher le nom des fichiers joints -->
<script>
    document.getElementById('attachmentInput').addEventListener('change', function () {
        const fileList = document.getElementById('fileList');
        fileList.innerHTML = '';

        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            fileList.innerHTML += `<span><i class="fas fa-file"></i> ${file.name}</span><br>`;
        }
    });
</script>
@endsection
