@extends('layouts.master')

@section('title', 'Modifier le paiement FNL')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('fnl.paiements.show', $paiement) }}" class="btn btn-label-secondary btn-sm">
        <i class="icon-base ti tabler-arrow-left me-1"></i>Retour
    </a>
</div>

<x-vuexy.card title="Correction du paiement FNL — {{ $paiement->poste->nom }}" icon="tabler-edit">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($paiement->statut === 'rejete' && $paiement->motif_rejet)
        <div class="alert alert-warning">
            <strong>Motif du rejet :</strong> {{ $paiement->motif_rejet }}
        </div>
    @endif

    <form method="POST" action="{{ route('fnl.paiements.update', $paiement) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('fnl.paiements._form', ['paiement' => $paiement])
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-send me-1"></i>Renvoyer à l'ACCD
            </button>
        </div>
    </form>
</x-vuexy.card>
@endsection
