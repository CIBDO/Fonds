@extends('layouts.master')

@section('title', 'Nouveau paiement FNL')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('fnl.paiements.index') }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Paiement FNL — {{ $poste->nom }}" icon="tabler-home">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('fnl.paiements.store') }}" enctype="multipart/form-data">
        @csrf
        @include('fnl.paiements._form', ['paiement' => null])
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-send me-1"></i>Soumettre à l'ACCD
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
