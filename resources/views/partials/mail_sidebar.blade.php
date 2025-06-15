<div class="d-flex flex-column h-100 bg-white shadow rounded-3 p-3">
    <!-- Bouton Composer -->
    <button class="btn btn-primary w-100 mb-4 fw-bold" data-bs-toggle="modal" data-bs-target="#composeModal">
        <i class="fas fa-pen"></i> Nouveau message
    </button>

    <!-- Navigation principale -->
    <nav class="nav flex-column mb-4">
        <a href="{{ route('messages.index') }}" class="nav-link {{ request()->routeIs('messages.index') ? 'active fw-bold text-primary' : 'text-dark' }}">
            <i class="fas fa-inbox me-2"></i> Boîte de réception
            @if(isset($inboxCount) && $inboxCount > 0)
                <span class="badge bg-light text-dark float-end">{{ $inboxCount }}</span>
            @endif
        </a>
        <a href="{{ route('messages.sent') }}" class="nav-link {{ request()->routeIs('messages.sent') ? 'active fw-bold text-primary' : 'text-dark' }}">
            <i class="fas fa-paper-plane me-2"></i> Envoyés
            @if(isset($sentCount) && $sentCount > 0)
                <span class="badge bg-light text-dark float-end">{{ $sentCount }}</span>
            @endif
        </a>
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-file-alt me-2"></i> Brouillons
            @if(isset($draftCount) && $draftCount > 0)
                <span class="badge bg-warning text-dark float-end">{{ $draftCount }}</span>
            @endif
        </a>
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-star me-2"></i> Favoris
        </a>
        {{-- <a href="#" class="nav-link text-dark">
            <i class="fas fa-exclamation-circle me-2"></i> Corbeille
            @if(isset($spamCount) && $spamCount > 0)
                <span class="badge bg-danger float-end">{{ $spamCount }}</span>
            @endif
        </a> --}}
        <a href="#" class="nav-link text-dark">
            <i class="fas fa-trash me-2"></i> Corbeille
        </a>
    </nav>

    {{-- <!-- Labels -->
    <div class="mt-auto">
        <div class="fw-bold text-muted small mb-2">LABELS</div>
        <div class="d-flex flex-column gap-2">
            <span class="d-flex align-items-center"><span class="badge bg-success me-2" style="width:12px;height:12px;"></span> Personnel</span>
            <span class="d-flex align-items-center"><span class="badge bg-primary me-2" style="width:12px;height:12px;"></span> Société</span>
            <span class="d-flex align-items-center"><span class="badge bg-warning me-2" style="width:12px;height:12px;"></span> Important</span>
            <span class="d-flex align-items-center"><span class="badge bg-info me-2" style="width:12px;height:12px;"></span> Privé</span>
        </div>
    </div> --}}
</div>
