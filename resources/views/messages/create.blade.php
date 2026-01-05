@extends('layouts.master')

@section('content')
<!-- Modale de composition ultra-moderne -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="max-width: 800px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

      <!-- En-tête bleu simple -->
      <div class="modal-header border-0" style="background: linear-gradient(135deg, #effdf5 0%, #08a551 100%); padding: 20px 24px; border-radius: 16px; color: #0a0a0a;">
        <div class="d-flex align-items-center">
          <div class="modal-icon me-3">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M14 6V4h-4v2h4zM4 8v11h16V8H4zm16-2c1.11 0 2 .89 2 2v11c0 1.11-.89 2-2 2H4c-1.11 0-2-.89-2-2l.01-11c0-1.11.88-2 1.99-2h4V4c0-1.11.89-2 2-2h4c1.11 0 2 .89 2 2v2h4z"/>
            </svg>
          </div>
          <div>
            <h5 class="modal-title fw-bold mb-0" id="composeModalLabel" style="font-size: 18px;">Nouveau message</h5>
            <p class="mb-0 opacity-75" style="font-size: 13px;">Rédigez votre message professionnel</p>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>

      <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Corps de la modale -->
        <div class="modal-body p-0">
          @if ($errors->any())
            <div class="alert alert-danger border-0 mx-4 mt-4" style="background: #fce8e6; color: #c5221f; border-radius: 8px;">
              <div class="d-flex align-items-center">
                <svg class="me-2" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <strong>Erreur de validation</strong>
              </div>
              <ul class="mb-0 mt-2" style="list-style: none; padding-left: 0;">
                @foreach ($errors->all() as $error)
                  <li style="display: flex; align-items: center; margin-bottom: 4px;">
                    <span style="color: #c5221f; margin-right: 8px;">•</span>
                    {{ $error }}
                  </li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Formulaire de composition -->
          <div class="compose-form p-4">

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
                <input type="text" id="recipientSearch" class="form-control" placeholder="Rechercher des destinataires..."
                       style="border: 2px solid #E5E7EB; border-radius: 12px; padding: 10px 16px 10px 40px; font-size: 14px; background: #F9FAFB;">
                <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="#6B7280" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
                  <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
              </div>

              <!-- Sélection avec checkboxes -->
              <div class="recipients-checkboxes" style="max-height: 200px; overflow-y: auto; border: 2px solid #E5E7EB; border-radius: 12px; background: #F9FAFB; padding: 8px;">
                <div class="selected-count mb-2" style="font-size: 12px; color: #6B7280; font-weight: 500;">
                  <span id="selectedCount">0</span> destinataire(s) sélectionné(s)
                </div>
                @foreach($users as $user)
                  <div class="recipient-item" style="display: flex; align-items: center; padding: 8px 12px; margin: 2px 0; border-radius: 8px; transition: background-color 0.2s ease;" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}">
                    <input type="checkbox" class="recipient-checkbox me-3" id="recipient_{{ $user->id }}" value="{{ $user->id }}" name="receiver_ids[]" style="width: 16px; height: 16px; accent-color: #3B82F6;">
                    <label for="recipient_{{ $user->id }}" style="flex: 1; margin: 0; cursor: pointer; font-size: 14px; color: #374151; display: flex; align-items: center;">
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
                <button type="button" id="selectAllBtn" class="btn btn-sm" style="background: #F3F4F6; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #374151;">
                  Tout sélectionner
                </button>
                <button type="button" id="clearAllBtn" class="btn btn-sm" style="background: #FEE2E2; border: none; border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #DC2626;">
                  Tout désélectionner
                </button>
              </div>
            </div>

            <!-- Champ objet -->
            <div class="form-group mb-4">
              <label for="subject" class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 8px; display: block;">
                <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                </svg>
                Objet
              </label>
              <input type="text" name="subject" id="subject" class="form-control" required
                     placeholder="Entrez l'objet du message"
                     style="border: 2px solid #E5E7EB; border-radius: 12px; padding: 12px; font-size: 14px; background: #F9FAFB;">
            </div>

            <!-- Corps du message -->
            <div class="form-group mb-4">
              <label for="body" class="form-label fw-semibold" style="color: #374151; font-size: 14px; margin-bottom: 8px; display: block;">
                <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                </svg>
                Corps du message
              </label>
              <textarea name="body" id="body" class="form-control" rows="6" required
                        placeholder="Écrivez votre message ici..."
                        style="border: 2px solid #E5E7EB; border-radius: 12px; padding: 12px; font-size: 14px; background: #F9FAFB;"></textarea>
            </div>

            <!-- Zone de pièces jointes -->
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
                        style="border: 2px dashed #D1D5DB; background: #F9FAFB; border-radius: 12px; padding: 12px; width: 100%; text-align: center;">
                  <svg class="me-2" width="18" height="18" viewBox="0 0 24 24" fill="#6B7280">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                  </svg>
                  Sélect. fichiers
                </button>
                <div class="file-info mt-1" style="font-size: 11px; color: #6B7280; text-align: center;">
                  Aucun fichier choisi
                </div>
                <div class="file-formats mt-1" style="font-size: 10px; color: #9CA3AF; text-align: center;">
                  Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2MB par fichier)
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pied de page simple -->
        <div class="modal-footer border-0" style="background: #F8F9FA; padding: 20px 24px;">
          <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                    style="border-radius: 12px; padding: 10px 20px; font-size: 14px; font-weight: 500;">
              <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
              </svg>
              Annuler
            </button>
            <button type="submit" class="btn btn-primary"
                    style="background: linear-gradient(135deg, #267048 0%, #387755 100%); border: none; border-radius: 12px; padding: 10px 24px; font-size: 14px; font-weight: 500;">
              <svg class="me-2" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
              </svg>
              Envoyer le message
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Styles épurés pour la composition -->
<style>
/* Animation d'ouverture */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-content {
    animation: slideInUp 0.3s ease-out;
}

