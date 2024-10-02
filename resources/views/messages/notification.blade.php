@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Notifications</h1>

    @foreach ($notifications as $notification)
        <div class="alert alert-info">
            {{ $notification->data['message'] }} <!-- Afficher le contenu de la notification -->
            <a href="{{ route('markAsRead', $notification->id) }}" class="btn btn-sm btn-primary">Marquer comme lu</a>
            <a href="{{ route('deleteNotification', $notification->id) }}" class="btn btn-sm btn-danger">Supprimer</a>
        </div>
    @endforeach
</div>
@endsection