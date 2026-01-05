@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar messagerie -->
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>

        <!-- Contenu principal : Boîte d'envoi épurée -->
        <div class="col-12 col-md-9">
            <!-- En-tête bleu simple -->
            <div class="sent-header mb-4" style="background: linear-gradient(135deg, #effdf5 0%, #08a551 100%); padding: 20px 24px; border-radius: 16px; color: #0a0a0a;">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="header-icon me-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="mb-0 fw-bold" style="font-size: 20px;">Boîte d'Envoi</h1>
                            <p class="mb-0 opacity-75" style="font-size: 14px;">Vos messages envoyés</p>
                        </div>
                    </div>
                    <!-- Barre de recherche simple -->
                    <form class="d-flex" method="GET" action="" style="min-width: 280px;">
                        <div class="search-container" style="position: relative; flex: 1;">
                            <input class="form-control" type="search" name="q"
                                   placeholder="Rechercher dans vos envois..."
                                   style="border: none; border-radius: 24px; padding: 8px 16px 8px 40px; background: rgba(255,255,255,0.9); font-size: 14px; width: 100%;">
                            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="#6B7280" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </div>
                    </form>
                </div>
            </div>

            @if($messages->isEmpty())
                <!-- État vide simple -->
                <div class="empty-state text-center py-5" style="background: #F8F9FA; border-radius: 16px; margin-top: 20px;">
                    <div class="empty-icon mb-3">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="#9CA3AF">
                            <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                        </svg>
                    </div>
                    <h3 class="fw-semibold mb-2" style="color: #374151;">Aucun message envoyé</h3>
                    <p class="text-muted" style="color: #6B7280;">Vos messages envoyés apparaîtront ici.</p>
                </div>
            @else
                <!-- Liste des messages envoyés épurée -->
                <div class="message-list" style="background: #ffffff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; margin-top: 20px;">
                    @foreach($messages as $message)
                        <div class="message-item" style="border-bottom: 1px solid #F3F4F6; position: relative;">

                            <a href="{{ route('messages.show', $message->id) }}"
                               class="message-link d-flex align-items-center py-3 px-4 text-decoration-none"
                               style="color: inherit;">

                                <!-- Avatar du premier destinataire -->
                                <div class="message-avatar me-3 flex-shrink-0">
                                    @php
                                        $firstRecipient = $message->recipients->first();
                                        $avatar = $firstRecipient->avatar ?? null;
                                        $initial = strtoupper(substr($firstRecipient->name ?? 'U', 0, 1));
                                        $colors = ['#3B82F6', '#1D4ED8', '#2563EB', '#1E40AF', '#1E3A8A', '#312E81'];
                                        $colorIndex = ($firstRecipient->id ?? 0) % count($colors);
                                        $avatarColor = $colors[$colorIndex];
                                    @endphp
                                    @if($avatar)
                                        <img src="{{ asset('assets/img/profiles/' . $avatar) }}"
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 1px solid #E5E7EB;">
                                    @else
                                        <div style="width: 40px; height: 40px; background: {{ $avatarColor }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 16px;">
                                            {{ $initial }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Contenu du message -->
                                <div class="message-content flex-grow-1 min-w-0">
                                    <!-- Header avec "Moi" et destinataires -->
                                    <div class="message-header d-flex align-items-center mb-1">
                                        <div class="message-sender fw-semibold me-2" style="color: #111827; font-size: 14px;">
                                            Moi
                                        </div>
                                        <div style="color: #6B7280; font-size: 12px; margin-right: 8px;">→</div>
                                        <div class="message-recipients d-flex align-items-center">
                                            @foreach($message->recipients->take(2) as $recipient)
                                                <span style="color: #6B7280; font-size: 13px;">
                                                    {{ Str::limit($recipient->name, 15) }}
                                                    @if(!$loop->last && $message->recipients->count() > 2), @endif
                                                </span>
                                            @endforeach
                                            @if($message->recipients->count() > 2)
                                                <span style="color: #6B7280; font-size: 12px;">+{{ $message->recipients->count() - 2 }}</span>
                                            @endif
                                        </div>
                                        <div class="message-time text-muted ms-auto" style="font-size: 12px; color: #6B7280;">
                                            {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                                        </div>
                                    </div>

                                    <div class="message-subject fw-semibold mb-1" style="color: #111827; font-size: 14px; line-height: 1.3;">
                                        {{ Str::limit($message->subject, 60) }}
                                    </div>

                                    <div class="message-preview text-muted" style="font-size: 13px; color: #6B7280; line-height: 1.4;">
                                        {{ Str::limit(strip_tags($message->body), 100) }}
                                    </div>
                                </div>

                                <!-- Indicateurs -->
                                <div class="message-actions d-flex align-items-center ms-3">
                                    @if($message->attachments->isNotEmpty())
                                        <div style="color: #6B7280; font-size: 12px;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination simple -->
            @if(!$messages->isEmpty())
                <div class="d-flex justify-content-center mt-4">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Inclure la modale de composition -->
@include('messages.partials.compose_modal')

<!-- Styles épurés -->
<style>
/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-list {
    animation: fadeInUp 0.3s ease-out;
}

/* Style des éléments de message */
.message-item {
    transition: background-color 0.2s ease;
}

.message-item:hover {
    background: #F9FAFB;
}

/* Style du contenu des messages */
.message-content {
    min-width: 0;
}

.message-preview {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive */
@media (max-width: 768px) {
    .sent-header {
        padding: 16px 20px;
    }

    .sent-header h1 {
        font-size: 18px;
    }

    .message-link {
        padding: 12px 16px !important;
    }

    .message-avatar {
        margin-right: 12px !important;
    }

    .message-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 4px;
    }

    .message-time {
        margin-left: 0 !important;
        font-size: 11px;
    }
}
</style>
@endsection
