@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar messagerie -->
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>

        <!-- Contenu principal : Boîte d'envoi ultra-moderne -->
        <div class="col-12 col-md-9">
            <!-- En-tête moderne avec dégradé -->
            <div class="card border-0 shadow-lg mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header border-0 text-white" style="background: linear-gradient(135deg, #cfd4d1 0%, #b6c6c3 100%); padding: 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-0 fw-bold fs-2">
                                <i class="fas fa-paper-plane me-3"></i> 📤 Boîte d'Envoi
                            </h2>
                            <p class="mb-0 mt-2 opacity-75">Vos messages envoyés</p>
                        </div>
                        <!-- Barre de recherche moderne -->
                        <form class="d-flex mb-2 mb-md-0" method="GET" action="" style="min-width: 300px;">
                            <div class="input-group">
                                <input class="form-control border-0 shadow-sm" type="search" name="q"
                                       placeholder="🔍 Rechercher dans vos envois..." aria-label="Search"
                                       style="border-radius: 25px 0 0 25px; background: rgba(255,255,255,0.9);">
                                <button class="btn text-white" type="submit"
                                        style="background: rgba(255,255,255,0.2); border-radius: 0 25px 25px 0; border: none;">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @if($messages->isEmpty())
                <!-- Message vide ultra-moderne -->
                <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-body text-center p-5" style="background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                        <div class="mb-4">
                            <i class="fas fa-paper-plane fa-5x text-muted opacity-50"></i>
                        </div>
                        <h4 class="fw-bold text-muted mb-3">Aucun message envoyé</h4>
                        <p class="text-muted mb-4">Vous n'avez encore envoyé aucun message. Commencez par composer votre premier message !</p>
                        <button type="button" class="btn btn-lg fw-bold text-white" data-bs-toggle="modal" data-bs-target="#composeModal"
                                style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border: none; border-radius: 15px; padding: 15px 30px; box-shadow: 0 8px 25px rgba(67, 233, 123, 0.3);">
                            <i class="fas fa-pen me-2"></i> Composer un message
                        </button>
                    </div>
                </div>
            @else
                <!-- Liste des messages ultra-moderne -->
                <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-body p-0">
                        @foreach($messages as $index => $message)
                            <div class="message-item border-0 {{ $index === 0 ? '' : 'border-top' }}"
                                 style="animation: slideInUp {{ 0.1 * $index }}s ease-out; animation-fill-mode: both;">
                                <a href="{{ route('messages.show', $message->id) }}"
                                   class="d-flex align-items-center py-4 px-4 text-decoration-none position-relative message-link {{ $message->status == 'unread' ? 'unread-message' : '' }}"
                                   style="transition: all 0.3s ease; border-left: 4px solid transparent;">

                                    <!-- Avatar destinataire principal ultra-moderne -->
                                    <div class="me-3 flex-shrink-0">
                                        @php
                                            $firstRecipient = $message->recipients->first();
                                            $avatar = $firstRecipient->avatar ?? null;
                                            $initial = strtoupper(substr($firstRecipient->name ?? 'U', 0, 1));
                                            $colors = [
                                                'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                                'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                                'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                                'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
                                                'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
                                                'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
                                                'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
                                                'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)'
                                            ];
                                            $colorIndex = ($firstRecipient->id ?? 0) % count($colors);
                                            $avatarBg = $colors[$colorIndex];
                                        @endphp
                                        @if($avatar)
                                            <img src="{{ asset('assets/img/profiles/' . $avatar) }}"
                                                 class="rounded-circle shadow-sm"
                                                 style="width:52px;height:52px;object-fit:cover;border:2px solid rgba(67, 233, 123, 0.3);">
                                        @else
                                            <div class="rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                                 style="width:52px;height:52px;background:{{ $avatarBg }};border:2px solid rgba(67, 233, 123, 0.3);color:white;font-weight:bold;font-size:1.3rem;">
                                                {{ $initial }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Détails du message -->
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="d-flex align-items-center me-3">
                                                <span class="badge bg-success me-2 px-2 py-1" style="border-radius: 10px; font-size: 0.75rem;">
                                                    <i class="fas fa-user me-1"></i>Moi
                                                </span>
                                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($message->recipients->take(3) as $recipient)
                                                    @php
                                                        $recipientInitial = strtoupper(substr($recipient->name ?? 'U', 0, 1));
                                                        $recipientColorIndex = ($recipient->id ?? 0) % count($colors);
                                                        $recipientBg = $colors[$recipientColorIndex];
                                                    @endphp
                                                    <div class="d-flex align-items-center bg-light rounded-pill px-2 py-1 shadow-sm">
                                                        <div class="rounded-circle me-1 d-flex align-items-center justify-content-center"
                                                             style="width:20px;height:20px;background:{{ $recipientBg }};color:white;font-weight:bold;font-size:0.7rem;">
                                                            {{ $recipientInitial }}
                                                        </div>
                                                        <span class="fw-semibold small">{{ Str::limit($recipient->name, 12) }}</span>
                                                    </div>
                                                @endforeach
                                                @if($message->recipients->count() > 3)
                                                    <span class="badge bg-info rounded-pill">+{{ $message->recipients->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center mb-1">
                                            <span class="fw-bold text-dark text-truncate me-2" style="max-width:300px; font-size: 1.1rem;">
                                                {{ $message->subject }}
                                            </span>
                                            @if($message->attachments->isNotEmpty())
                                                <span class="badge bg-warning text-dark rounded-pill me-2">
                                                    <i class="fas fa-paperclip me-1"></i>{{ $message->attachments->count() }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-muted small text-truncate" style="max-width:400px; line-height: 1.4;">
                                            {{ Str::limit(strip_tags($message->body), 80) }}
                                        </div>
                                    </div>

                                    <!-- Statut et date -->
                                    <div class="text-end ms-3 flex-shrink-0" style="min-width:120px;">
                                        <div class="mb-2">
                                            <span class="badge {{ $message->status == 'unread' ? 'bg-warning text-dark' : 'bg-success' }} px-3 py-2"
                                                  style="border-radius: 15px; font-size: 0.8rem;">
                                                <i class="fas {{ $message->status == 'unread' ? 'fa-envelope' : 'fa-envelope-open' }} me-1"></i>
                                                {{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}
                                            </span>
                                        </div>
                                        <div class="text-muted small d-flex align-items-center justify-content-end">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m H:i') : '' }}
                                        </div>
                                        <div class="text-muted small mt-1">
                                            {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->diffForHumans() : '' }}
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Pagination moderne -->
            @if(!$messages->isEmpty())
                <div class="d-flex justify-content-center mt-4">
                    <div class="pagination-wrapper">
                        {{ $messages->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Inclure la modale de composition -->
@include('messages.partials.compose_modal')

<!-- Styles Ultra-Modernes -->
<style>
/* Animations d'entrée */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Effets hover sur les messages */
.message-link:hover {
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%) !important;
    border-left: 4px solid #43e97b !important;
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(67, 233, 123, 0.2) !important;
}

/* Messages non lus */
.unread-message {
    background: linear-gradient(135deg, #f0f8ff 0%, #ffffff 100%) !important;
    border-left: 4px solid #43e97b !important;
}

/* Pagination moderne */
.pagination-wrapper .pagination {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.pagination-wrapper .page-link {
    border: none;
    padding: 12px 18px;
    color: #43e97b;
    background: white;
    transition: all 0.3s ease;
}

.pagination-wrapper .page-link:hover {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
    transform: translateY(-2px);
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border: none;
    color: white;
}

/* Animation des badges */
.badge {
    animation: fadeInScale 0.5s ease-out;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Effet de survol sur les avatars */
.message-link:hover .rounded-circle {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .message-link {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .message-link .text-end {
        align-self: flex-end;
        margin-top: 10px;
    }
}

/* Animation du header */
.card-header {
    animation: slideInDown 0.6s ease-out;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Effet de focus sur la recherche */
.form-control:focus {
    box-shadow: 0 0 0 0.25rem rgba(67, 233, 123, 0.25) !important;
    border-color: #43e97b !important;
}
</style>
@endsection
