@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Répondre au message</h1>
    
    <form action="{{ route('messages.reply', $message->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="body" class="form-label">Corps du message</label>
            <textarea name="body" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="attachments" class="form-label">Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
    </form>

    <a href="{{ route('messages.index') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection
