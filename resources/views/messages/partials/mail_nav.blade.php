<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('messages.index') }}" class="btn btn-sm {{ request()->routeIs('messages.index') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="icon-base ti tabler-inbox me-1"></i>Boîte de réception
    </a>
    <a href="{{ route('messages.sent') }}" class="btn btn-sm {{ request()->routeIs('messages.sent') ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="icon-base ti tabler-send me-1"></i>Envoyés
    </a>
</div>
