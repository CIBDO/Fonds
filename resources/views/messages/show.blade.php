@extends('layouts.email')

@section('title', $message->subject)

@section('email-column', 'app-email-view')

@section('email-content')
<div class="card shadow-none border-0 rounded-0">
    <div class="app-email-view-header p-4 pb-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill">
                    <i class="icon-base ti tabler-chevron-left icon-20px"></i>
                </a>
                <h5 class="mb-0 text-truncate">{{ Str::limit($message->subject, 50) }}</h5>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('messages.reply', $message->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="icon-base ti tabler-arrow-back-up me-1"></i>Répondre
                </a>
                <a href="{{ route('messages.replyAllForm', $message->id) }}" class="btn btn-sm btn-outline-success">
                    <i class="icon-base ti tabler-arrows-double-ne-sw me-1"></i>Répondre à tous
                </a>
                <a href="{{ route('messages.forward', $message->id) }}" class="btn btn-sm btn-outline-warning">
                    <i class="icon-base ti tabler-arrow-forward-up me-1"></i>Transférer
                </a>
            </div>
        </div>
        <hr class="my-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <span class="avatar avatar-md me-3">
                    @php $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1)); @endphp
                    @if($message->sender->avatar ?? null)
                        <img src="{{ asset('assets/img/profiles/' . $message->sender->avatar) }}" class="rounded-circle" alt="">
                    @else
                        <span class="avatar-initial rounded-circle bg-label-primary">{{ $initial }}</span>
                    @endif
                </span>
                <div>
                    <h6 class="mb-0">{{ $message->sender->name ?? 'Expéditeur inconnu' }}</h6>
                    <small class="text-body-secondary">{{ $message->sender->email ?? '' }}</small>
                </div>
            </div>
            <div class="text-end">
                @if($message->status == 'unread')
                    <span class="badge bg-label-primary mb-1">Non lu</span>
                @else
                    <span class="badge bg-label-success mb-1">Lu</span>
                @endif
                <div class="small text-body-secondary">
                    {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d MMM Y, H:i') : '' }}
                </div>
            </div>
        </div>
    </div>

    <div class="app-email-view-content p-4 pt-2">
        @if($message->recipients->count() > 0)
            <div class="mb-4 p-3 bg-lighter rounded">
                <span class="fw-medium small text-body-secondary me-2">À :</span>
                @foreach($message->recipients as $recipient)
                    <span class="badge bg-label-secondary me-1">{{ $recipient->name }}</span>
                @endforeach
            </div>
        @endif

        <h4 class="mb-4">{{ $message->subject }}</h4>

        <div class="email-card-last border rounded p-4 mb-4">
            {!! nl2br(e($message->body)) !!}
        </div>

        @if($message->attachments->isNotEmpty())
            <div class="mb-4">
                <h6 class="mb-3">
                    <i class="icon-base ti tabler-paperclip me-1"></i>
                    Pièces jointes ({{ $message->attachments->count() }})
                </h6>
                <div class="row g-3">
                    @foreach($message->attachments as $attachment)
                        @php
                            $extension = strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION));
                            $fileSize = $attachment->size ?? 0;
                        @endphp
                        <div class="col-md-6">
                            <div class="border rounded p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-medium">{{ $attachment->filename }}</div>
                                    <small class="text-body-secondary">{{ strtoupper($extension) }} • {{ number_format($fileSize / 1024, 1) }} Ko</small>
                                </div>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('attachments.download', $attachment->id) }}" class="btn btn-sm btn-primary">
                                        <i class="icon-base ti tabler-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="previewAttachment('{{ $attachment->public_url }}')">
                                        <i class="icon-base ti tabler-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu de la pièce jointe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="attachmentPreview" src="" style="width: 100%; height: 600px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAttachment(url) {
    document.getElementById('attachmentPreview').src = url;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}
</script>
@endpush
