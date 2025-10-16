<div class="dgtcp-header-main">
    <div class="dgtcp-header-left">
        <a href="{{ route('login') }}" class="dgtcp-logo-container">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo DGTCP" class="dgtcp-logo-img">
            <div class="dgtcp-logo-text">
               {{--  <span class="dgtcp-ministry">DGTCP</span>
                <span class="dgtcp-subtitle">Trésor Public</span> --}}
            </div>
        </a>
        <a href="{{ route('login') }}" class="dgtcp-logo-small">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo DGTCP" class="dgtcp-logo-img-small">
        </a>
    </div>

    {{-- <div class="dgtcp-menu-toggle">
        <a href="javascript:void(0);" id="toggle_btn" class="dgtcp-toggle-btn">
            <i class="fas fa-bars"></i>
        </a>
    </div> --}}

    <div class="dgtcp-search-container">
        <form class="dgtcp-search-form">
            <div class="dgtcp-search-wrapper">
                <input type="text" class="dgtcp-search-input" placeholder="Rechercher dans le système DGTCP...">
                <button class="dgtcp-search-btn" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <a class="dgtcp-mobile-btn" id="mobile_btn">
        <i class="fas fa-bars"></i>
    </a>

    <ul class="dgtcp-nav-menu">
        <!-- Sélecteur de langue -->
        {{-- <li class="dgtcp-nav-item dgtcp-language-selector">
            <div class="dgtcp-language-content">
                <a class="dgtcp-language-link" href="javascript:;">
                    <img src="{{ asset('assets/img/icons/benin-flag.png') }}" alt="Français" class="dgtcp-flag">
                    <span>Français</span>
                </a>
            </div>
        </li> --}}

        <!-- Notifications DGTCP -->
        <li class="dgtcp-nav-item dgtcp-notifications">
            <a href="#" class="dgtcp-nav-link dgtcp-notification-toggle" data-bs-toggle="dropdown">
                <div class="dgtcp-notification-icon">
                    <i class="fas fa-bell"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="dgtcp-notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </div>
            </a>
            <div class="dgtcp-dropdown-menu dgtcp-notifications-dropdown">
                <div class="dgtcp-dropdown-header">
                    <div class="dgtcp-notification-title">
                        <i class="fas fa-bell me-2"></i>
                        Notifications DGTCP
                    </div>
                    <a href="javascript:void(0)" class="dgtcp-mark-all-read" id="markAllAsRead">
                        <i class="fas fa-check-double me-1"></i>Marquer tout comme lu
                    </a>
                </div>
                <div class="dgtcp-notification-content">
                    <ul class="dgtcp-notification-list">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <li class="dgtcp-notification-item" data-notification-id="{{ $notification->id }}">
                                <a href="#" class="dgtcp-notification-link" data-url="{{ $notification->data['url'] ?? '#' }}">
                                    <div class="dgtcp-notification-body">
                                        <div class="dgtcp-notification-icon-wrapper">
                                            @if($notification->type === 'App\Notifications\DemandeFondsNotification')
                                                <i class="fas fa-coins text-warning"></i>
                                            @elseif($notification->type === 'App\Notifications\DemandeFondsStatusNotification')
                                                <i class="fas fa-clipboard-check text-success"></i>
                                            @elseif($notification->type === 'App\Notifications\MessageSent')
                                                <i class="fas fa-envelope text-info"></i>
                                            @elseif($notification->type === 'App\Notifications\PcsDeclarationSoumise')
                                                <i class="fas fa-file-invoice-dollar text-warning"></i>
                                            @elseif($notification->type === 'App\Notifications\PcsDeclarationValidee')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @elseif($notification->type === 'App\Notifications\PcsDeclarationRejetee')
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @elseif($notification->type === 'App\Notifications\PcsAutreDemandeSoumise')
                                                <i class="fas fa-folder-open text-info"></i>
                                            @elseif($notification->type === 'App\Notifications\PcsAutreDemandeValidee')
                                                <i class="fas fa-check-circle text-success"></i>
                                            @elseif($notification->type === 'App\Notifications\PcsAutreDemandeRejetee')
                                                <i class="fas fa-times-circle text-danger"></i>
                                            @else
                                                <i class="fas fa-bell text-primary"></i>
                                            @endif
                                        </div>
                                        <div class="dgtcp-notification-text">
                                            @if($notification->type === 'App\Notifications\DemandeFondsNotification')
                                                <div class="dgtcp-notification-title">Demande de fonds</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                                <div class="dgtcp-notification-amount">{{ $notification->data['montant'] }} FCFA</div>
                                            @elseif($notification->type === 'App\Notifications\DemandeFondsStatusNotification')
                                                <div class="dgtcp-notification-title">Mise à jour du statut</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\MessageSent')
                                                <div class="dgtcp-notification-title">Nouveau message</div>
                                                <div class="dgtcp-notification-message">De: {{ $notification->data['sender_name'] }}</div>
                                                <div class="dgtcp-notification-subject">{{ $notification->data['subject'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\PcsDeclarationSoumise')
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\PcsDeclarationValidee')
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\PcsDeclarationRejetee')
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\PcsAutreDemandeSoumise')
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\PcsAutreDemandeValidee')
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @elseif($notification->type === 'App\Notifications\PcsAutreDemandeRejetee')
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] }}</div>
                                            @else
                                                <div class="dgtcp-notification-title">{{ $notification->data['title'] ?? 'Notification' }}</div>
                                                <div class="dgtcp-notification-message">{{ $notification->data['message'] ?? 'Nouvelle notification' }}</div>
                                            @endif
                                            <div class="dgtcp-notification-time">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <li class="dgtcp-notification-empty">
                                <div class="dgtcp-empty-state">
                                    <i class="fas fa-bell-slash"></i>
                                    <p>Aucune notification non lue</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="dgtcp-dropdown-footer">
                    <a href="{{ route('demandes-fonds.situation') }}" class="dgtcp-view-all-btn">
                        <i class="fas fa-list me-2"></i>Voir toutes les notifications
                    </a>
                </div>
            </div>
        </li>

        <!-- Plein écran -->
        <li class="dgtcp-nav-item dgtcp-fullscreen">
            <a href="#" class="dgtcp-nav-link dgtcp-fullscreen-toggle" onclick="toggleFullscreen()">
                <i class="fas fa-expand"></i>
            </a>
        </li>

        <!-- Profile utilisateur DGTCP -->
        <li class="dgtcp-nav-item dgtcp-user-menu">
            <a href="#" class="dgtcp-nav-link dgtcp-user-toggle" data-bs-toggle="dropdown">
                <div class="dgtcp-user-info">
                    <div class="dgtcp-user-avatar">
                        <img src="{{ asset('assets/img/profiles/Avatar-01.png') }}" alt="{{ Auth::check() ? Auth::user()->name : 'Guest' }}">
                        <div class="dgtcp-user-status"></div>
                    </div>
                    <div class="dgtcp-user-details">
                        <div class="dgtcp-user-name">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                        <div class="dgtcp-user-role">
                            @if(Auth::check())
                                @switch(Auth::user()->role)
                                    @case('admin')
                                        Administrateur DGTCP
                                        @break
                                    @case('tresorier')
                                        Trésorerie Régionale
                                        @break
                                    @case('acct')
                                        ACCT - Agent Comptable
                                        @break
                                    @case('superviseur')
                                        Superviseur DGTCP
                                        @break
                                    @default
                                        {{ Auth::user()->role }}
                                @endswitch
                            @else
                                Invité
                            @endif
                        </div>
                    </div>
                    <i class="fas fa-chevron-down dgtcp-dropdown-arrow"></i>
                </div>
            </a>
            <div class="dgtcp-dropdown-menu dgtcp-user-dropdown">
                <div class="dgtcp-user-header">
                    <div class="dgtcp-user-avatar-large">
                        <img src="{{ asset('assets/img/profiles/Avatar-01.png') }}" alt="User Image">
                    </div>
                    <div class="dgtcp-user-info-large">
                        <div class="dgtcp-user-name-large">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                        <div class="dgtcp-user-role-large">
                            @if(Auth::check())
                                {{ Auth::user()->email }}
                            @endif
                        </div>
                        <div class="dgtcp-user-poste">
                            @if(Auth::check() && Auth::user()->poste)
                                <i class="fas fa-map-marker-alt me-1"></i>{{ Auth::user()->poste->nom }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="dgtcp-dropdown-section">
                    <a class="dgtcp-dropdown-item" href="{{ Auth::check() ? route('users.edit', auth()->user()->id) : '#' }}">
                        <i class="fas fa-user-circle"></i>
                        <span>Mon Profil</span>
                    </a>
                    <a class="dgtcp-dropdown-item" href="{{ Auth::check() ? route('messages.index') : '#' }}">
                        <i class="fas fa-inbox"></i>
                        <span>Boîte de Réception</span>
                    </a>
                    <a class="dgtcp-dropdown-item" href="#">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                </div>

                <div class="dgtcp-dropdown-divider"></div>

                <div class="dgtcp-dropdown-section">
                    @if(Auth::check())
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dgtcp-dropdown-item dgtcp-logout" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Déconnexion</span>
                        </a>
                    @else
                        <a class="dgtcp-dropdown-item" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Connexion</span>
                        </a>
                    @endif
                </div>
            </div>
        </li>
    </ul>
</div>

<script>
// Scripts pour le header DGTCP
document.addEventListener('DOMContentLoaded', function() {
    // Animation du logo au survol
    const logoContainer = document.querySelector('.dgtcp-logo-container');
    if (logoContainer) {
        logoContainer.addEventListener('mouseenter', function() {
            this.style.animation = 'pulse 0.6s ease-in-out';
        });

        logoContainer.addEventListener('animationend', function() {
            this.style.animation = '';
        });
    }

    // Gestion de la recherche
    const searchForm = document.querySelector('.dgtcp-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('.dgtcp-search-input');
            if (searchInput.value.trim()) {
                // Ici vous pouvez ajouter la logique de recherche
                console.log('Recherche DGTCP:', searchInput.value);
                // Redirection vers la page de recherche ou affichage des résultats
            }
        });
    }

    // Marquer toutes les notifications comme lues
    const markAllAsReadBtn = document.getElementById('markAllAsRead');
    if (markAllAsReadBtn) {
        markAllAsReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Ici vous pouvez ajouter l'appel AJAX pour marquer toutes les notifications comme lues
            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recharger pour mettre à jour l'affichage
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        });
    }

    // Gestion des clics sur les notifications
    const notificationLinks = document.querySelectorAll('.dgtcp-notification-link');
    notificationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.closest('.dgtcp-notification-item').dataset.notificationId;
            const url = this.dataset.url;

            // Marquer la notification comme lue
            if (notificationId) {
                fetch(`/notifications/${notificationId}/mark-as-read`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(() => {
                    if (url && url !== '#') {
                        window.location.href = url;
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            }
        });
    });

    // Animation du badge de notification
    const notificationBadge = document.querySelector('.dgtcp-notification-badge');
    if (notificationBadge && parseInt(notificationBadge.textContent) > 0) {
        setInterval(() => {
            notificationBadge.style.animation = 'pulse-notification 2s infinite';
        }, 5000);
    }
});

// Fonction pour le plein écran
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.log(`Erreur d'activation du plein écran: ${err.message}`);
        });

        // Changer l'icône
        const icon = document.querySelector('.dgtcp-fullscreen-toggle i');
        if (icon) {
            icon.className = 'fas fa-compress';
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }

        // Remettre l'icône d'origine
        const icon = document.querySelector('.dgtcp-fullscreen-toggle i');
        if (icon) {
            icon.className = 'fas fa-expand';
        }
    }
}

// Écouter les changements de plein écran
document.addEventListener('fullscreenchange', function() {
    const icon = document.querySelector('.dgtcp-fullscreen-toggle i');
    if (icon) {
        if (document.fullscreenElement) {
            icon.className = 'fas fa-compress';
        } else {
            icon.className = 'fas fa-expand';
        }
    }
});
</script>

<style>
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
</style>
