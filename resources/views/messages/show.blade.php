@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <div class="card shadow rounded-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white border-0 rounded-top-4 pb-0">
                    <div class="d-flex align-items-center">
                        <!-- Avatar expéditeur -->
                        @php
                            $avatar = $message->sender->avatar ?? null;
                            $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1));
                            $color = ['bg-primary','bg-success','bg-info','bg-warning','bg-danger'][($message->sender->id ?? 0) % 5];
                        @endphp
                        @if($avatar)
                            <img src="{{ asset('assets/img/profiles/' . $avatar) }}" class="rounded-circle me-3" style="width:54px;height:54px;object-fit:cover;">
                        @else
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-3 {{ $color }}" style="width:54px;height:54px;color:white;font-weight:bold;font-size:1.5rem;">
                                {{ $initial }}
                            </span>
                        @endif
                        <div>
                            <div class="fw-bold fs-5 mb-1">{{ $message->sender->name ?? 'Expéditeur inconnu' }}</div>
                            <div class="text-muted small">{{ $message->sender->email ?? '' }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ $message->status == 'unread' ? 'bg-warning text-dark' : 'bg-success' }} mb-1">
                            {{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}
                        </span>
                        <div class="text-muted small">
                            {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                        </div>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="mb-2">
                        <span class="fw-bold fs-4">{{ $message->subject }}</span>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted">À :</span>
                        @foreach($message->recipients as $recipient)
                            <span class="badge bg-secondary me-1">{{ $recipient->name }}</span>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <span class="text-muted">Date de réception :</span>
                        {{ $recipient && $recipient->pivot->received_at ? \Carbon\Carbon::parse($recipient->pivot->received_at)->format('d/m/Y H:i') : 'Non défini' }}
                    </div>
                    <div class="mb-4">
                        <div class="border rounded-3 p-3 bg-light">
                            {!! nl2br(e($message->body)) !!}
                        </div>
                    </div>
                    <!-- Pièces jointes -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-2"><i class="fas fa-paperclip"></i> Pièces jointes</h6>
                        @if($message->attachments->isNotEmpty())
                            <ul class="list-group list-group-flush mb-3">
                                @foreach($message->attachments as $attachment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 ps-0">
                                        <span><i class="fas fa-file me-2"></i>{{ $attachment->filename }}</span>
                                        <div>
                                            <a href="{{ route('attachments.download', $attachment->id) }}" class="btn btn-outline-secondary btn-sm me-2">
                                                <i class="fas fa-download"></i> Télécharger
                                            </a>
                                            <button class="btn btn-outline-info btn-sm" onclick="previewAttachment('{{ asset('storage/' . $attachment->filepath) }}')">
                                                <i class="fas fa-eye"></i> Aperçu
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted"><i class="fas fa-paperclip"></i> Aucune pièce jointe</span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white border-0 rounded-bottom-4 d-flex flex-wrap justify-content-end gap-2">
                    <a href="{{ route('messages.reply', $message->id) }}" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Répondre
                    </a>
                    <a href="{{ route('messages.replyAllForm', $message->id) }}" class="btn btn-warning">
                        <i class="fas fa-reply-all"></i> Répondre à tous
                    </a>
                    <a href="{{ route('messages.forward', $message->id) }}" class="btn btn-success">
                        <i class="fas fa-share"></i> Transférer
                    </a>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aperçu des pièces jointes -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel"><i class="fas fa-eye"></i> Aperçu de la pièce jointe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>
@endsection

