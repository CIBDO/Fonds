@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Header violet simple pour le transfert -->
            <div class="forward-header mb-4" style="background: linear-gradient(135deg, #8B7CF8 0%, #7C3AED 100%); padding: 20px 24px; border-radius: 16px; color: white;">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 9l-5 5 5 5z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="mb-0 fw-bold" style="font-size: 20px;">Transférer le message</h1>
                        <p class="mb-0 opacity-75" style="font-size: 14px;">Partagez ce message avec d'autres destinataires</p>
                    </div>
                </div>
            </div>

            <!-- Contenu épuré -->
            <div class="forward-content" style="background: #ffffff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">

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
                <div class="original-message p-4" style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                    <div class="message-info mb-3">
                        <h6 class="fw-semibold mb-2" style="color: #6B46C1; font-size: 14px;">
                            <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            Message original
                        </h6>
                        <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">
                            <strong>De :</strong> {{ $originalMessage->sender->name ?? 'Expéditeur inconnu' }}
                        </div>
                        <div style="color: #6B7280; font-size: 13px; margin-bottom: 4px;">
                            <strong>Sujet :</strong> {{ $originalMessage->subject }}
                        </div>
                        <div style="color: #6B7280; font-size: 12px;">
                            <strong>Date :</strong> {{ $originalMessage->sent_at ? \Carbon\Carbon::parse($originalMessage->sent_at)->format('d/m/Y H:i') : '' }}
                        </div>
                    </div>
                </div>

                    <form action="{{ route('messages.forward.store', $originalMessage->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Section Destinataires Ultra-Moderne -->
                        <div class="mb-4">
                            <label for="receiver_ids" class="form-label fw-bold text-dark mb-3">
                                <i class="fas fa-users text-primary me-2"></i> 👥 Destinataires
                            </label>
                            <div class="position-relative">
                                <select name="receiver_ids[]" id="receiver_ids" class="form-select border-0 shadow-sm select2-modern" multiple required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}"
                                                data-avatar="{{ $user->avatar ?? '' }}"
                                                data-poste="{{ $user->poste->nom ?? 'Aucun poste' }}"
                                                data-email="{{ $user->email ?? '' }}">
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-text text-muted mt-2 d-flex align-items-center">
                                <i class="fas fa-search me-1 text-primary"></i>
                                Tapez pour rechercher, sélectionnez plusieurs destinataires
                            </small>
                        </div>

                        <div class="mb-4">
                            <label for="subject" class="form-label fw-bold text-dark mb-2">
                                <i class="fas fa-heading text-warning me-2"></i> 📝 Sujet
                            </label>
                            <input type="text" name="subject" class="form-control border-0 shadow-sm"
                                   value="FW: {{ $originalMessage->subject }}" required
                                   style="border-radius: 15px; padding: 12px; background: #f8f9ff;">
                        </div>

                        <div class="mb-4">
                            <label for="body" class="form-label fw-bold text-dark mb-2">
                                <i class="fas fa-align-left text-success me-2"></i> 💬 Message
                            </label>
                            <textarea name="body" class="form-control border-0 shadow-sm" rows="6" required
                                      style="border-radius: 15px; padding: 15px; background: #f8f9ff;">{{ $originalMessage->body }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="attachments" class="form-label fw-bold text-dark mb-2">
                                <i class="fas fa-paperclip text-info me-2"></i> 📎 Pièces jointes
                            </label>
                            <input type="file" name="attachments[]" class="form-control border-0 shadow-sm" multiple
                                   style="border-radius: 15px; padding: 12px; background: #f8f9ff;">
                            <small class="form-text text-muted mt-1">
                                <i class="fas fa-info-circle me-1"></i>
                                Formats acceptés : PDF, DOC, XLS, ZIP, Images (max 50MB)
                            </small>
                        </div>

                        <div class="d-grid gap-3 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-lg fw-bold text-white"
                                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 15px; padding: 15px 30px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
                                <i class="fas fa-paper-plane me-2"></i> 🚀 Transférer le message
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-lg btn-outline-secondary fw-bold"
                               style="border-radius: 15px; padding: 15px 30px;">
                                <i class="fas fa-arrow-left me-2"></i> Annuler
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
/* Styles Select2 Ultra-Modernes */
.select2-container--default .select2-selection--multiple {
  border: none !important;
  border-radius: 15px !important;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%) !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1) !important;
  min-height: 50px !important;
  padding: 8px 12px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  border: none !important;
  border-radius: 25px !important;
  color: white !important;
  padding: 8px 15px !important;
  margin: 3px !important;
  font-weight: 500 !important;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  animation: chipSlideIn 0.3s ease-out;
}

@keyframes chipSlideIn {
  from { opacity: 0; transform: scale(0.8); }
  to { opacity: 1; transform: scale(1); }
}

.select2-dropdown {
  border: none !important;
  border-radius: 15px !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
  background: white !important;
  overflow: hidden !important;
}

.select2-container--default .select2-results__option {
  padding: 12px 15px !important;
  border-bottom: 1px solid #f0f0f0 !important;
  transition: all 0.2s ease !important;
}

.select2-container--default .select2-results__option--highlighted {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  transform: translateX(5px) !important;
}

.form-control:focus, .form-select:focus {
  box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
  border-color: #667eea !important;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4) !important;
}

.card {
  animation: slideInUp 0.5s ease-out;
}

@keyframes slideInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- Script Select2 Ultra-Moderne -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.jQuery && $('#receiver_ids').length) {
        // Fonction pour générer un avatar avec initiale
        function generateAvatar(name) {
            const initial = name.charAt(0).toUpperCase();
            const colors = [
                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)'
            ];
            const colorIndex = name.charCodeAt(0) % colors.length;
            return { initial: initial, background: colors[colorIndex] };
        }

        // Fonction pour formater les options avec avatars
        function formatUser(user) {
            if (!user.id) return user.text;

            const $user = $(user.element);
            const name = user.text;
            const poste = $user.data('poste') || 'Aucun poste';
            const email = $user.data('email') || '';
            const avatar = generateAvatar(name);

            return $(`
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 35px; height: 35px; border-radius: 50%; background: ${avatar.background}; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">
                        ${avatar.initial}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #333; margin-bottom: 2px;">${name}</div>
                        <div style="font-size: 12px; color: #666;">
                            <i class="fas fa-briefcase me-1"></i>${poste}
                            ${email ? `<br><i class="fas fa-envelope me-1"></i>${email}` : ''}
                        </div>
                    </div>
                </div>
            `);
        }

        // Initialisation du Select2 ultra-moderne
        $('#receiver_ids').select2({
            width: '100%',
            placeholder: '🔍 Rechercher et sélectionner des destinataires...',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatUser,
            escapeMarkup: function(markup) { return markup; }
        });

        // Animation d'ouverture
        $('#receiver_ids').on('select2:open', function() {
            $('.select2-dropdown').hide().slideDown(200);
        });
    }
});
</script>
@endsection
