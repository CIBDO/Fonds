@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-paper-plane"></i> Boîte d'Envoi</h2>

    @if($messages->isEmpty())
        <div class="alert alert-secondary text-center" role="alert">
            <i class="fas fa-inbox"></i> Aucun message envoyé.
        </div>
    @else
        <ul class="list-group shadow-sm">
            @foreach($messages as $message)
                <li class="list-group-item d-flex justify-content-between align-items-center {{ $message->status == 'unread' ? 'bg-light' : '' }}">
                    <div>
                        <!-- Sujet et contenu abrégé du message -->
                        <h5 class="mb-1">
                            <i class="fas fa-envelope{{ $message->attachments->isNotEmpty() ? '-open-text' : '' }}"></i> 
                            {{ $message->subject }}
                        </h5>
                        <p class="text-muted mb-2">{{ Str::limit($message->body, 100) }}</p>
                        
                        <!-- Expéditeur et destinataires avec icône d'enveloppe pour chacun -->
                        <small>
                            <strong>De:</strong> {{ $message->sender->name ?? 'Expéditeur inconnu' }}<br>
                            <strong>À:</strong> 
                            @foreach($message->recipients as $recipient)
                                <span class="badge bg-secondary me-1">
                                    <i class="fas fa-user"></i> {{ $recipient->name }}
                                </span>
                            @endforeach
                        </small>

                        <!-- Icône pour pièce jointe si présente -->
                        @if($message->attachments->isNotEmpty())
                            <span class="badge bg-success mt-2">
                                <i class="fas fa-paperclip"></i> Pièce jointe
                            </span>
                        @endif
                    </div>

                    <!-- Boutons d'action pour chaque message -->
                    <div class="d-flex align-items-center">
                        <a href="{{ route('messages.show', $message->id) }}" class="btn btn-outline-primary btn-sm me-2">
                            <i class="fas fa-eye"></i> Voir
                        </a>
                       {{--  <form action="{{ route('messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce message ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash-alt"></i> Supprimer
                            </button>
                        </form> --}}
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</div>

<div class="pagination">
    {{ $messages->links() }}
</div>

@endsection
