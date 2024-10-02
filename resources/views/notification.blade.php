@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Notifications</h1>

    @if($notifications->isEmpty())
        <p>Aucune notification.</p>
    @else
        <ul class="list-group">
            @foreach($notifications as $notification)
                <li class="list-group-item">
                    <strong>{{ $notification->data['sujet'] }}</strong><br>
                    {{ $notification->data['contenu'] }}<br>
                    <small>Envoyé par : {{ $notification->data['sender_id'] }}</small>
                    <a href="{{ route('messages.show', $notification->data['message_id']) }}" class="btn btn-primary btn-sm">Voir le message</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
