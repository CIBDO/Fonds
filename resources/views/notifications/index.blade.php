@extends('layouts.master')

@section('title', 'Notifications')

@section('content')
<x-vuexy.page-header title="Mes Notifications" subtitle="Alertes et mises à jour du système">
    <x-slot:actions>
        @if($notifications->where('read_at', null)->count() > 0)
            <button type="button" class="btn btn-primary btn-sm" onclick="markAllAsRead()">
                <i class="icon-base ti tabler-checks me-1"></i>Tout marquer comme lu
            </button>
        @endif
    </x-slot:actions>
</x-vuexy.page-header>

@forelse($notifications as $notification)
    @php
        $data = $notification->data;
        $type = $data['type'] ?? 'default';
        $icon = $data['icon'] ?? 'tabler-bell';
        $color = $data['color'] ?? 'primary';
        $title = $data['title'] ?? 'Notification';
        $message = $data['message'] ?? '';
        $url = $data['url'] ?? '#';
        $isRead = $notification->read_at !== null;
    @endphp
    <x-vuexy.card class="mb-4 {{ $isRead ? '' : 'border-start border-3 border-' . $color }}">
        <div class="d-flex align-items-start gap-3">
            <span class="avatar avatar-sm">
                <span class="avatar-initial rounded-circle bg-label-{{ $color }}">
                    <i class="icon-base ti {{ $icon }}"></i>
                </span>
            </span>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <h6 class="mb-1 {{ $isRead ? 'text-body-secondary' : 'fw-bold' }}">{{ $title }}</h6>
                        <p class="mb-1 {{ $isRead ? 'text-body-secondary' : '' }}">{{ $message }}</p>
                        <small class="text-body-secondary">
                            <i class="icon-base ti tabler-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon btn-text-secondary" type="button" data-bs-toggle="dropdown">
                            <i class="icon-base ti tabler-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if(!$isRead)
                                <li>
                                    <a class="dropdown-item" href="#" onclick="markAsRead('{{ $notification->id }}'); return false;">
                                        <i class="icon-base ti tabler-check me-2"></i>Marquer comme lu
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="deleteNotification('{{ $notification->id }}'); return false;">
                                    <i class="icon-base ti tabler-trash me-2"></i>Supprimer
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                @if($url && $url !== '#')
                    <div class="mt-2">
                        <a href="{{ $url }}" class="btn btn-sm btn-outline-{{ $color }}">
                            <i class="icon-base ti tabler-eye me-1"></i>Voir les détails
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </x-vuexy.card>
@empty
    <x-vuexy.alert type="info">
        Vous n'avez aucune notification pour le moment.
    </x-vuexy.alert>
@endforelse

@if($notifications->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links() }}
    </div>
@endif
@endsection

@push('scripts')
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
    .then(data => { if (data.success) location.reload(); })
    .catch(() => alert('Erreur lors du marquage de la notification'));
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
    .then(data => { if (data.success) location.reload(); })
    .catch(() => alert('Erreur lors du marquage des notifications'));
}

function deleteNotification(notificationId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) return;

    fetch(`/notifications/${notificationId}/delete`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => { if (data.success) location.reload(); })
    .catch(() => alert('Erreur lors de la suppression de la notification'));
}
</script>
@endpush
