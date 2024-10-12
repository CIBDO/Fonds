@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h1>Boîte d'Envoi</h1>
    @if($messages->isEmpty())
        <p class="text-muted">Aucun message envoyé.</p>
    @else
        <ul class="list-group">
            @foreach($messages as $message)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5>{{ $message->subject }}</h5>
                        <p>{{ Str::limit($message->body, 100) }}</p>
                        <small>De: {{ $message->sender->name ?? 'Expéditeur inconnu' }}</small><br>
                        <small>À: 
                            @foreach($message->recipients as $recipient)
                                <span class="badge bg-info">{{ $recipient->name }}</span>
                            @endforeach
                        </small>
                    </div>
                    <a href="{{ route('messages.show', $message->id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Voir
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
