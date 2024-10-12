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
    <h1><i class="fas fa-envelope"></i> Nouveau Message</h1>

    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="subject" class="form-label"><i class="fas fa-heading"></i> Objet</label>
            <input type="text" name="subject" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label"><i class="fas fa-align-left"></i> Corps du message</label>
            <textarea name="body" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="destinataires" class="form-label"><i class="fas fa-users"></i> Destinataires</label>
            @if($users->isEmpty())
                <p class="text-muted">Aucun destinataire disponible.</p>
            @else
                <select name="receiver_ids[]" class="form-select" multiple required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <script>
                    $(document).ready(function() {
                        $('.form-select').select2({
                            placeholder: "Sélectionnez les destinataires",
                            allowClear: true
                        });
                    });
                </script>
            @endif
        </div>

        <div class="mb-3">
            <label for="attachments" class="form-label"><i class="fas fa-paperclip"></i> Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
    </form>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection
