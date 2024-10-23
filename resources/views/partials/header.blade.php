<div class="header">
            <div class="header-left">
                <a href="#" class="logo">
                    <img src="{{asset('assets/img/logo.png')}}" alt="Logo">
                </a>
                <a href="index.html" class="logo logo-small">
                    <img src="{{asset('assets/img/logo-small.png')}}" alt="Logo" width="30" height="30">
                </a>
            </div>
            <div class="menu-toggle">
                <a href="javascript:void(0);" id="toggle_btn">
                    <i class="fas fa-bars"></i>
                </a>
            </div>

            <div class="top-nav-search">
                <form>
                    <input type="text" class="form-control" placeholder="Search here">
                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <a class="mobile_btn" id="mobile_btn">
                <i class="fas fa-bars"></i>
            </a>

            <ul class="nav user-menu">
                <li class="nav-item dropdown noti-dropdown language-drop me-2">
                    <div class="dropdown-menu ">
                        <div class="noti-content">
                            <div>
                                <a class="dropdown-item" href="javascript:;"><i class="flag flag-bl me-2"></i>Francais</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown noti-dropdown me-2">
                    <a href="#" class="dropdown-toggle nav-link header-nav-list" data-bs-toggle="dropdown">
                        <img src="{{asset('assets/img/icons/header-icon-05.svg')}}" alt="">
                        <span class="badge bg-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                    </a>
                    <div class="dropdown-menu notifications">
                        <div class="topnav-dropdown-header">
                            <span class="notification-title">Notifications</span>
                            <a href="{{ route('notifications.markAllAsRead') }}" class="clear-noti">
                                Tout marquer comme lu
                            </a>
                        </div>
                        <div class="noti-content">
                            <ul class="notification-list">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <li class="notification-message">
                                        @if($notification->data['type'] === 'message')
                                            <a href="{{ route('messages.show', $notification->data['message_id']) }}">
                                        @elseif($notification->data['type'] === 'demande_fonds' || $notification->data['type'] === 'status_update')
                                            <a href="{{ route('demandes-fonds.show', $notification->data['demande_id']) }}">
                                        @endif
                                            <div class="media d-flex">
                                                <div class="media-body">
                                                    <p class="noti-details">{{ $notification->data['message'] }}</p>
                                                    <p class="noti-time">
                                                        <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="notification-message">
                                        <p class="text-center p-3">Aucune notification non lue</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="topnav-dropdown-footer">
                            <a href="{{ route('notifications.index') }}">Voir toutes les Notifications</a>
                        </div>
                    </div>
                </li>
                
                <li class="nav-item zoom-screen me-2">
                    <a href="#" class="nav-link header-nav-list win-maximize">
                        <img src="{{asset('assets/img/icons/header-icon-04.svg')}}" alt="">
                    </a>
                </li>
                <li class="nav-item dropdown has-arrow new-user-menus">
                    <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                        <span class="user-img">
                            <img class="rounded-circle" src="{{ asset('assets/img/profiles/Avatar-01.png') }}" width="31" alt="{{ Auth::check() ? Auth::user()->name : 'Guest' }}">
                            <div class="user-text">
                                <h6>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h6>
                                <p class="text-muted mb-0">{{ Auth::check() ? Auth::user()->role : 'N/A' }}</p>
                            </div>
                        </span>
                    </a>
                    <div class="dropdown-menu">
                        <div class="user-header">
                            <div class="avatar avatar-sm">
                                <img src="{{ asset('assets/img/profiles/Avatar-01.png') }}" alt="User Image" class="avatar-img rounded-circle">
                            </div>
                            <div class="user-text">
                                <h6>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</h6>
                                <p class="text-muted mb-0">{{ Auth::check() ? Auth::user()->role : 'N/A' }}</p>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{ Auth::check() ? route('profile.edit') : '#' }}">Mon Profil</a>
                        {{-- <a class="dropdown-item" href="{{ Auth::check() ? route('messages.inbox') : '#' }}">Inbox</a> --}}
                        @if(Auth::check())
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        @else
                            <a class="dropdown-item" href="{{ route('login') }}">Login</a>
                            <a class="dropdown-item" href="{{ route('register') }}">Register</a>
                        @endif
                    </div>
                </li>
                
                

            </ul>

        </div>
