<!-- Sidebar ultra-moderne inspirée de Gmail -->
<div class="mail-sidebar h-100 d-flex flex-column" style="background: #ffffff; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden;">

    <!-- Bouton Composer ultra-moderne -->
    <div class="p-4 border-bottom">
        <button class="btn btn-compose w-100 fw-semibold" data-bs-toggle="modal" data-bs-target="#composeModal"
                style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); color: white; border: none; border-radius: 12px; padding: 12px 20px; font-size: 14px; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3); transition: all 0.2s ease;">
            <i class="fas fa-edit me-2" style="font-size: 16px;"></i>
            Nouveau message
        </button>
    </div>

    <!-- Navigation principale avec style Gmail -->
    <nav class="flex-grow-1 p-2">
        <div class="nav-main mb-3">
            <a href="{{ route('messages.index') }}" class="nav-item {{ request()->routeIs('messages.index') ? 'active' : '' }}"
               style="display: flex; align-items: center; padding: 12px 16px; margin: 2px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease; position: relative;">
                <div class="nav-icon me-3 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                    <svg class="nav-svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zm0-13H5V5h14v1z"/>
                    </svg>
                </div>
                <span class="nav-text">Boîte de réception</span>
                @if(isset($inboxCount) && $inboxCount > 0)
                    <span class="nav-badge ms-auto" style="background: #3B82F6; color: white; font-size: 11px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center;">{{ $inboxCount }}</span>
                @endif
            </a>

            <a href="{{ route('messages.sent') }}" class="nav-item {{ request()->routeIs('messages.sent') ? 'active' : '' }}"
               style="display: flex; align-items: center; padding: 12px 16px; margin: 2px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease; position: relative;">
                <div class="nav-icon me-3 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                    <svg class="nav-svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    </svg>
                </div>
                <span class="nav-text">Messages envoyés</span>
                @if(isset($sentCount) && $sentCount > 0)
                    <span class="nav-badge ms-auto" style="background: #1a73e8; color: white; font-size: 11px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center;">{{ $sentCount }}</span>
                @endif
            </a>

            <a href="#" class="nav-item"
               style="display: flex; align-items: center; padding: 12px 16px; margin: 2px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease; position: relative;">
                <div class="nav-icon me-3 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                    <svg class="nav-svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                    </svg>
                </div>
                <span class="nav-text">Brouillons</span>
                @if(isset($draftCount) && $draftCount > 0)
                    <span class="nav-badge ms-auto" style="background: #ff6d01; color: white; font-size: 11px; padding: 2px 6px; border-radius: 10px; min-width: 18px; text-align: center;">{{ $draftCount }}</span>
                @endif
            </a>

            <a href="#" class="nav-item"
               style="display: flex; align-items: center; padding: 12px 16px; margin: 2px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease; position: relative;">
                <div class="nav-icon me-3 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                    <svg class="nav-svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                    </svg>
                </div>
                <span class="nav-text">Favoris</span>
            </a>

            <a href="#" class="nav-item"
               style="display: flex; align-items: center; padding: 12px 16px; margin: 2px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease; position: relative;">
                <div class="nav-icon me-3 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                    <svg class="nav-svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M15 4H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2h-7l-3-3z"/>
                    </svg>
                </div>
                <span class="nav-text">Corbeille</span>
            </a>
        </div>

        <!-- Section Labels -->
        <div class="nav-section mt-4 pt-3 border-top">
            <div class="nav-section-title px-3 mb-2" style="font-size: 11px; font-weight: 500; color: #9aa0a6; text-transform: uppercase; letter-spacing: 0.8px;">
                Labels
            </div>
            <div class="nav-labels">
                <a href="#" class="nav-item"
                   style="display: flex; align-items: center; padding: 8px 16px; margin: 1px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease;">
                    <span class="nav-dot me-3" style="width: 12px; height: 12px; border-radius: 50%; background: #16a34a;"></span>
                    <span class="nav-text">Personnel</span>
                </a>
                <a href="#" class="nav-item"
                   style="display: flex; align-items: center; padding: 8px 16px; margin: 1px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease;">
                    <span class="nav-dot me-3" style="width: 12px; height: 12px; border-radius: 50%; background: #1a73e8;"></span>
                    <span class="nav-text">Société</span>
                </a>
                <a href="#" class="nav-item"
                   style="display: flex; align-items: center; padding: 8px 16px; margin: 1px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease;">
                    <span class="nav-dot me-3" style="width: 12px; height: 12px; border-radius: 50%; background: #f59e0b;"></span>
                    <span class="nav-text">Important</span>
                </a>
                <a href="#" class="nav-item"
                   style="display: flex; align-items: center; padding: 8px 16px; margin: 1px 0; border-radius: 8px; color: #5f6368; text-decoration: none; font-size: 14px; font-weight: 400; transition: all 0.2s ease;">
                    <span class="nav-dot me-3" style="width: 12px; height: 12px; border-radius: 50%; background: #8b5cf6;"></span>
                    <span class="nav-text">Privé</span>
                </a>
            </div>
        </div>
    </nav>
</div>

<!-- Styles pour la sidebar ultra-moderne -->
<style>
/* Animation d'entrée de la sidebar */
.mail-sidebar {
    animation: slideInLeft 0.3s ease-out;
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Style des éléments de navigation */
.nav-item {
    position: relative;
    overflow: hidden;
}

.nav-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, #e8f0fe 0%, #f1f3f4 100%);
    transition: width 0.2s ease;
    z-index: -1;
}

.nav-item:hover::before {
    width: 100%;
}

.nav-item:hover {
    color: #3B82F6 !important;
    background: linear-gradient(135deg, #e8f0fe 0%, #f1f3f4 100%);
}

.nav-item.active {
    color: #3B82F6 !important;
    background: linear-gradient(135deg, #e8f0fe 0%, #f1f3f4 100%);
    font-weight: 500;
}

.nav-item.active::after {
    content: '';
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 24px;
    background: #3B82F6;
    border-radius: 2px 0 0 2px;
}

/* Style des icônes SVG */
.nav-svg {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.nav-item:hover .nav-svg,
.nav-item.active .nav-svg {
    opacity: 1;
}

/* Style du bouton composer */
.btn-compose:hover {
    background: linear-gradient(135deg, #1D4ED8 0%, #3B82F6 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .mail-sidebar {
        position: fixed;
        top: 0;
        left: -280px;
        width: 280px;
        height: 100vh;
        z-index: 1050;
        transition: left 0.3s ease;
    }

    .mail-sidebar.show {
        left: 0;
    }
}
</style>