/* Style des champs */
.form-control:focus,
.form-select:focus {
    border-color: #3B82F6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
}

/* Style des boutons */
.btn-primary:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-outline-secondary:hover {
    background: #F3F4F6 !important;
    border-color: #D1D5DB !important;
}

/* Zone de fichier */
.file-input-container button:hover {
    border-color: #3B82F6 !important;
    background: #F3F4F6 !important;
}

/* Styles pour les checkboxes de destinataires */
.recipient-item:hover {
    background: #F3F4F6;
}

.recipient-checkbox:checked + label {
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

.recipient-item:has(.recipient-checkbox:checked) {
    border-color: #3B82F6;
    background: #EFF6FF;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 0;
        max-width: 100%;
        height: 100vh;
    }

    .modal-content {
        height: 100vh;
        border-radius: 0;
    }

    .modal-header,
    .modal-body,
    .modal-footer {
        padding-left: 16px !important;
        padding-right: 16px !important;
    }

    .d-flex.justify-content-end {
        flex-direction: column;
    }

    .d-flex.justify-content-end .btn {
        width: 100%;
        margin-bottom: 8px;
    }

    .recipients-checkboxes {
        max-height: 150px;
    }

    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<!-- Script pour la gestion des destinataires -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize du textarea
    const textarea = document.getElementById('body');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }

    // Gestion des destinataires avec checkboxes
    const searchInput = document.getElementById('recipientSearch');
    const recipientItems = document.querySelectorAll('.recipient-item');
    const checkboxes = document.querySelectorAll('.recipient-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');

    // Fonction pour mettre à jour le compteur
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.recipient-checkbox:checked');
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
    const form = document.querySelector('form[action*="messages.store"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedBoxes = document.querySelectorAll('.recipient-checkbox:checked');
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
