@extends('layouts.master')

@section('title', 'Nouvelle banque')

@section('content')
<div class="d-flex justify-content-between flex-wrap gap-2 mb-4">
    <h4 class="mb-0">Nouvelle banque</h4>
    <a href="{{ route('banques.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Paramètres banque" icon="tabler-building-bank">
    <form method="POST" action="{{ route('banques.store') }}">
        @csrf
        @include('virements.banques._form', ['banque' => null])
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="{{ route('banques.index') }}" class="btn btn-label-secondary">Annuler</a>
        </div>
    </form>
</x-vuexy.card>
@endsection
