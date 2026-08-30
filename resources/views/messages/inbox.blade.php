@extends('layouts.email')

@section('title', 'Boîte de réception')

@section('email-content')
<div class="card shadow-none border-0 rounded-0">
    <div class="card-body emails-list-header p-3 py-2">
        <div class="d-flex justify-content-between align-items-center px-3 mt-2">
            <div class="d-flex align-items-center w-100">
                <i class="icon-base ti tabler-menu-2 icon-lg cursor-pointer d-block d-lg-none me-3"
                   data-bs-toggle="sidebar" data-target="#app-email-sidebar" data-overlay></i>
                <div class="mb-2 w-100">
                    <form method="GET" action="">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text border-0 ps-0">
                                <i class="icon-base ti tabler-search"></i>
                            </span>
                            <input type="search" name="q" class="form-control border-0" placeholder="Rechercher dans vos messages..." value="{{ request('q') }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        @if($messages->isEmpty())
            <div class="text-center py-5">
                <i class="icon-base ti tabler-mail icon-48px text-body-secondary mb-3"></i>
                <h5 class="mb-1">Votre boîte de réception est vide</h5>
                <p class="text-body-secondary mb-0">Les nouveaux messages apparaîtront ici.</p>
            </div>
        @else
            <ul class="list-unstyled email-list m-0">
                @foreach($messages as $message)
                    <li class="email-list-item {{ $message->status == 'unread' ? 'email-marked-unread' : '' }}">
                        <a href="{{ route('messages.show', $message->id) }}" class="d-flex align-items-center px-4 py-3 text-body">
                            <span class="avatar avatar-sm me-3 flex-shrink-0">
                                @php $initial = strtoupper(substr($message->sender->name ?? 'U', 0, 1)); @endphp
                                @if($message->sender->avatar ?? null)
                                    <img src="{{ asset('assets/img/profiles/' . $message->sender->avatar) }}" class="rounded-circle" alt="">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ $initial }}</span>
                                @endif
                            </span>
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium text-truncate me-2">{{ Str::limit($message->sender->name ?? 'Expéditeur inconnu', 25) }}</span>
                                    <small class="text-body-secondary flex-shrink-0">
                                        {{ $message->sent_at ? \Carbon\Carbon::parse($message->sent_at)->format('d/m/Y H:i') : '' }}
                                    </small>
                                </div>
                                <div class="fw-medium text-truncate mb-1">{{ Str::limit($message->subject, 60) }}</div>
                                <div class="text-body-secondary small text-truncate">{{ Str::limit(strip_tags($message->body), 100) }}</div>
                            </div>
                            @if($message->attachments->isNotEmpty())
                                <i class="icon-base ti tabler-paperclip ms-2 text-body-secondary"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="d-flex justify-content-center p-4">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
