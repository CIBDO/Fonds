<ul class="navbar-nav flex-row align-items-center ms-md-auto">
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-2 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
            href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <span class="position-relative">
                <i class="icon-base ti tabler-bell icon-22px"></i>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="badge rounded-pill bg-danger badge-dot badge-notifications border"></span>
                @endif
            </span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end p-0">
            <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                    <h6 class="mb-0 me-auto">Notifications</h6>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="badge bg-label-primary me-2">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                    <a href="javascript:void(0)" class="dropdown-notifications-all p-2" id="markAllAsRead"
                        data-bs-toggle="tooltip" title="Marquer tout comme lu">
                        <i class="icon-base ti tabler-mail-opened icon-20px"></i>
                    </a>
                </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container">
                <ul class="list-group list-group-flush">
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <li class="list-group-item list-group-item-action dropdown-notifications-item notification-item unread"
                            data-notification-id="{{ $notification->id }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="icon-base ti tabler-bell"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="small mb-1">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                    <small class="mb-1 d-block text-body">{{ $notification->data['message'] ?? '' }}</small>
                                    <small class="text-body-secondary">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions">
                                    <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-notifications-read notification-link"
                                        data-url="{{ $notification->data['url'] ?? '#' }}">
                                        <span class="badge badge-dot"></span>
                                    </a>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center py-4 text-body-secondary">Aucune notification</li>
                    @endforelse
                </ul>
            </li>
            <li class="dropdown-menu-footer border-top p-3">
                <a href="{{ route('notifications.index') }}" class="btn btn-primary btn-sm w-100">Voir tout</a>
            </li>
        </ul>
    </li>

    <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
                <img src="{{ asset('assets/img/profiles/Avatar-01.png') }}" alt="{{ Auth::user()->name }}" class="rounded-circle">
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-online me-3">
                            <img src="{{ asset('assets/img/profiles/Avatar-01.png') }}" alt class="w-px-40 h-auto rounded-circle">
                        </div>
                        <div>
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <small class="text-body-secondary">{{ Auth::user()->poste->nom ?? 'N/A' }}</small>
                        </div>
                    </div>
                </a>
            </li>
            <li><div class="dropdown-divider my-1"></div></li>
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="icon-base ti tabler-user me-2"></i>Mon Profil</a></li>
            <li><a class="dropdown-item" href="{{ route('messages.index') }}"><i class="icon-base ti tabler-mail me-2"></i>Messagerie</a></li>
            <li><div class="dropdown-divider my-1"></div></li>
            <li>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="icon-base ti tabler-power me-2"></i>Déconnexion
                </a>
            </li>
        </ul>
    </li>
</ul>
