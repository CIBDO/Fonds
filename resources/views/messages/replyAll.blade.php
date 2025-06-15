@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Carte ultra-moderne pour répondre à tous -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 1.5rem;">
                    <h4 class="mb-0 fw-bold fs-3">
                        <i class="fas fa-reply-all me-3"></i> 👥 Répondre à tous
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Envoyez votre réponse à tous les participants</p>
                </div>

                <div class="card-body p-4" style="background: linear-gradient(to bottom, #fff8f0 0%, #ffffff 100%);">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle fa-2x text-danger me-3"></i>
                                <div>
                                    <strong>⚠️ Erreurs à corriger :</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Informations du message original -->
                    <div class="mb-4 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #fff3e0 0%, #fce4ec 100%); border-radius: 15px;">
                        <h6 class="fw-bold text-warning mb-2">
                            <i class="fas fa-envelope-open me-2"></i> Message original
                        </h6>
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $avatar = $message->sender->avatar ?? null;
                                $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1));
                                $color = ['bg-primary','bg-success','bg-info','bg-warning','bg-danger'][($message->sender->id ?? 0) % 5];
                            @endphp
                            @if($avatar)
                                <img src="{{ asset('assets/img/profiles/' . $avatar) }}" class="rounded-circle me-2" style="width:32px;height:32px;object-fit:cover;">
                            @else
                                <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 {{ $color }}" style="width:32px;height:32px;color:white;font-weight:bold;font-size:0.9rem;">
                                    {{ $initial }}
                                </span>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $message->sender->name ?? 'Expéditeur inconnu' }}</div>
                                <div class="small text-muted">{{ $message->sender->email ?? '' }}</div>
                            </div>
                        </div>
                        <div class="small text-muted mb-1">
                            <strong>Sujet :</strong> {{ $message->subject }}
                        </div>
                        <div class="small text-muted">
                            <strong>Date :</strong> {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                        </div>
                    </div>

                    <!-- Destinataires -->
                    <div class="mb-4 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f5e8 0%, #f3e5f5 100%); border-radius: 15px;">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="fas fa-users me-2"></i> Destinataires de la réponse
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($message->recipients as $recipient)
                                @php
                                    $recipientAvatar = $recipient->avatar ?? null;
                                    $recipientInitial = strtoupper(substr($recipient->name ?? 'U', 0, 1));
                                    $recipientColor = ['bg-primary','bg-success','bg-info','bg-warning','bg-danger'][($recipient->id ?? 0) % 5];
                                @endphp
                                <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                                    @if($recipientAvatar)
                                        <img src="{{ asset('assets/img/profiles/' . $recipientAvatar) }}" class="rounded-circle me-2" style="width:24px;height:24px;object-fit:cover;">
                                    @else
                                        <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 {{ $recipientColor }}" style="width:24px;height:24px;color:white;font-weight:bold;font-size:0.7rem;">
                                            {{ $recipientInitial }}
                                        </span>
                                    @endif
                                    <span class="fw-semibold">{{ $recipient->name }}</span>
                                </div>
                            @endforeach
                            <!-- Ajouter l'expéditeur original -->
                            <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                                @if($avatar)
                                    <img src="{{ asset('assets/img/profiles/' . $avatar) }}" class="rounded-circle me-2" style="width:24px;height:24px;object-fit:cover;">
                                @else
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2 {{ $color }}" style="width:24px;height:24px;color:white;font-weight:bold;font-size:0.7rem;">
                                        {{ $initial }}
                                    </span>
                                @endif
                                <span class="fw-semibold">{{ $message->sender->name ?? 'Expéditeur' }}</span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('messages.replyAll', $message->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="body" class="form-label fw-bold text-dark mb-2">
                                <i class="fas fa-pen-fancy text-primary me-2"></i> 💭 Votre réponse à tous
                            </label>
                            <textarea name="body" class="form-control border-0 shadow-sm" rows="6"
                                      placeholder="Écrivez votre réponse qui sera envoyée à tous les participants..." required
                                      style="border-radius: 15px; padding: 15px; background: #fff8f0;"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="attachments" class="form-label fw-bold text-dark mb-2">
                                <i class="fas fa-paperclip text-success me-2"></i> 📎 Pièces jointes
                            </label>
                            <input type="file" name="attachments[]" class="form-control border-0 shadow-sm" multiple id="attachmentInput"
                                   style="border-radius: 15px; padding: 12px; background: #fff8f0;">
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2MB par fichier)
                            </small>
                            <div id="fileList" class="mt-2 text-muted small"></div>
                        </div>

                        <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-lg fw-bold text-white"
                                    style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border: none; border-radius: 15px; padding: 15px 30px; box-shadow: 0 8px 25px rgba(250, 112, 154, 0.3);">
                                <i class="fas fa-paper-plane me-2"></i> 🚀 Envoyer à tous
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-lg btn-outline-secondary fw-bold"
                               style="border-radius: 15px; padding: 15px 30px;">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles Ultra-Modernes -->
<style>
.form-control:focus, .form-select:focus {
  box-shadow: 0 0 0 0.25rem rgba(250, 112, 154, 0.25) !important;
  border-color: #fa709a !important;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(250, 112, 154, 0.4) !important;
}

.card {
  animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

#fileList {
  background: rgba(250, 112, 154, 0.1);
  border-radius: 10px;
  padding: 10px;
  margin-top: 10px;
}

#fileList:empty {
  display: none;
}

.file-item {
  display: flex;
  align-items: center;
  padding: 5px 0;
  border-bottom: 1px solid rgba(250, 112, 154, 0.1);
}

.file-item:last-child {
  border-bottom: none;
}

.file-icon {
  color: #fa709a;
  margin-right: 8px;
}

.recipient-chip {
  animation: chipFadeIn 0.3s ease-out;
}

@keyframes chipFadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}
</style>

<script>
document.getElementById('attachmentInput')?.addEventListener('change', function () {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '';

    if (this.files.length > 0) {
        fileList.innerHTML = '<div class="fw-bold text-primary mb-2"><i class="fas fa-files me-1"></i> Fichiers sélectionnés :</div>';

        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Taille en MB
            const fileIcon = getFileIcon(file.name);

            fileList.innerHTML += `
                <div class="file-item">
                    <i class="${fileIcon} file-icon"></i>
                    <span class="flex-grow-1">${file.name}</span>
                    <span class="badge bg-warning ms-2">${fileSize} MB</span>
                </div>
            `;
        }
    }
});

function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();
    const iconMap = {
        'pdf': 'fas fa-file-pdf',
        'doc': 'fas fa-file-word',
        'docx': 'fas fa-file-word',
        'xls': 'fas fa-file-excel',
        'xlsx': 'fas fa-file-excel',
        'jpg': 'fas fa-file-image',
        'jpeg': 'fas fa-file-image',
        'png': 'fas fa-file-image',
        'gif': 'fas fa-file-image',
        'zip': 'fas fa-file-archive',
        'rar': 'fas fa-file-archive'
    };
    return iconMap[extension] || 'fas fa-file';
}
</script>
@endsection
