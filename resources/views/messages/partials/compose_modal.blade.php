<!-- Modale de composition ultra-attractive -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
      <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header border-0 text-white" style="background: linear-gradient(135deg, #3c7657 0%, #397756 50%); padding: 1.5rem;">
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

          <!-- Section Destinataires avec Cases à Cocher -->
          <div class="mb-4">
            <label class="form-label fw-bold text-dark mb-3">
              <i class="fas fa-users text-primary me-2"></i> 👥 Destinataires
            </label>

            <!-- Barre de recherche -->
            <div class="mb-3">
              <input type="text" id="searchRecipients" class="form-control border-0 shadow-sm" placeholder="🔍 Rechercher un destinataire..." style="border-radius: 15px; padding: 12px; background: #f8f9ff;">
            </div>

            <!-- Liste des destinataires avec cases à cocher -->
            <div class="recipients-list border-0 shadow-sm" style="border-radius: 15px; background: #f8f9ff; max-height: 300px; overflow-y: auto; padding: 15px;">
              @if(isset($users) && $users->count() > 0)
                <div class="mb-2">
                  <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllRecipients">
                    <i class="fas fa-check-double me-1"></i> Tout sélectionner
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllRecipients">
                    <i class="fas fa-times me-1"></i> Tout désélectionner
                  </button>
                </div>
                <div class="recipients-checkboxes">
                  @foreach($users as $user)
                    <div class="recipient-item mb-2" data-name="{{ strtolower($user->name) }}" data-poste="{{ strtolower($user->poste->nom ?? '') }}" data-email="{{ strtolower($user->email ?? '') }}">
                      <div class="form-check p-3 rounded" style="background: white; border: 2px solid #e0e0e0; transition: all 0.3s ease; cursor: pointer;" onmouseover="this.style.borderColor='#667eea'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.2)'" onmouseout="this.style.borderColor='#e0e0e0'; this.style.boxShadow='none'">
                        <input class="form-check-input recipient-checkbox" type="checkbox" name="receiver_ids[]" value="{{ $user->id }}" id="user_{{ $user->id }}" style="width: 20px; height: 20px; cursor: pointer; margin-top: 5px;">
                        <label class="form-check-label w-100" for="user_{{ $user->id }}" style="cursor: pointer; margin-left: 10px;">
                          <div class="d-flex align-items-center">
                            <div class="user-avatar me-3" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px; flex-shrink: 0;">
                              {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                              <div class="fw-bold text-dark">{{ $user->name }}</div>
                              <div class="text-muted small">
                                <i class="fas fa-briefcase me-1"></i>{{ $user->poste->nom ?? 'Aucun poste' }}
                                @if($user->email)
                                  <br><i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                @endif
                              </div>
                            </div>
                          </div>
                        </label>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-center text-muted py-4">
                  <i class="fas fa-users fa-3x mb-3"></i>
                  <p>Aucun utilisateur disponible</p>
                </div>
              @endif
            </div>
            <small class="form-text text-muted mt-2 d-flex align-items-center">
              <i class="fas fa-info-circle me-1 text-primary"></i>
              Cochez les destinataires souhaités. Au moins un destinataire est requis.
            </small>
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
            <button type="submit" class="btn btn-lg fw-bold text-white" style="background: linear-gradient(135deg, #3c7657 0%, #397756 50%); border: none; border-radius: 15px; padding: 15px; box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);">
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
    // Fonction de recherche dans les destinataires
    const searchInput = document.getElementById('searchRecipients');
    const recipientItems = document.querySelectorAll('.recipient-item');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();

            recipientItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const poste = item.getAttribute('data-poste') || '';
                const email = item.getAttribute('data-email') || '';

                if (name.includes(searchTerm) || poste.includes(searchTerm) || email.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Bouton "Tout sélectionner"
    const selectAllBtn = document.getElementById('selectAllRecipients');
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.recipient-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        });
    }

    // Bouton "Tout désélectionner"
    const deselectAllBtn = document.getElementById('deselectAllRecipients');
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.recipient-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        });
    }

    // Mettre à jour le compteur de sélection
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.recipient-checkbox:checked').length;
        const total = document.querySelectorAll('.recipient-checkbox').length;

        // Mettre à jour le texte des boutons si nécessaire
        if (selectAllBtn && deselectAllBtn) {
            if (checked === total) {
                selectAllBtn.disabled = true;
                deselectAllBtn.disabled = false;
            } else if (checked === 0) {
                selectAllBtn.disabled = false;
                deselectAllBtn.disabled = true;
            } else {
                selectAllBtn.disabled = false;
                deselectAllBtn.disabled = false;
            }
        }
    }

    // Écouter les changements sur les checkboxes
    document.querySelectorAll('.recipient-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();

            // Animation visuelle
            const parent = this.closest('.form-check');
            if (this.checked) {
                parent.style.borderColor = '#667eea';
                parent.style.background = 'linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%)';
            } else {
                parent.style.borderColor = '#e0e0e0';
                parent.style.background = 'white';
            }
        });
    });

    // Validation du formulaire
    const form = document.querySelector('#composeModal form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.recipient-checkbox:checked').length;
            if (checked === 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Destinataire requis',
                        text: 'Veuillez sélectionner au moins un destinataire.',
                        confirmButtonColor: '#667eea'
                    });
                } else {
                    alert('Veuillez sélectionner au moins un destinataire.');
                }
                return false;
            }
        });
    }

    // Initialiser le compteur
    updateSelectedCount();
});

// Styles supplémentaires pour les cases à cocher
const checkboxStyle = document.createElement('style');
checkboxStyle.textContent = `
    .recipients-list::-webkit-scrollbar {
        width: 8px;
    }

    .recipients-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .recipients-list::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
    }

    .recipients-list::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .form-check-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .recipient-item {
        transition: all 0.3s ease;
    }

    .recipient-item:hover {
        transform: translateX(5px);
    }
`;
document.head.appendChild(checkboxStyle);
</script>

