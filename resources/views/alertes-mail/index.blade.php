@extends('layouts.master')

@section('title', 'Alertes e-mail')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<h4 class="mb-4"><i class="ti tabler-mail me-2"></i>Destinataires des alertes e-mail</h4>

<x-vuexy.card title="Ajouter un destinataire" icon="tabler-user-plus" class="mb-4">
    <form action="{{ route('alertes-mail.store') }}" method="POST" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label">E-mail <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="{{ old('nom') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="actif" value="1" id="actif_new" checked>
                <label class="form-check-label" for="actif_new">Actif</label>
            </div>
        </div>
        <div class="col-12">
            <div class="text-muted small mb-2">Événements :</div>
            <div class="d-flex flex-wrap gap-3">
                @foreach($evenements as $key => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="new_{{ $key }}"
                            @checked(in_array($key, ['fonds_soumise','autre_soumise','vir_soumission','vir_paiement','fnl_soumise'], true))>
                        <label class="form-check-label" for="new_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Ajouter</button>
        </div>
    </form>
</x-vuexy.card>

@foreach($destinataires as $d)
<x-vuexy.card class="mb-3" title="{{ $d->email }}{{ $d->nom ? ' — '.$d->nom : '' }}" icon="tabler-mail">
    <form action="{{ route('alertes-mail.update', $d) }}" method="POST" class="row g-3">
        @csrf @method('PUT')
        <div class="col-md-4">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" class="form-control" value="{{ $d->email }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="{{ $d->nom }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="actif" value="1" id="actif_{{ $d->id }}" @checked($d->actif)>
                <label class="form-check-label" for="actif_{{ $d->id }}">Actif</label>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3">
                @foreach($evenements as $key => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1" id="{{ $d->id }}_{{ $key }}" @checked($d->{$key})>
                        <label class="form-check-label" for="{{ $d->id }}_{{ $key }}">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary btn-sm">Enregistrer</button>
        </div>
    </form>
    <div class="d-flex gap-2 mt-2">
        <form action="{{ route('alertes-mail.test', $d) }}" method="POST">
            @csrf
            <button class="btn btn-label-info btn-sm">E-mail test</button>
        </form>
        <form action="{{ route('alertes-mail.destroy', $d) }}" method="POST" onsubmit="return confirm('Supprimer ce destinataire ?');">
            @csrf @method('DELETE')
            <button class="btn btn-label-danger btn-sm">Supprimer</button>
        </form>
    </div>
</x-vuexy.card>
@endforeach

@if($destinataires->isEmpty())
    <p class="text-muted">Aucun destinataire configuré.</p>
@endif
@endsection
