<div class="app-email-compose modal" id="emailComposeSidebar" tabindex="-1" aria-labelledby="emailComposeSidebarLabel" aria-hidden="true">
    <div class="modal-dialog m-0 me-md-6 mb-6 modal-lg">
        <div class="modal-content p-0">
            <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" id="composeForm">
                @csrf
                <div class="modal-header py-3 justify-content-between">
                    <h5 class="modal-title text-body fs-5" id="emailComposeSidebarLabel">Nouveau message</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" data-bs-dismiss="modal" aria-label="Fermer">
                        <i class="icon-base ti tabler-x icon-20px"></i>
                    </button>
                </div>

                <div class="modal-body flex-grow-1 pb-sm-0 p-5 py-2">
                    @if ($errors->any())
                        <x-vuexy.alert type="danger" class="mb-4">
                            <strong>Erreurs à corriger :</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-vuexy.alert>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-medium" for="recipientSearch">
                            <i class="icon-base ti tabler-users me-1"></i>Destinataires
                        </label>
                        <input type="text" id="recipientSearch" class="form-control mb-3" placeholder="Rechercher des destinataires...">
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <div class="small text-body-secondary mb-2">
                                <span id="selectedCount">0</span> destinataire(s) sélectionné(s)
                            </div>
                            @foreach($users ?? [] as $user)
                                <div class="recipient-item d-flex align-items-center py-2" data-user-name="{{ $user->name }}">
                                    <input type="checkbox" class="form-check-input recipient-checkbox me-3" id="recipient_{{ $user->id }}" value="{{ $user->id }}" name="receiver_ids[]">
                                    <label for="recipient_{{ $user->id }}" class="form-check-label d-flex align-items-center gap-2 mb-0 flex-grow-1">
                                        <span class="avatar avatar-sm">
                                            <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </span>
                                        <span>
                                            <span class="d-block fw-medium">{{ $user->name }}</span>
                                            <small class="text-body-secondary">{{ $user->poste->nom ?? 'Sans poste' }}</small>
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" id="selectAllBtn" class="btn btn-sm btn-outline-primary">Tout sélectionner</button>
                            <button type="button" id="clearAllBtn" class="btn btn-sm btn-outline-secondary">Tout désélectionner</button>
                        </div>
                    </div>

                    <hr class="mx-n5 my-3">

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-medium">Objet</label>
                        <input type="text" name="subject" id="subject" class="form-control" required placeholder="Entrez l'objet du message">
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label fw-medium">Message</label>
                        <textarea name="body" id="body" class="form-control" rows="6" required placeholder="Écrivez votre message ici..."></textarea>
                    </div>

                    <div class="mb-0">
                        <label for="attachments" class="form-label fw-medium">Pièces jointes</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
                        <small class="text-body-secondary">Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2 Mo par fichier)</small>
                    </div>
                </div>

                <div class="modal-footer justify-content-between px-5 py-4">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-base ti tabler-send me-1"></i>Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('recipientSearch');
    const recipientItems = document.querySelectorAll('.recipient-item');
    const checkboxes = document.querySelectorAll('.recipient-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');
    const form = document.getElementById('composeForm');

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.recipient-checkbox:checked').length;
        if (selectedCount) selectedCount.textContent = checked;
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            recipientItems.forEach(item => {
                const name = (item.dataset.userName || '').toLowerCase();
                item.style.display = name.includes(term) ? 'flex' : 'none';
            });
        });
    }

    selectAllBtn?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true);
        updateSelectedCount();
    });

    clearAllBtn?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
        updateSelectedCount();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedCount));
    updateSelectedCount();

    form?.addEventListener('submit', function(e) {
        if (document.querySelectorAll('.recipient-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un destinataire.');
        }
    });
});
</script>
@endpush
