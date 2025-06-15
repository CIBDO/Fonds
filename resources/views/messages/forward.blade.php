@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <div class="card shadow rounded-4">
                <div class="card-header bg-white border-0 rounded-top-4">
                    <h4 class="mb-0 fw-bold"><i class="fas fa-share"></i> Transférer le message</h4>
                </div>
                <div class="card-body">
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
                    <form action="{{ route('messages.forward.store', $originalMessage->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="receiver_ids" class="form-label"><i class="fas fa-users"></i> Destinataires</label>
                            <select name="receiver_ids[]" id="receiver_ids" class="form-select" multiple required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Vous pouvez sélectionner plusieurs destinataires.</small>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label"><i class="fas fa-heading"></i> Sujet</label>
                            <input type="text" name="subject" class="form-control" value="FW: {{ $originalMessage->subject }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="body" class="form-label"><i class="fas fa-align-left"></i> Message</label>
                            <textarea name="body" class="form-control" rows="5" required>{{ $originalMessage->body }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Ajouter des pièces jointes</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-success fw-bold px-4">
                                <i class="fas fa-paper-plane"></i> Envoyer
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
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
