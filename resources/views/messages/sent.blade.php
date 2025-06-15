@extends('layouts.master')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar messagerie -->
        <div class="col-12 col-md-3 mb-4">
            @include('partials.mail_sidebar')
        </div>

        <!-- Contenu principal : Boîte d'envoi -->
        <div class="col-12 col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                <h2 class="mb-0 fw-bold" style="font-family: 'Geologica', sans-serif;">Boîte d'Envoi</h2>
                <form class="d-flex mb-2 mb-md-0" method="GET" action="">
                    <input class="form-control me-2" type="search" name="q" placeholder="Rechercher un mail..." aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            @if($messages->isEmpty())
                <div class="alert alert-secondary text-center" role="alert">
                    <i class="fas fa-inbox"></i> Aucun message envoyé.
                </div>
            @else
                <div class="list-group shadow rounded-3">
                    @foreach($messages as $message)
                        <a href="{{ route('messages.show', $message->id) }}"
                           class="list-group-item list-group-item-action d-flex align-items-center py-3 px-2 border-0 border-bottom position-relative {{ $message->status == 'unread' ? 'bg-light' : '' }}"
                           style="transition: background 0.2s;">
                            <!-- Avatar destinataire principal (ou premier) -->
                            <div class="me-3 flex-shrink-0">
                                @php
                                    $firstRecipient = $message->recipients->first();
                                    $avatar = $firstRecipient->avatar ?? null;
                                    $initial = strtoupper(substr($firstRecipient->name ?? 'U', 0, 1));
                                    $color = ['bg-primary','bg-success','bg-info','bg-warning','bg-danger'][($firstRecipient->id ?? 0) % 5];
                                @endphp
                                @if($avatar)
                                    <img src="{{ asset('assets/img/profiles/' . $avatar) }}" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;">
                                @else
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center {{ $color }}" style="width:44px;height:44px;color:white;font-weight:bold;font-size:1.2rem;">
                                        {{ $initial }}
                                    </span>
                                @endif
                            </div>
                            <!-- Détails du message -->
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="fw-semibold me-2 text-truncate" style="max-width:120px;">Moi</span>
                                    @foreach($message->recipients as $recipient)
                                        <span class="badge bg-secondary me-1">{{ $recipient->name }}</span>
                                    @endforeach
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold text-dark text-truncate" style="max-width:220px;">{{ $message->subject }}</span>
                                    @if($message->attachments->isNotEmpty())
                                        <i class="fas fa-paperclip ms-2 text-muted"></i>
                                    @endif
                                </div>
                                <div class="text-muted small text-truncate" style="max-width:350px;">{{ Str::limit(strip_tags($message->body), 60) }}</div>
                            </div>
                            <!-- Statut et date -->
                            <div class="text-end ms-3 flex-shrink-0" style="min-width:90px;">
                                <span class="badge {{ $message->status == 'unread' ? 'bg-warning text-dark' : 'bg-success' }} mb-1">
                                    {{ $message->status == 'unread' ? 'Non lu' : 'Lu' }}
                                </span>
                                <div class="text-muted small">
                                    {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('H:i') : '' }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="d-flex justify-content-center mt-4">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Inclure la modale de composition -->
@include('messages.partials.compose_modal')
@endsection
