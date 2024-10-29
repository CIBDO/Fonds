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
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-envelope"></i> {{ $message->subject }}</h4>
            <span class="badge bg-{{ $message->status == 'unread' ? 'warning' : 'success' }}">
                {{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}
            </span>
        </div>

        <div class="card-body">
            <!-- Informations sur l'expéditeur et les destinataires -->
            <div class="d-flex align-items-center mb-3">
                <img src="{{ asset('assets/img/profiles/' . ($message->sender->avatar ?: 'Avatar-01.png')) }}" alt="Avatar"
                     class="rounded-circle" style="width: 50px; height: 50px;">
                <div class="ms-3">
                    <strong>De:</strong> {{ $message->sender->name ?? 'Expéditeur inconnu' }}<br>
                    <strong>À:</strong> 
                    @foreach($message->recipients as $recipient)
                        <span class="badge bg-secondary">{{ $recipient->name }}</span>{{ !$loop->last ? ', ' : '' }}
                    @endforeach
                </div>
            </div>
            
            <p><strong>Date et Heure d'envoi :</strong> 
                {{ $message->created_at ? $message->created_at->format('d/m/Y H:i:s') : 'Non défini' }}
            </p>
            
            <p><strong>Date et Heure de réception :</strong> 
                {{ $recipient && $recipient->pivot->received_at ? 
                    \Carbon\Carbon::parse($recipient->pivot->received_at)->format('d/m/Y H:i:s') : 'Non défini' }}
            </p>

            <!-- Corps du message -->
            <p class="mb-4">{{ $message->body }}</p>

            <!-- Gestion des pièces jointes -->
            @if($message->attachments->isNotEmpty())
                <h5><i class="fas fa-paperclip"></i> Pièces jointes</h5>
                <ul class="list-group list-group-flush mb-3">
                    @foreach($message->attachments as $attachment)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $attachment->file_name }}</span>
                            <div>
                                <a href="{{ route('attachments.download', $attachment->id) }}" class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                                <button class="btn btn-outline-info btn-sm" onclick="previewAttachment('{{ Storage::url($attachment->filepath) }}')">
                                    <i class="fas fa-eye"></i> Aperçu
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted"><i class="fas fa-paperclip"></i> Aucune pièce jointe</p>
            @endif
        </div>

        <!-- Pied de page avec les boutons d'action -->
        {{-- <div class="card-footer text-end">
            <a href="{{ route('messages.reply', $message->id) }}" class="btn btn-primary">
                <i class="fas fa-reply"></i> Répondre
            </a>
            <a href="{{ route('messages.sent') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la Boîte d'Envoi
            </a>
        </div> --}}
        <!-- Pied de page avec les boutons d'action -->
        <div class="card-footer text-end">
            <a href="{{ route('messages.reply', $message->id) }}" class="btn btn-primary">
                <i class="fas fa-reply"></i> Répondre
            </a>
            <a href="{{ route('messages.replyAllForm', $message->id) }}" class="btn btn-warning">
                <i class="fas fa-reply-all"></i> Répondre à tous
            </a>
            
            <a href="{{ route('messages.forward', $message->id) }}" class="btn btn-success">
                <i class="fas fa-share"></i> Transférer
            </a>
            <a href="{{ route('messages.sent') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la Boîte d'Envoi
            </a>
        </div>

    </div>
</div>

<!-- Modal d'aperçu des pièces jointes -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel"><i class="fas fa-eye"></i> Aperçu de la pièce jointe</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
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
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}
</script>
@endsection

