<!-- Modale de composition ultra-attractive -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
          <h5 class="modal-title fw-bold fs-4" id="composeModalLabel">
            <i class="fas fa-pen-fancy me-2"></i> Nouveau message
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body p-4" style="background: linear-gradient(to bottom, #f8f9ff 0%, #ffffff 100%);">
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" style="border-radius: 15px;">
                <strong>Erreurs à corriger :</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <!-- Section Destinataires Ultra-Moderne -->
          <div class="mb-4">
            <label for="receiver_ids" class="form-label fw-bold text-dark mb-3">
              <i class="fas fa-users text-primary me-2"></i> 👥 Destinataires
            </label>
            <div class="position-relative">
              <select name="receiver_ids[]" id="receiver_ids" class="form-select border-0 shadow-sm select2-modern" multiple required>
                  @if(isset($users))
                      @foreach($users as $user)
                          <option value="{{ $user->id }}"
                                  data-avatar="{{ $user->avatar ?? '' }}"
                                  data-poste="{{ $user->poste->nom ?? 'Aucun poste' }}"
                                  data-email="{{ $user->email ?? '' }}">
                              {{ $user->name }}
                          </option>
                      @endforeach
                  @endif
              </select>
            </div>
            <small class="form-text text-muted mt-2 d-flex align-items-center">
              <i class="fas fa-search me-1 text-primary"></i>
              Tapez pour rechercher, cliquez pour sélectionner plusieurs destinataires
            </small>
            <!-- Affichage des destinataires sélectionnés -->
            <div id="selected-recipients" class="mt-3"></div>
          </div>

          <div class="mb-4">
            <label for="subject" class="form-label fw-bold text-dark mb-2">
              <i class="fas fa-heading text-warning me-2"></i> Objet
            </label>
            <input type="text" name="subject" class="form-control border-0 shadow-sm" placeholder="Entrez l'objet du message" required style="border-radius: 15px; padding: 12px; background: #f8f9ff;">
          </div>

          <div class="mb-4">
            <label for="body" class="form-label fw-bold text-dark mb-2">
              <i class="fas fa-align-left text-success me-2"></i> Corps du message
            </label>
            <textarea name="body" class="form-control border-0 shadow-sm" rows="6" placeholder="Écrivez votre message ici..." required style="border-radius: 15px; padding: 15px; background: #f8f9ff;"></textarea>
          </div>

          <div class="mb-4">
            <label for="attachments" class="form-label fw-bold text-dark mb-2">
              <i class="fas fa-paperclip text-info me-2"></i> Pièces jointes
            </label>
            <input type="file" name="attachments[]" class="form-control border-0 shadow-sm" multiple style="border-radius: 15px; padding: 12px; background: #f8f9ff;">
          </div>
        </div>

        <div class="modal-footer border-0 p-4" style="background: #f8f9ff;">
          <div class="d-grid w-100">
            <button type="submit" class="btn btn-lg fw-bold text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 15px; padding: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
              <i class="fas fa-paper-plane me-2"></i> Envoyer le message
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.modal-content {
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.form-control:focus, .form-select:focus {
  box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
  border-color: #667eea !important;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4) !important;
}

/* Styles Select2 Ultra-Modernes */
.select2-container--default .select2-selection--multiple {
  border: none !important;
  border-radius: 15px !important;
  background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%) !important;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.1) !important;
  min-height: 50px !important;
  padding: 8px 12px !important;
}

.select2-container--default .select2-selection--multiple:focus {
  box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25) !important;
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
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
  color: white !important;
  font-weight: bold !important;
  margin-right: 8px !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
  background: rgba(255, 255, 255, 0.2) !important;
  border-radius: 50% !important;
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

.select2-search--dropdown .select2-search__field {
  border: none !important;
  border-radius: 10px !important;
  background: #f8f9ff !important;
  padding: 12px 15px !important;
  margin: 10px !important;
  width: calc(100% - 20px) !important;
}

.user-option {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
}

.user-avatar {
  width: 35px !important;
  height: 35px !important;
  border-radius: 50% !important;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: white !important;
  font-weight: bold !important;
  font-size: 14px !important;
  flex-shrink: 0 !important;
}

