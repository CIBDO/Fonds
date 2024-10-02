@extends('layouts.master')

@section('content')
<div class="container">
    <h1>Boîte de Réception</h1>

    @if($messages->isEmpty())
        <p>Aucun message reçu.</p>
    @else
        <ul class="list-group">
            @foreach($messages as $message)
                <li class="list-group-item">
                    <h5>{{ $message->subject }}</h5>
                    <p>{{ $message->body }}</p>
                    <small>De: {{ $message->sender->name }}</small>
                    <a href="{{ route('messages.show', $message->id) }}" class="btn btn-info btn-sm">Voir</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
