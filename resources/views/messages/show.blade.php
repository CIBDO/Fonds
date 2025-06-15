@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Carte ultra-moderne pour l'affichage du message -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <!-- En-tête avec dégradé -->
                <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <!-- Avatar expéditeur ultra-moderne -->
                            @php
                                $avatar = $message->sender->avatar ?? null;
                                $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1));
                                $colors = [
                                    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                    'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                    'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                                    'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'
                                ];
                                $colorIndex = ($message->sender->id ?? 0) % count($colors);
                                $avatarBg = $colors[$colorIndex];
                            @endphp
                            @if($avatar)
                                <img src="{{ asset('assets/img/profiles/' . $avatar) }}" class="rounded-circle me-3 shadow" style="width:64px;height:64px;object-fit:cover;border:3px solid rgba(255,255,255,0.3);">
                            @else
                                <div class="rounded-circle me-3 shadow d-flex align-items-center justify-content-center" style="width:64px;height:64px;background:{{ $avatarBg }};border:3px solid rgba(255,255,255,0.3);color:white;font-weight:bold;font-size:1.8rem;">
                                    {{ $initial }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold fs-4 mb-1">{{ $message->sender->name ?? 'Expéditeur inconnu' }}</div>
                                <div class="opacity-75 small">
                                    <i class="fas fa-envelope me-1"></i>{{ $message->sender->email ?? '' }}
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $message->status == 'unread' ? 'bg-warning text-dark' : 'bg-success' }} mb-2 px-3 py-2" style="border-radius: 25px; font-size: 0.9rem;">
                                <i class="fas {{ $message->status == 'unread' ? 'fa-envelope' : 'fa-envelope-open' }} me-1"></i>
                                {{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}
                            </span>
                            <div class="opacity-75 small">
                                <i class="fas fa-clock me-1"></i>
                                {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Corps du message -->
                <div class="card-body p-4" style="background: linear-gradient(to bottom, #f8f9ff 0%, #ffffff 100%);">
                    <!-- Sujet du message -->
                    <div class="mb-4 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-radius: 15px;">
                        <h3 class="fw-bold text-primary mb-0">
                            <i class="fas fa-comment-alt me-2"></i>{{ $message->subject }}
                        </h3>
                    </div>

                    <!-- Destinataires -->
                    <div class="mb-4 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f5e8 0%, #f3e5f5 100%); border-radius: 15px;">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-users me-2"></i> Destinataires
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($message->recipients as $recipient)
                                @php
                                    $recipientAvatar = $recipient->avatar ?? null;
                                    $recipientInitial = strtoupper(substr($recipient->name ?? 'U', 0, 1));
                                    $recipientColorIndex = ($recipient->id ?? 0) % count($colors);
                                    $recipientBg = $colors[$recipientColorIndex];
                                @endphp
                                <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                                    @if($recipientAvatar)
                                        <img src="{{ asset('assets/img/profiles/' . $recipientAvatar) }}" class="rounded-circle me-2" style="width:28px;height:28px;object-fit:cover;">
                                    @else
                                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:{{ $recipientBg }};color:white;font-weight:bold;font-size:0.8rem;">
                                            {{ $recipientInitial }}
                                        </div>
                                    @endif
                                    <span class="fw-semibold">{{ $recipient->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date de réception -->
                    <div class="mb-4 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #fff3e0 0%, #fce4ec 100%); border-radius: 15px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-check text-warning me-2 fa-lg"></i>
                            <div>
                                <span class="fw-bold text-dark">Date de réception :</span>
                                <span class="text-muted ms-2">
                                    {{ $recipient && $recipient->pivot->received_at ? \Carbon\Carbon::parse($recipient->pivot->received_at)->format('d/m/Y H:i') : 'Non défini' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Corps du message -->
                    <div class="mb-4 p-4 border-0 shadow-sm" style="background: white; border-radius: 15px; border-left: 4px solid #667eea;">
                        <h6 class="fw-bold text-dark mb-3">
                            <i class="fas fa-file-alt me-2 text-primary"></i> Contenu du message
                        </h6>
                        <div class="message-content" style="line-height: 1.6; font-size: 1.1rem;">
                            {!! nl2br(e($message->body)) !!}
                        </div>
                    </div>

                    <!-- Pièces jointes ultra-modernes -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-paperclip text-info me-2"></i> 📎 Pièces jointes
                        </h6>
                        @if($message->attachments->isNotEmpty())
                            <div class="row g-3">
                                @foreach($message->attachments as $attachment)
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px; background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $extension = pathinfo($attachment->filename, PATHINFO_EXTENSION);
                                                        $iconMap = [
                                                            'pdf' => ['fas fa-file-pdf', '#dc3545'],
                                                            'doc' => ['fas fa-file-word', '#0d6efd'],
                                                            'docx' => ['fas fa-file-word', '#0d6efd'],
                                                            'xls' => ['fas fa-file-excel', '#198754'],
                                                            'xlsx' => ['fas fa-file-excel', '#198754'],
                                                            'jpg' => ['fas fa-file-image', '#fd7e14'],
                                                            'jpeg' => ['fas fa-file-image', '#fd7e14'],
                                                            'png' => ['fas fa-file-image', '#fd7e14'],
                                                            'zip' => ['fas fa-file-archive', '#6f42c1']
                                                        ];
                                                        $iconData = $iconMap[strtolower($extension)] ?? ['fas fa-file', '#6c757d'];
                                                    @endphp
                                                    <div class="me-3">
                                                        <i class="{{ $iconData[0] }} fa-2x" style="color: {{ $iconData[1] }};"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-semibold text-dark">{{ $attachment->filename }}</div>
                                                        <div class="small text-muted">{{ strtoupper($extension) }} • Fichier joint</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 mt-3">
                                                    <a href="{{ route('attachments.download', $attachment->id) }}" class="btn btn-sm btn-primary" style="border-radius: 10px;">
                                                        <i class="fas fa-download me-1"></i> Télécharger
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-info" onclick="previewAttachment('{{ asset('storage/' . $attachment->filepath) }}')" style="border-radius: 10px;">
                                                        <i class="fas fa-eye me-1"></i> Aperçu
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%); border-radius: 15px;">
                                <i class="fas fa-paperclip fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Aucune pièce jointe dans ce message</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pied de page avec boutons d'action -->
                <div class="card-footer border-0 p-4" style="background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                    <div class="d-flex flex-wrap justify-content-end gap-3">
                        <a href="{{ route('messages.reply', $message->id) }}" class="btn btn-lg fw-bold text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border: none; border-radius: 15px; padding: 12px 25px; box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);">
                            <i class="fas fa-reply me-2"></i> Répondre
                        </a>
                        <a href="{{ route('messages.replyAllForm', $message->id) }}" class="btn btn-lg fw-bold text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border: none; border-radius: 15px; padding: 12px 25px; box-shadow: 0 6px 20px rgba(250, 112, 154, 0.3);">
                            <i class="fas fa-reply-all me-2"></i> Répondre à tous
                        </a>
                        <a href="{{ route('messages.forward', $message->id) }}" class="btn btn-lg fw-bold text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; border-radius: 15px; padding: 12px 25px; box-shadow: 0 6px 20px rgba(67, 233, 123, 0.3);">
                            <i class="fas fa-share me-2"></i> Transférer
                        </a>
                        <a href="{{ url()->previous() }}" class="btn btn-lg btn-outline-secondary fw-bold" style="border-radius: 15px; padding: 12px 25px;">
                            <i class="fas fa-arrow-left me-2"></i> Retour
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

<!-- Styles Ultra-Modernes -->
<style>
.btn:hover {
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

.card {
  animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.message-content {
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.card-body .card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card-body .card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
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

