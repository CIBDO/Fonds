@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>
        <div class="col-12 col-md-9">
            <!-- Header violet simple pour le transfert -->
            <div class="forward-header mb-4" style="background: linear-gradient(135deg, #effdf5 0%, #08a551 100%); padding: 20px 24px; border-radius: 16px; color: #0a0a0a;">
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

                        <!-- Champ destinataires avec checkboxes -->
                        <div class="form-group mb-4">
                            <label class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 12px; display: block;">
                                <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                                Destinataires
                            </label>

                            <!-- Barre de recherche -->
                            <div class="search-container mb-3" style="position: relative;">
                                <input type="text" id="forwardRecipientSearch" class="form-control" placeholder="Rechercher des destinataires..."
                                       style="border: 2px solid #E5E7EB; border-radius: 12px; padding: 10px 16px 10px 40px; font-size: 14px; background: #F9FAFB;">
                                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="#6B7280" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
                                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                </svg>
                            </div>

                            <!-- Sélection avec checkboxes -->
                            <div class="recipients-checkboxes" style="max-height: 200px; overflow-y: auto; border: 2px solid #E5E7EB; border-radius: 12px; background: #F9FAFB; padding: 8px;">
                                <div class="selected-count mb-2" style="font-size: 12px; color: #6B7280; font-weight: 500;">
                                    <span id="forwardSelectedCount">0</span> destinataire(s) sélectionné(s)
                                </div>
                                @foreach($users as $user)
                                    <div class="recipient-item" style="display: flex; align-items: center; padding: 8px 12px; margin: 2px 0; border-radius: 8px; transition: background-color 0.2s ease;" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                                        <input type="checkbox" class="forward-recipient-checkbox me-3" id="forward_recipient_{{ $user->id }}" value="{{ $user->id }}" name="receiver_ids[]" style="width: 16px; height: 16px; accent-color: #3B82F6;">
                                        <label for="forward_recipient_{{ $user->id }}" style="flex: 1; margin: 0; cursor: pointer; font-size: 14px; color: #374151; display: flex; align-items: center;">
                                            @php
                                                $avatarColors = ['#3B82F6', '#1D4ED8', '#2563EB', '#1E40AF', '#1E3A8A', '#312E81'];
                                                $colorIndex = ($user->id ?? 0) % count($avatarColors);
                                                $avatarColor = $avatarColors[$colorIndex];
                                                $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
                                            @endphp
                                            <div style="width: 24px; height: 24px; background: {{ $avatarColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 10px; margin-right: 10px; flex-shrink: 0;">
                                                {{ $initial }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 500;">{{ $user->name }}</div>
                                                <div style="font-size: 12px; color: #6B7280;">{{ $user->poste->nom ?? 'Sans poste' }}</div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Actions rapides -->
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <button type="button" id="forwardSelectAllBtn" class="btn btn-sm" style="background: #F3F4F6; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #374151;">
                                    Tout sélectionner
                                </button>
                                <button type="button" id="forwardClearAllBtn" class="btn btn-sm" style="background: #FEE2E2; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #DC2626;">
                                    Tout désélectionner
                                </button>
                            </div>
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

/* Styles pour les checkboxes de destinataires */
.recipient-item:hover {
    background: #F3F4F6;
}

.forward-recipient-checkbox:checked + label {
    background: #EFF6FF;
    border-radius: 8px;
}

.search-container .form-control:focus {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Animation pour le compteur */
.selected-count {
    transition: color 0.2s ease;
}

.recipient-item {
    border: 1px solid transparent;
}

.recipient-item:has(.forward-recipient-checkbox:checked) {
    border-color: #3B82F6;
    background: #EFF6FF;
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

    // Gestion des destinataires avec checkboxes pour forward
    const searchInput = document.getElementById('forwardRecipientSearch');
    const recipientItems = document.querySelectorAll('.recipient-item');
    const checkboxes = document.querySelectorAll('.forward-recipient-checkbox');
    const selectedCount = document.getElementById('forwardSelectedCount');
    const selectAllBtn = document.getElementById('forwardSelectAllBtn');
    const clearAllBtn = document.getElementById('forwardClearAllBtn');

    // Fonction pour mettre à jour le compteur
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.forward-recipient-checkbox:checked');
        selectedCount.textContent = checkedBoxes.length;

        // Activer/désactiver les boutons d'action rapide
        if (checkedBoxes.length === checkboxes.length) {
            selectAllBtn.disabled = true;
            clearAllBtn.disabled = false;
        } else if (checkedBoxes.length === 0) {
            selectAllBtn.disabled = false;
            clearAllBtn.disabled = true;
        } else {
            selectAllBtn.disabled = false;
            clearAllBtn.disabled = false;
        }
    }

    // Recherche en temps réel
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            recipientItems.forEach(item => {
                const userName = item.dataset.userName.toLowerCase();
                const poste = item.querySelector('div > div:last-child').textContent.toLowerCase();

                if (userName.includes(searchTerm) || poste.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Sélectionner tout
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        });
    }

    // Désélectionner tout
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        });
    }

    // Mettre à jour le compteur quand une checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Initialiser le compteur
    updateSelectedCount();

    // Validation avant envoi
    const form = document.querySelector('form[action*="forward"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.forward-recipient-checkbox:checked');
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Veuillez sélectionner au moins un destinataire.');
                return false;
            }
        });
    }
});
</script>
@endsection
