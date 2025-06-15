@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Carte ultra-moderne pour le transfert -->
            <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                    <h4 class="mb-0 fw-bold fs-3">
                        <i class="fas fa-share-square me-3"></i> 📤 Transférer le message
                    </h4>
                    <p class="mb-0 mt-2 opacity-75">Partagez ce message avec d'autres destinataires</p>
                </div>

                <div class="card-body p-4" style="background: linear-gradient(to bottom, #f8f9ff 0%, #ffffff 100%);">
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

                    <!-- Message original en aperçu -->
                    <div class="mb-4 p-3 border-0 shadow-sm" style="background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%); border-radius: 15px;">
                        <h6 class="fw-bold text-primary mb-2">
                            <i class="fas fa-envelope-open me-2"></i> Message original
                        </h6>
                        <div class="small text-muted mb-1">
                            <strong>De :</strong> {{ $originalMessage->sender->name ?? 'Expéditeur inconnu' }}
                        </div>
                        <div class="small text-muted mb-1">
                            <strong>Sujet :</strong> {{ $originalMessage->subject }}
                        </div>
                        <div class="small text-muted">
                            <strong>Date :</strong> {{ $originalMessage->sent_at ? \Carbon\Carbon::parse($originalMessage->sent_at)->format('d/m/Y H:i') : '' }}
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
