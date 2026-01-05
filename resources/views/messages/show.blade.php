@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Vue détaillée du message ultra-moderne -->
            <div class="message-detail-card" style="background: #ffffff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden;">

                <!-- En-tête épuré -->
                <div class="message-header" style="padding: 24px; border-bottom: 1px solid #f1f3f4;">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-center">
                            <!-- Avatar expéditeur -->
                            @php
                                $avatar = $message->sender->avatar ?? null;
                                $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1));
                                $colors = ['#1a73e8', '#ea4335', '#fbbc04', '#34a853', '#8ab4f8', '#f28b82', '#fdd663', '#81c995'];
                                $colorIndex = ($message->sender->id ?? 0) % count($colors);
                                $avatarColor = $colors[$colorIndex];
                            @endphp
                            <div class="message-avatar me-3">
                                @if($avatar)
                                    <img src="{{ asset('assets/img/profiles/' . $avatar) }}"
                                         class="rounded-circle" style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #f1f3f4;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width: 48px; height: 48px; background: {{ $avatarColor }}; color: white; font-weight: 500; font-size: 20px;">
                                        {{ $initial }}
                                    </div>
                                @endif
                            </div>

                            <div class="message-sender-info">
                                <div class="sender-name fw-semibold" style="font-size: 16px; color: #202124; line-height: 1.2;">
                                    {{ $message->sender->name ?? 'Expéditeur inconnu' }}
                                </div>
                                <div class="sender-email text-muted" style="font-size: 13px; color: #5f6368;">
                                    {{ $message->sender->email ?? '' }}
                                </div>
                            </div>
                        </div>

                        <div class="message-meta text-end">
                            <div class="message-status mb-2">
                                @if($message->status == 'unread')
                                    <span class="badge" style="background: #effdf5; color: #08a551; font-size: 12px; padding: 4px 8px; border-radius: 8px; font-weight: 500;">
                                        <svg class="me-1" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 5-8-5h16z"/>
                                        </svg>
                                        Non lu
                                    </span>
                                @else
                                    <span class="badge" style="background: #e8f5e8; color: #34a853; font-size: 12px; padding: 4px 8px; border-radius: 8px; font-weight: 500;">
                                        <svg class="me-1" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6zm-2 0l-8 5-8-5h16zm0 12H4V8l8 5 8-5v10z"/>
                                        </svg>
                                        Lu
                                    </span>
                                @endif
                            </div>
                            <div class="message-time text-muted" style="font-size: 12px; color: #5f6368;">
                                {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d MMM Y, H:i') : '' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corps du message épuré -->
                <div class="message-body" style="padding: 32px;">

                    <!-- Sujet -->
                    <div class="message-subject mb-4">
                        <h2 class="fw-bold mb-0" style="font-size: 24px; color: #202124; line-height: 1.3;">
                            {{ $message->subject }}
                        </h2>
                    </div>

                    <!-- Destinataires -->
                    @if($message->recipients->count() > 0)
                        <div class="message-recipients mb-4 p-3" style="background: #f8f9fa; border-radius: 12px; border-left: 3px solid #1a73e8;">
                            <div class="recipients-label fw-semibold mb-2" style="font-size: 13px; color: #5f6368;">
                                <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                À :
                            </div>
                            <div class="recipients-list d-flex flex-wrap gap-2">
                                @foreach($message->recipients as $recipient)
                                    @php
                                        $recipientAvatar = $recipient->avatar ?? null;
                                        $recipientInitial = strtoupper(substr($recipient->name ?? 'U', 0, 1));
                                        $recipientColorIndex = ($recipient->id ?? 0) % count($colors);
                                        $recipientColor = $colors[$recipientColorIndex];
                                    @endphp
                                    <div class="recipient-item d-flex align-items-center" style="background: white; padding: 6px 12px; border-radius: 20px; border: 1px solid #e0e0e0; font-size: 13px;">
                                        @if($recipientAvatar)
                                            <img src="{{ asset('assets/img/profiles/' . $recipientAvatar) }}" class="rounded-circle me-2" style="width: 20px; height: 20px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; background: {{ $recipientColor }}; color: white; font-weight: 500; font-size: 10px;">
                                                {{ $recipientInitial }}
                                            </div>
                                        @endif
                                        <span class="fw-medium">{{ $recipient->name }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Contenu du message -->
                    <div class="message-content" style="background: #fafbfc; border-radius: 12px; padding: 24px; line-height: 1.6; font-size: 15px; color: #202124; border-left: 3px solid #fbbc04;">
                        {!! nl2br(e($message->body)) !!}
                    </div>

                    <!-- Pièces jointes modernes -->
                    @if($message->attachments->isNotEmpty())
                        <div class="message-attachments mt-4">
                            <div class="attachments-header mb-3">
                                <h6 class="fw-semibold mb-0" style="font-size: 14px; color: #202124;">
                                    <svg class="me-2" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/>
                                    </svg>
                                    Pièces jointes ({{ $message->attachments->count() }})
                                </h6>
                            </div>
                            <div class="attachments-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                                @foreach($message->attachments as $attachment)
                                    @php
                                        $extension = strtolower(pathinfo($attachment->filename, PATHINFO_EXTENSION));
                                        $fileSize = $attachment->size ?? 0;
                                        $iconMap = [
                                            'pdf' => ['#ea4335', 'M8.5 6v12h7V6h-7zM15.5 16h-5V8h5v8z'],
                                            'doc' => ['#1a73e8', 'M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z'],
                                            'docx' => ['#1a73e8', 'M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z'],
                                            'xls' => ['#34a853', 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z'],
                                            'xlsx' => ['#34a853', 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z'],
                                            'jpg' => ['#fbbc04', 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'],
                                            'jpeg' => ['#fbbc04', 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'],
                                            'png' => ['#fbbc04', 'M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z'],
                                            'zip' => ['#8ab4f8', 'M15 4H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2h-7l-3-3z']
                                        ];
                                        $iconData = $iconMap[$extension] ?? ['#5f6368', 'M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z'];
                                    @endphp
                                    <div class="attachment-item" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 16px; transition: all 0.2s ease;">
                                        <div class="d-flex align-items-center">
                                            <div class="attachment-icon me-3">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="{{ $iconData[0] }}">
                                                    <path d="{{ $iconData[1] }}"/>
                                                </svg>
                                            </div>
                                            <div class="attachment-info flex-grow-1">
                                                <div class="attachment-name fw-semibold" style="font-size: 14px; color: #202124; margin-bottom: 4px;">
                                                    {{ $attachment->filename }}
                                                </div>
                                                <div class="attachment-meta text-muted" style="font-size: 12px; color: #5f6368;">
                                                    {{ strtoupper($extension) }} • {{ number_format($fileSize / 1024, 1) }} Ko
                                                </div>
                                            </div>
                                        </div>
                                        <div class="attachment-actions mt-3 d-flex gap-2">
                                            <a href="{{ route('attachments.download', $attachment->id) }}"
                                               class="btn btn-sm" style="background: #1a73e8; color: white; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px;">
                                                <svg class="me-1" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/>
                                                </svg>
                                                Télécharger
                                            </a>
                                            <button class="btn btn-sm" onclick="previewAttachment('{{ asset('storage/' . $attachment->filepath) }}')"
                                                    style="background: #f1f3f4; color: #5f6368; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px;">
                                                <svg class="me-1" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                                </svg>
                                                Aperçu
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Actions du message -->
                <div class="message-actions" style="padding: 20px; background: #f8f9fa; border-top: 1px solid #f1f3f4;">
                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('messages.reply', $message->id) }}"
                           class="btn btn-reply" style="background: #aeeaa6; color: white; border: none; border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M10 9V7.41c0-.89-1.08-1.34-1.71-.71L3.7 11.29c-.39.39-.39 1.02 0 1.41l4.59 4.59c.63.63 1.71.18 1.71-.71V14.9c5 0 8.5 1.6 11 5.1-1-5-4-10-11-11z"/>
                            </svg>
                            Répondre
                        </a>
                        <a href="{{ route('messages.replyAllForm', $message->id) }}"
                           class="btn btn-reply-all" style="background: #34a853; color: white; border: none; border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M7 14l5-5 5 5z"/>
                            </svg>
                            Répondre à tous
                        </a>
                        <a href="{{ route('messages.forward', $message->id) }}"
                           class="btn btn-forward" style="background: #fbbc04; color: #202124; border: none; border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 9l-5 5 5 5z"/>
                            </svg>
                            Transférer
                        </a>
                        <a href="{{ url()->previous() }}"
                           class="btn btn-back" style="background: #f1f3f4; color: #5f6368; border: none; border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'aperçu des pièces jointes ultra-moderne -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                <h5 class="modal-title fw-bold fs-4" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i> 👁️ Aperçu de la pièce jointe
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="attachmentPreview" src="" style="width: 100%; height: 600px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Styles ultra-modernes pour la vue détaillée -->
<style>
/* Animation d'entrée de la carte */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-detail-card {
    animation: slideInUp 0.3s ease-out;
}

/* Style des boutons d'action */
.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-reply:hover {
    background: #1557b0 !important;
}

.btn-reply-all:hover {
    background: #2e7d32 !important;
}

.btn-forward:hover {
    background: #f57c00 !important;
}

.btn-back:hover {
    background: #e0e0e0 !important;
}

/* Animation du contenu */
@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.message-body {
    animation: fadeIn 0.4s ease-out 0.1s both;
}

.message-content {
    animation: fadeIn 0.4s ease-out 0.2s both;
}

/* Style des éléments de pièce jointe */
.attachment-item {
    transition: all 0.2s ease;
}

.attachment-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #1a73e8;
}

/* Style des destinataires */
.recipient-item {
    transition: all 0.2s ease;
}

.recipient-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Animation des badges de statut */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(0.95);
    }
}

.message-status .badge {
    animation: pulse 2s ease-in-out infinite;
}

/* Amélioration responsive */
@media (max-width: 768px) {
    .message-detail-card {
        margin: 0 -16px;
        border-radius: 0;
    }

    .message-header {
        padding: 16px !important;
    }

    .message-body {
        padding: 20px !important;
    }

    .message-subject h2 {
        font-size: 20px !important;
    }

    .message-actions {
        padding: 16px !important;
    }

    .message-actions .d-flex {
        flex-direction: column !important;
    }

    .message-actions .btn {
        width: 100%;
        margin-bottom: 8px;
    }

    .attachments-grid {
        grid-template-columns: 1fr !important;
    }

    .attachment-item {
        padding: 12px !important;
    }

    .attachment-actions {
        flex-direction: column !important;
    }

    .attachment-actions .btn {
        width: 100%;
        margin-bottom: 4px;
    }
}

/* Style pour le mode sombre */
@media (prefers-color-scheme: dark) {
    .message-detail-card {
        background: #2d3748;
        color: #e2e8f0;
    }

    .message-header {
        border-bottom-color: #4a5568;
    }

    .message-body {
        color: #e2e8f0;
    }

    .message-sender-info .sender-name {
        color: #f7fafc !important;
    }

    .message-sender-info .sender-email {
        color: #a0aec0 !important;
    }

    .message-subject h2 {
        color: #f7fafc !important;
    }

    .message-content {
        background: #374151 !important;
        color: #e2e8f0 !important;
    }

    .message-recipients {
        background: #374151 !important;
    }

    .recipient-item {
        background: #4a5568 !important;
        border-color: #718096 !important;
    }

    .attachment-item {
        background: #374151 !important;
        border-color: #4a5568 !important;
    }

    .attachment-name {
        color: #f7fafc !important;
    }

    .attachment-meta {
        color: #a0aec0 !important;
    }

    .message-actions {
        background: #374151 !important;
        border-top-color: #4a5568;
    }

    .btn-back {
        background: #4a5568 !important;
        color: #e2e8f0 !important;
    }
}

/* Animations de micro-interactions */
.message-avatar {
    transition: transform 0.2s ease;
}

.message-detail-card:hover .message-avatar {
    transform: scale(1.05);
}

/* Focus states améliorés */
.btn:focus {
    outline: 2px solid rgba(26, 115, 232, 0.3);
    outline-offset: 2px;
}

/* Style pour les liens dans le contenu */
.message-content a {
    color: #1a73e8;
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: border-color 0.2s ease;
}

.message-content a:hover {
    border-bottom-color: #1a73e8;
}

/* Animation de chargement pour les images */
.message-content img {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.message-content img:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}
</style>

<script>
function previewAttachment(url) {
    document.getElementById('attachmentPreview').src = url;
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}
</script>
@endsection

