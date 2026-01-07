@extends('layouts.master')

@section('title', 'Notifications')

@section('content')
<div class="content">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">
                    <i class="fas fa-bell text-primary me-2"></i>
                    Mes Notifications
                </h3>
            </div>
            <div class="col-auto">
                @if($notifications->where('read_at', null)->count() > 0)
                    <button type="button" class="btn btn-primary btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i>
                        Tout marquer comme lu
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse($notifications as $notification)
                @php
                    $data = $notification->data;
                    $type = $data['type'] ?? 'default';
                    $icon = $data['icon'] ?? 'fas fa-bell';
                    $color = $data['color'] ?? 'primary';
                    $title = $data['title'] ?? 'Notification';
                    $message = $data['message'] ?? '';
                    $url = $data['url'] ?? '#';
                    $isRead = $notification->read_at !== null;
                @endphp
                <div class="card mb-3 {{ $isRead ? '' : 'border-left-' . $color }}" style="{{ $isRead ? '' : 'border-left-width: 4px;' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="avatar avatar-sm rounded-circle bg-{{ $color }}-light">
                                    <i class="{{ $icon }} text-{{ $color }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 {{ $isRead ? 'text-muted' : 'fw-bold' }}">
                                            {{ $title }}
                                        </h6>
                                        <p class="mb-1 {{ $isRead ? 'text-muted' : '' }}">
                                            {{ $message }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            @if(!$isRead)
                                                <li>
                                                    <a class="dropdown-item" href="#" onclick="markAsRead('{{ $notification->id }}'); return false;">
                                                        <i class="fas fa-check me-2"></i>Marquer comme lu
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item text-danger" href="#" onclick="deleteNotification('{{ $notification->id }}'); return false;">
                                                    <i class="fas fa-trash me-2"></i>Supprimer
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                @if($url && $url !== '#')
                                    <div class="mt-2">
                                        <a href="{{ $url }}" class="btn btn-sm btn-{{ $color }} btn-outline">
                                            <i class="fas fa-eye me-1"></i>Voir les détails
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune notification</h5>
                        <p class="text-muted">Vous n'avez aucune notification pour le moment.</p>
                    </div>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($notifications->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('add-js')
<script>
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-as-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du marquage de la notification');
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-as-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors du marquage des notifications');
    });
}

function deleteNotification(notificationId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        fetch(`/notifications/${notificationId}/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression de la notification');
        });
    }
}
</script>
@endsection
