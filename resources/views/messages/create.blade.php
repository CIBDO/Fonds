@extends('layouts.email')

@section('title', 'Nouveau message')

@section('email-content')
<x-vuexy.card title="Nouveau message" icon="tabler-pencil">
    @if ($errors->any())
        <x-vuexy.alert type="danger" class="mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-vuexy.alert>
    @endif

    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" id="createMessageForm">
        @csrf

        <div class="mb-4">
            <label class="form-label" for="createRecipientSearch">Destinataires</label>
            <input type="text" id="createRecipientSearch" class="form-control mb-3" placeholder="Rechercher des destinataires...">
            <div class="border rounded p-3" style="max-height: 220px; overflow-y: auto;">
                <div class="small text-body-secondary mb-2">
                    <span id="createSelectedCount">0</span> destinataire(s) sélectionné(s)
                </div>
                @foreach($users as $user)
                    <div class="recipient-item d-flex align-items-center py-2" data-user-name="{{ $user->name }}">
                        <input type="checkbox" class="form-check-input create-recipient-checkbox me-3" id="create_recipient_{{ $user->id }}" value="{{ $user->id }}" name="receiver_ids[]">
                        <label for="create_recipient_{{ $user->id }}" class="form-check-label d-flex align-items-center gap-2 mb-0 flex-grow-1">
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
                <button type="button" id="createSelectAllBtn" class="btn btn-sm btn-outline-primary">Tout sélectionner</button>
                <button type="button" id="createClearAllBtn" class="btn btn-sm btn-outline-secondary">Tout désélectionner</button>
            </div>
        </div>

        <div class="mb-4">
            <label for="subject" class="form-label">Objet</label>
            <input type="text" name="subject" id="subject" class="form-control" required placeholder="Entrez l'objet du message">
        </div>

        <div class="mb-4">
            <label for="body" class="form-label">Corps du message</label>
            <textarea name="body" id="body" class="form-control" rows="6" required placeholder="Écrivez votre message ici..."></textarea>
        </div>

        <div class="mb-4">
            <label for="attachments" class="form-label">Pièces jointes</label>
            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
            <small class="text-body-secondary">Formats acceptés : JPG, PNG, PDF, DOC, XLS, ZIP... (max 2 Mo par fichier)</small>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('messages.index') }}" class="btn btn-label-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-send me-1"></i>Envoyer le message
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('createRecipientSearch');
    const recipientItems = document.querySelectorAll('.recipient-item');
    const checkboxes = document.querySelectorAll('.create-recipient-checkbox');
    const selectedCount = document.getElementById('createSelectedCount');
    const form = document.getElementById('createMessageForm');

    function updateCount() {
        if (selectedCount) {
            selectedCount.textContent = document.querySelectorAll('.create-recipient-checkbox:checked').length;
        }
    }

    searchInput?.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        recipientItems.forEach(item => {
            const name = (item.dataset.userName || '').toLowerCase();
            item.style.display = name.includes(term) ? 'flex' : 'none';
        });
    });

    document.getElementById('createSelectAllBtn')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true);
        updateCount();
    });

    document.getElementById('createClearAllBtn')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
        updateCount();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();

    form?.addEventListener('submit', function(e) {
        if (document.querySelectorAll('.create-recipient-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un destinataire.');
        }
    });
});
</script>
@endpush