.user-info {
  flex: 1 !important;
}

.user-name {
  font-weight: 600 !important;
  color: #333 !important;
  margin-bottom: 2px !important;
}

.user-details {
  font-size: 12px !important;
  color: #666 !important;
}

.recipient-chip {
  display: inline-flex !important;
  align-items: center !important;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
  color: white !important;
  padding: 8px 15px !important;
  border-radius: 25px !important;
  margin: 3px !important;
  font-size: 14px !important;
  font-weight: 500 !important;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3) !important;
  animation: chipSlideIn 0.3s ease-out !important;
}

.recipient-chip .chip-avatar {
  width: 24px !important;
  height: 24px !important;
  border-radius: 50% !important;
  background: rgba(255, 255, 255, 0.2) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  margin-right: 8px !important;
  font-size: 12px !important;
  font-weight: bold !important;
}
</style>

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
            return {
                initial: initial,
                background: colors[colorIndex]
            };
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
                <div class="user-option">
                    <div class="user-avatar" style="background: ${avatar.background}">
                        ${avatar.initial}
                    </div>
                    <div class="user-info">
                        <div class="user-name">${name}</div>
                        <div class="user-details">
                            <i class="fas fa-briefcase me-1"></i>${poste}
                            ${email ? `<br><i class="fas fa-envelope me-1"></i>${email}` : ''}
                        </div>
                    </div>
                </div>
            `);
        }

        // Fonction pour formater les sélections
        function formatSelection(user) {
            if (!user.id) return user.text;

            const $user = $(user.element);
            const name = user.text;
            const avatar = generateAvatar(name);

            return $(`
                <span style="display: flex; align-items: center; gap: 8px;">
                    <span class="chip-avatar" style="
                        width: 20px;
                        height: 20px;
                        border-radius: 50%;
                        background: ${avatar.background};
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 11px;
                        font-weight: bold;
                        color: white;
                    ">
                        ${avatar.initial}
                    </span>
                    ${name}
                </span>
            `);
        }

        // Initialisation du Select2 ultra-moderne
        $('#receiver_ids').select2({
            width: '100%',
            placeholder: '🔍 Rechercher et sélectionner des destinataires...',
            allowClear: true,
            closeOnSelect: false,
            templateResult: formatUser,
            templateSelection: formatSelection,
            escapeMarkup: function(markup) {
                return markup;
            },
            language: {
                noResults: function() {
                    return '<div style="padding: 20px; text-align: center; color: #666;"><i class="fas fa-search-minus fa-2x mb-2"></i><br>Aucun utilisateur trouvé</div>';
                },
                searching: function() {
                    return '<div style="padding: 20px; text-align: center; color: #667eea;"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>Recherche en cours...</div>';
                },
                inputTooShort: function() {
                    return '<div style="padding: 20px; text-align: center; color: #999;"><i class="fas fa-keyboard fa-2x mb-2"></i><br>Tapez pour rechercher</div>';
                }
            }
        });

        // Animation d'ouverture du dropdown
        $('#receiver_ids').on('select2:open', function() {
            $('.select2-dropdown').hide().slideDown(200);
        });

        // Effet sonore et visuel lors de la sélection (optionnel)
        $('#receiver_ids').on('select2:select', function(e) {
            const selectedData = e.params.data;

            // Animation de succès
            $(this).next('.select2-container').addClass('pulse-success');
            setTimeout(() => {
                $(this).next('.select2-container').removeClass('pulse-success');
            }, 600);

            // Notification toast (optionnel)
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `${selectedData.text} ajouté(e)`,
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            }
        });

        // Animation lors de la suppression
        $('#receiver_ids').on('select2:unselect', function(e) {
            const unselectedData = e.params.data;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: `${unselectedData.text} retiré(e)`,
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        });
    }
});

// CSS pour l'animation de succès
const style = document.createElement('style');
style.textContent = `
    .pulse-success {
        animation: pulseSuccess 0.6s ease-in-out;
    }

    @keyframes pulseSuccess {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); box-shadow: 0 0 20px rgba(67, 233, 123, 0.5); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

