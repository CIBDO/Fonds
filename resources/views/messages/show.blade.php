@extends('layouts.master')

@section('content')
<div class="container">
    <h1>{{ $message->subject }}</h1>
    <p>{{ $message->body }}</p>
    <small>
        De: {{ $message->sender->name ?? 'Expéditeur inconnu' }} |
        À: {{ $message->receiver->name ?? 'Destinataire inconnu' }}
    </small>

    @if($message->attachments && $message->attachments->isNotEmpty())
    <h5>Pièces jointes:</h5>
    <ul>
        @foreach($message->attachments as $attachment)
            <li>
                <a href="{{ Storage::url($attachment->filepath) }}" target="_blank">{{ $attachment->filename }}</a>
            </li>
        @endforeach
    </ul>
@else
    <p>Aucune pièce jointe disponible.</p>
@endif

    <a href="{{ route('messages.sent') }}" class="btn btn-secondary">Retour</a>
</div>
@endsection
