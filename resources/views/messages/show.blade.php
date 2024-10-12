@extends('layouts.master')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4>{{ $message->subject }}</h4>
            <span class="badge bg-secondary">{{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}</span>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3">
                <img src="{{ asset('assets/img/profiles/' . ($message->sender->avatar ?: 'Avatar-01.png')) }}" alt="Avatar" class="rounded-circle" style="width: 40px; height: 40px;">
                <div class="ms-3">
                    <strong>De:</strong> {{ $message->sender->name ?? 'Expéditeur inconnu' }}<br>
                    <strong>À:</strong> 
                    @foreach($message->recipients as $recipient)
                        <span class="badge bg-secondary">{{ $recipient->name }}</span>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
            <p><strong>Date et Heure d'envoi :</strong> {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i:s') : 'Non spécifiée' }}</p>
            <p><strong>Date et Heure de réception :</strong> {{ $message->received_at ? \Carbon\Carbon::parse($message->received_at)->format('d/m/Y H:i:s') : 'Non spécifiée' }}</p>

            <p>{{ $message->body }}</p>

            @if($message->attachments->isNotEmpty())
                <h5>Pièces jointes :</h5>
                <ul>
                    @foreach($message->attachments as $attachment)
                        <li>
                            <span>{{ $attachment->file_name }}</span>
                            <!-- Modifier le chemin pour utiliser le lien symbolique -->
                            <a href="{{ asset('storage/' . $attachment->file_name) }}" class="btn btn-sm btn-secondary" download>
                                <i class="fas fa-download"></i> Télécharger
                            </a>
                    
                            @if (in_array(pathinfo($attachment->file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'pdf']))
                                <button class="btn btn-sm btn-info" onclick="previewAttachment('{{ asset('storage/' . $attachment->file_name) }}')">
                                    Aperçu <i class="fas fa-eye"></i>
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p>Aucune pièce jointe.</p>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('messages.reply', $message->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-reply"></i> Répondre
            </a>
            <a href="{{ route('messages.sent') }}" class="btn btn-sm btn-secondary">
                Retour à la Boîte d'Envoi
            </a>
        </div>
    </div>
</div>

<!-- Modal d'aperçu -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Aperçu de la pièce jointe</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="attachmentPreview" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function previewAttachment(url) {
    document.getElementById('attachmentPreview').src = url;
    $('#previewModal').modal('show');
}
</script>
@endsection
