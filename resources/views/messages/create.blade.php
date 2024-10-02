@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Nouveau Message</h1>

    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="subject" class="form-label">Sujet</label>
            <input type="text" name="subject" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Contenu</label>
            <textarea name="body" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="destinataires" class="form-label">Destinataires</label>
            <select name="receiver_ids[]" class="form-select" multiple required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="attachments" class="form-label">Pièces jointes</label>
            <input type="file" name="attachments[]" class="form-control" multiple>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>
@endsection
