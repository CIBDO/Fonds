@extends('layouts.master')

@section('title', 'Nouveau bordereau de virements')

@section('content')
<x-vuexy.card title="Nouveau bordereau — {{ $demande->poste->nom ?? '' }} ({{ $demande->mois }} {{ $demande->annee }})" icon="tabler-file-plus">
    <p class="text-muted mb-4">
        Demande approuvée · Montant couverture :
        <strong>{{ number_format((float) $demande->montant, 0, ',', ' ') }} FCFA</strong>
    </p>

    <form action="{{ route('virements.poste.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
        @csrf
        <input type="hidden" name="demande_fonds_id" value="{{ $demande->id }}">
        <div class="col-md-4">
            <label class="form-label">Période début</label>
            <input type="date" name="periode_debut" class="form-control" value="{{ old('periode_debut') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Période fin</label>
            <input type="date" name="periode_fin" class="form-control" value="{{ old('periode_fin') }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">Pièce jointe (PDF / scan / Excel)</label>
            <input type="file" name="fichier_joint" class="form-control">
        </div>
        <div class="col-12 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Créer le bordereau</button>
            <a href="{{ route('virements.poste.index') }}" class="btn btn-label-secondary">Annuler</a>
        </div>
    </form>
</x-vuexy.card>
@endsection
