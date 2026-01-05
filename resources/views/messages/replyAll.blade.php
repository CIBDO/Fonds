@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Header orange simple -->
            <div class="reply-all-header mb-4" style="background: linear-gradient(135deg, #effdf5 0%, #08a551 100%); padding: 20px 24px; border-radius: 16px; color: #0a0a0a;">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 14l5-5 5 5z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="mb-0 fw-bold" style="font-size: 20px;">Répondre à tous</h1>
                        <p class="mb-0 opacity-75" style="font-size: 14px;">Envoyez votre réponse à tous les participants</p>
                    </div>
                </div>
            </div>

            <!-- Contenu épuré -->
            <div class="reply-all-content" style="background: #ffffff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">

                @if ($errors->any())
                    <div class="alert alert-danger border-0 m-4" style="background: #FEF2F2; color: #DC2626; border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <strong>Erreur de validation</strong>
                        </div>
                        <ul class="mb-0 mt-2" style="list-style: none; padding-left: 0;">
                            @foreach ($errors->all() as $error)
                                <li style="display: flex; align-items: center; margin-bottom: 4px;">
                                    <span style="color: #DC2626; margin-right: 8px;">•</span>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Message original simple -->
                <div class="original-message p-4" style="background: #FEF7ED; border-bottom: 1px solid #FED7AA;">
                    <div class="message-info mb-3">
                        <h6 class="fw-semibold mb-2" style="color: #92400E; font-size: 14px;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Message original
                        </h6>
                        <div class="d-flex align-items-center mb-2">
                            @php
                                $avatar = $message->sender->avatar ?? null;
                                $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1));
                                $colors = ['#3B82F6', '#1D4ED8', '#2563EB', '#1E40AF', '#1E3A8A', '#312E81'];
                                $colorIndex = ($message->sender->id ?? 0) % count($colors);
                                $avatarColor = $colors[$colorIndex];
                            @endphp
                            <div style="width: 32px; height: 32px; background: {{ $avatarColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 14px; margin-right: 12px;">
                                @if($avatar)
                                    <img src="{{ asset('assets/img/profiles/' . $avatar) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ $initial }}
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold" style="color: #111827;">{{ $message->sender->name ?? 'Expéditeur inconnu' }}</div>
                                <div style="color: #6B7280; font-size: 12px;">{{ $message->sender->email ?? '' }}</div>
                            </div>
                        </div>
                        <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">
                            <strong>Sujet :</strong> {{ $message->subject }}
                        </div>
                        <div style="color: #6B7280; font-size: 12px;">
                            <strong>Date :</strong> {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                        </div>
                    </div>
                </div>

                <!-- Destinataires de la réponse -->
                <div class="reply-recipients p-4" style="background: #FFF7ED; border-bottom: 1px solid #FED7AA;">
                    <h6 class="fw-semibold mb-3" style="color: #92400E; font-size: 14px;">
                        <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Destinataires de la réponse
                    </h6>
                    <div class="recipients-list d-flex flex-wrap gap-2">
                        @foreach($message->recipients as $recipient)
                            @php
                                $recipientAvatar = $recipient->avatar ?? null;
                                $recipientInitial = strtoupper(substr($recipient->name ?? 'U', 0, 1));
                                $recipientColorIndex = ($recipient->id ?? 0) % count($colors);
                                $recipientColor = $colors[$recipientColorIndex];
                            @endphp
                            <div class="recipient-item d-flex align-items-center" style="background: white; padding: 6px 12px; border-radius: 20px; border: 1px solid #FED7AA; font-size: 13px;">
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

                <!-- Formulaire de réponse à tous -->
                <form action="{{ route('messages.replyAll', $message->id) }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf

                    <div class="form-group mb-4">
                        <label for="body" class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 8px; display: block;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                            </svg>
                            Votre réponse à tous
                        </label>
                        <textarea name="body" id="body" class="form-control" rows="6" required
                                  placeholder="Écrivez votre réponse qui sera envoyée à tous les participants..."
                                  style="border: 2px solid #E5E7EB; border-radius: 12px; padding: 16px; font-size: 14px; background: #F9FAFB; transition: all 0.2s ease;"></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 8px; display: block;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/>
                            </svg>
                            Pièces jointes
                        </label>
                        <div class="file-input-container">
                            <input type="file" name="attachments[]" id="attachments" class="d-none" multiple>
                            <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('attachments').click()"
                                    style="border: 2px dashed #D1D5DB; background: #F9FAFB; border-radius: 12px; padding: 16px; width: 100%; text-align: center; transition: all 0.2s ease;">
                                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="#6B7280">
                                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                </svg>
                                Sélectionner des fichiers
                            </button>
                            <div class="file-info mt-2" style="font-size: 12px; color: #6B7280; text-align: center;">
                                Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2MB par fichier)
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"
                           style="border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                            </svg>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-primary"
                                style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); border: none; border-radius: 12px; padding: 10px 24px; font-size: 14px; font-weight: 500;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                            Envoyer à tous
                        </button>
                    </div>
                </form>
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
