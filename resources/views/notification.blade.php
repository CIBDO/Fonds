@extends('layouts.master')

@section('title', 'Notifications')

@section('content')
<x-vuexy.page-header title="Notifications" />

<x-vuexy.card>
    @if($notifications->isEmpty())
        <x-vuexy.alert type="info">Aucune notification.</x-vuexy.alert>
    @else
        <div class="list-group list-group-flush">
            @foreach($notifications as $notification)
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $notification->data['sujet'] ?? 'Notification' }}</h6>
                            <p class="mb-1 text-body-secondary">{{ $notification->data['contenu'] ?? '' }}</p>
                            <small class="text-body-secondary">Envoyé par : {{ $notification->data['sender_id'] ?? '—' }}</small>
                        </div>
                        @if(isset($notification->data['message_id']))
                            <a href="{{ route('messages.show', $notification->data['message_id']) }}" class="btn btn-sm btn-primary">Voir le message</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-vuexy.card>
@endsection
