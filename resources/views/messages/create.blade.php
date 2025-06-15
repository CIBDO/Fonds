@extends('layouts.master')

@section('content')
<!-- Modale de composition -->
<div class="modal fade show" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-modal="true" role="dialog" style="display:block; background:rgba(0,0,0,0.1);">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="composeModalLabel"><i class="fas fa-pen"></i> Nouveau message</h5>
          <a href="{{ url()->previous() }}" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
        </div>
        <div class="modal-body">
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erreur !</strong> Veuillez corriger les erreurs suivantes :
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <div class="mb-3">
            <label for="receiver_ids" class="form-label"><i class="fas fa-users"></i> Destinataires</label>
            <select name="receiver_ids[]" id="receiver_ids" class="form-select" multiple required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->poste->nom }}</option>
                @endforeach
            </select>
            <small class="form-text text-muted">Vous pouvez sélectionner plusieurs destinataires.</small>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label"><i class="fas fa-heading"></i> Objet</label>
            <input type="text" name="subject" class="form-control" placeholder="Entrez l'objet du message" required>
          </div>

          <div class="mb-3">
            <label for="body" class="form-label"><i class="fas fa-align-left"></i> Corps du message</label>
            <textarea name="body" class="form-control" rows="5" placeholder="Écrivez votre message ici..." required></textarea>
          </div>

          <div class="mb-3">
            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
            <small class="form-text text-muted">Vous pouvez joindre plusieurs fichiers (jpg, png, pdf, doc, xls, zip...)</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="fas fa-paper-plane"></i> Envoyer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script pour Select2 (optionnel pour une meilleure UX) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery) {
            $('#receiver_ids').select2({
                width: '100%',
                placeholder: 'Sélectionnez les destinataires',
                allowClear: true
            });
        }
    });
</script>
@endsection
