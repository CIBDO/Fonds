<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2 mb-4">
    <a href="{{ route('messages.index') }}" class="btn {{ request()->routeIs('messages.index') ? 'btn-primary' : 'btn-outline-primary' }} fw-bold">
        <i class="fas fa-inbox"></i> Boîte de réception
        <span class="badge bg-light text-dark ms-1">21</span>
    </a>
    <a href="{{ route('messages.sent') }}" class="btn {{ request()->routeIs('messages.sent') ? 'btn-primary' : 'btn-outline-primary' }} fw-bold">
        <i class="fas fa-paper-plane"></i> Envoyés
    </a>
    <a href="#" class="btn btn-outline-primary fw-bold">
        <i class="fas fa-file-alt"></i> Brouillons
        <span class="badge bg-warning text-dark ms-1">2</span>
    </a>
    <a href="#" class="btn btn-outline-primary fw-bold">
        <i class="fas fa-star"></i> Favoris
    </a>
    <a href="#" class="btn btn-outline-primary fw-bold">
        <i class="fas fa-exclamation-circle"></i> Corbeille
        <span class="badge bg-danger ms-1">4</span>
    </a>
    <a href="#" class="btn btn-outline-primary fw-bold">
        <i class="fas fa-trash"></i> Corbeille
    </a>
</div>