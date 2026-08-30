@extends('layouts.email')

@section('title', 'Répondre au message')

@section('email-content')
<x-vuexy.card title="Répondre au message" icon="tabler-arrow-back-up">
    @if ($errors->any())
        <x-vuexy.alert type="danger" class="mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-vuexy.alert>
    @endif

    <div class="bg-lighter rounded p-3 mb-4">
        <h6 class="fw-medium mb-2">Message original</h6>
        <p class="mb-1"><strong>De :</strong> {{ $message->sender->name ?? 'Expéditeur inconnu' }}</p>
        <p class="mb-1"><strong>Sujet :</strong> {{ $message->subject }}</p>
        <p class="mb-0 text-body-secondary small">
            {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
        </p>
    </div>

    <form action="{{ route('messages.reply', $message->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label for="body" class="form-label">Votre réponse</label>
            <textarea name="body" id="body" class="form-control" rows="6" required placeholder="Écrivez votre réponse ici..."></textarea>
        </div>
        <div class="mb-4">
            <label for="attachments" class="form-label">Pièces jointes</label>
            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
            <small class="text-body-secondary">Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2 Mo par fichier)</small>
        </div>
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-label-secondary">Retour</a>
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-send me-1"></i>Envoyer la réponse
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
