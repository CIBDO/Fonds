@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <div class="row">
        <!-- Barre latérale de navigation -->
        <div class="col-md-3 mb-4">
            <div class="list-group shadow-sm rounded">
                <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-inbox"></i> Boîte de Réception
                </a>
                <a href="{{ route('messages.sent') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-paper-plane"></i> Boîte d'Envoi
                </a>
                <a href="{{ route('messages.create') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-edit"></i> Nouveau Message
                </a>
            </div>
        </div>

        <!-- Boîte de réception -->
        <div class="col-md-9">
            <h2 class="mb-4">Boîte de Réception</h2>

            @if($messages->isEmpty())
                <div class="alert alert-info">Aucun message dans votre boîte de réception.</div>
            @else
                <div class="list-group shadow-sm">
                    @foreach($messages as $message)
                        <a href="{{ route('messages.show', $message->id) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3 {{ $message->status == 'unread' ? 'bg-light' : '' }}">

                            <!-- Détails du message -->
                            <div>
                                <h5 class="mb-1">
                                    {{ $message->subject }}
                                    @if($message->attachments->isNotEmpty())
                                        <i class="fas fa-paperclip ms-2 text-muted"></i>
                                    @endif
                                </h5>
                                <p class="text-muted mb-1 small">
                                    <strong>De :</strong> {{ $message->sender->name ?? 'Expéditeur inconnu' }}
                                </p>
                                <p class="text-muted mb-1 small">
                                    <strong>À :</strong> 
                                    @foreach($message->recipients as $recipient)
                                        <span class="badge bg-secondary">{{ $recipient->name }}</span>{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </p>
                                <p class="text-muted small">
                                    <strong>Envoyé le :</strong> {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i:s') : 'Non spécifiée' }}
                                </p>
                            </div>

                            <!-- Statut de lecture -->
                            <span class="badge {{ $message->status == 'unread' ? 'bg-warning' : 'bg-success' }}">
                                {{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
