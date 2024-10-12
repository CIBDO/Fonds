@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action active">Boîte de Réception</a>
                <a href="{{ route('messages.sent') }}" class="list-group-item list-group-item-action">Boîte d'Envoi</a>
                {{-- <a href="{{ route('messages.trash') }}" class="list-group-item list-group-item-action">Corbeille</a> --}}
                <a href="{{ route('messages.create') }}" class="list-group-item list-group-item-action">Nouveau Message</a>
            </div>
        </div>
        <div class="col-md-9">
            <h2>Boîte de Réception</h2>
            <div class="list-group">
                @foreach($messages as $message)
                    <a href="{{ route('messages.show', $message->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $message->status == 'unread' ? 'bg-light' : '' }}">
                        <div>
                            <h5 class="mb-1">{{ $message->subject }}</h5>
                            <p class="mb-1">
                                <strong>De:</strong> {{ $message->sender->name ?? 'Expéditeur inconnu' }}<br>
                                <strong>À:</strong> 
                                @foreach($message->recipients as $recipient)
                                    <span class="badge bg-secondary">{{ $recipient->name }}</span>{{ !$loop->last ? ', ' : '' }}
                                @endforeach
                                <br>
                                <strong>Date et Heure d'envoi :</strong> {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i:s') : 'Non spécifiée' }}

                            </p>
                            @if($message->attachments->isNotEmpty())
                                <span class="badge bg-info">Pièce jointe</span>
                            @endif
                        </div>
                        <span class="badge bg-secondary">{{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
