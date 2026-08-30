<div class="btn-compose-wrapper d-grid p-4 pb-0">
    <button
        class="btn btn-primary btn-compose"
        data-bs-toggle="modal"
        data-bs-target="#emailComposeSidebar"
        id="emailComposeSidebarLabel">
        <i class="icon-base ti tabler-pencil me-2"></i>Nouveau message
    </button>
</div>

<div class="email-filters pt-4 pb-2">
    <ul class="email-filter-folders list-unstyled px-3">
        <li class="d-flex justify-content-between align-items-center mb-1 {{ request()->routeIs('messages.index') ? 'active' : '' }}">
            <a href="{{ route('messages.index') }}" class="d-flex flex-wrap align-items-center text-body">
                <i class="icon-base ti tabler-mail"></i>
                <span class="align-middle ms-2">Boîte de réception</span>
            </a>
            @if(isset($inboxCount) && $inboxCount > 0)
                <div class="badge bg-label-primary rounded-pill">{{ $inboxCount }}</div>
            @endif
        </li>
        <li class="d-flex justify-content-between align-items-center mb-1 {{ request()->routeIs('messages.sent') ? 'active' : '' }}">
            <a href="{{ route('messages.sent') }}" class="d-flex flex-wrap align-items-center text-body">
                <i class="icon-base ti tabler-send"></i>
                <span class="align-middle ms-2">Messages envoyés</span>
            </a>
            @if(isset($sentCount) && $sentCount > 0)
                <div class="badge bg-label-info rounded-pill">{{ $sentCount }}</div>
            @endif
        </li>
        <li class="d-flex justify-content-between align-items-center mb-1">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center text-body">
                <i class="icon-base ti tabler-edit"></i>
                <span class="align-middle ms-2">Brouillons</span>
            </a>
            @if(isset($draftCount) && $draftCount > 0)
                <div class="badge bg-label-warning rounded-pill">{{ $draftCount }}</div>
            @endif
        </li>
        <li class="d-flex mb-1">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center text-body">
                <i class="icon-base ti tabler-star"></i>
                <span class="align-middle ms-2">Favoris</span>
            </a>
        </li>
        <li class="d-flex align-items-center mb-1">
            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center text-body">
                <i class="icon-base ti tabler-trash"></i>
                <span class="align-middle ms-2">Corbeille</span>
            </a>
        </li>
    </ul>

    <div class="email-filter-labels pt-4 px-3">
        <p class="small text-body-secondary text-uppercase mb-2">Labels</p>
        <ul class="list-unstyled mb-2">
            <li class="mb-1">
                <a href="javascript:void(0);" class="text-body">
                    <i class="badge badge-dot bg-success align-middle"></i>
                    <span class="align-middle ms-2">Personnel</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="javascript:void(0);" class="text-body">
                    <i class="badge badge-dot bg-primary align-middle"></i>
                    <span class="align-middle ms-2">Société</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="javascript:void(0);" class="text-body">
                    <i class="badge badge-dot bg-warning align-middle"></i>
                    <span class="align-middle ms-2">Important</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" class="text-body">
                    <i class="badge badge-dot bg-danger align-middle"></i>
                    <span class="align-middle ms-2">Privé</span>
                </a>
            </li>
        </ul>
    </div>
</div>
