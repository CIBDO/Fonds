@extends('layouts.email')

@section('title', 'Transférer le message')

@section('email-content')
<x-vuexy.card title="Transférer le message" icon="tabler-arrow-forward-up">
    @if ($errors->any())
        <x-vuexy.alert type="danger" class="mb-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-vuexy.alert>
    @endif

    <div class="bg-lighter rounded p-3 mb-4">
        <h6 class="fw-medium mb-2">Message original</h6>
        <p class="mb-1"><strong>De :</strong> {{ $originalMessage->sender->name ?? 'Expéditeur inconnu' }}</p>
        <p class="mb-1"><strong>Sujet :</strong> {{ $originalMessage->subject }}</p>
        <p class="mb-0 text-body-secondary small">
            {{ $originalMessage->sent_at ? \Carbon\Carbon::parse($originalMessage->sent_at)->format('d/m/Y H:i') : '' }}
        </p>
    </div>

    <form action="{{ route('messages.forward.store', $originalMessage->id) }}" method="POST" enctype="multipart/form-data" id="forwardForm">
        @csrf

        <div class="mb-4">
            <label class="form-label" for="forwardRecipientSearch">Destinataires</label>
            <input type="text" id="forwardRecipientSearch" class="form-control mb-3" placeholder="Rechercher des destinataires...">
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                <div class="small text-body-secondary mb-2">
                    <span id="forwardSelectedCount">0</span> destinataire(s) sélectionné(s)
                </div>
                @foreach($users as $user)
                    <div class="recipient-item d-flex align-items-center py-2" data-user-name="{{ $user->name }}">
                        <input type="checkbox" class="form-check-input forward-recipient-checkbox me-3" id="forward_recipient_{{ $user->id }}" value="{{ $user->id }}" name="receiver_ids[]">
                        <label for="forward_recipient_{{ $user->id }}" class="form-check-label d-flex align-items-center gap-2 mb-0 flex-grow-1">
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
                <button type="button" id="forwardSelectAllBtn" class="btn btn-sm btn-outline-primary">Tout sélectionner</button>
                <button type="button" id="forwardClearAllBtn" class="btn btn-sm btn-outline-secondary">Tout désélectionner</button>
            </div>
        </div>

        <div class="mb-4">
            <label for="subject" class="form-label">Objet</label>
            <input type="text" name="subject" id="subject" class="form-control" value="FW: {{ $originalMessage->subject }}" required>
        </div>

        <div class="mb-4">
            <label for="body" class="form-label">Message</label>
            <textarea name="body" id="body" class="form-control" rows="6" required>{{ $originalMessage->body }}</textarea>
        </div>

        <div class="mb-4">
            <label for="attachments" class="form-label">Pièces jointes</label>
            <input type="file" name="attachments[]" id="attachments" class="form-control" multiple>
            <small class="text-body-secondary">Formats acceptés : PDF, DOC, XLS, ZIP, Images (max 50 Mo)</small>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-label-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-send me-1"></i>Transférer le message
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('forwardRecipientSearch');
    const recipientItems = document.querySelectorAll('.recipient-item');
    const checkboxes = document.querySelectorAll('.forward-recipient-checkbox');
    const selectedCount = document.getElementById('forwardSelectedCount');
    const form = document.getElementById('forwardForm');

    function updateCount() {
        if (selectedCount) {
            selectedCount.textContent = document.querySelectorAll('.forward-recipient-checkbox:checked').length;
        }
    }

    searchInput?.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        recipientItems.forEach(item => {
            const name = (item.dataset.userName || '').toLowerCase();
            item.style.display = name.includes(term) ? 'flex' : 'none';
        });
    });

    document.getElementById('forwardSelectAllBtn')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = true);
        updateCount();
    });

    document.getElementById('forwardClearAllBtn')?.addEventListener('click', () => {
        checkboxes.forEach(cb => cb.checked = false);
        updateCount();
    });

    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();

    form?.addEventListener('submit', function(e) {
        if (document.querySelectorAll('.forward-recipient-checkbox:checked').length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un destinataire.');
        }
    });
});
</script>
@endpush
