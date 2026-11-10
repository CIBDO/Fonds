@extends('layouts.master')

@section('title', 'Bordereau VIR')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h4 class="mb-1">Bordereau VIR — {{ $bordereau->poste->nom }}</h4>
        <div class="text-muted">{{ $bordereau->mois }} {{ $bordereau->annee }}
            @if($bordereau->status === 'soumis')
                <span class="badge bg-label-success ms-2">Soumis</span>
            @else
                <span class="badge bg-label-warning ms-2">Brouillon</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('virements.poste.index') }}" class="btn btn-label-secondary btn-sm">Retour</a>
        @if($bordereau->isBrouillon())
            <form action="{{ route('virements.poste.soumettre', $bordereau) }}" method="POST" onsubmit="return confirm('Soumettre ce bordereau à l\'ACCT ?');">
                @csrf
                <button class="btn btn-primary btn-sm"><i class="ti tabler-send me-1"></i>Soumettre à l'ACCT</button>
            </form>
        @endif
    </div>
</div>

<p class="mb-4">
    Couverture demande :
    <strong>{{ number_format((float) ($bordereau->demandeFonds->montant ?? 0), 0, ',', ' ') }} FCFA</strong>
    · Total VIR :
    <strong>{{ number_format($bordereau->virements->sum('montant'), 0, ',', ' ') }} FCFA</strong>
</p>

@if($bordereau->isBrouillon())
<x-vuexy.card title="Ajouter un VIR (1 banque = 1 VIR)" icon="tabler-plus" class="mb-4">
    <p class="text-muted small mb-3">
        Renseignez le VIR puis chargez le fichier Excel des lignes détail (BE, Mdt, Ord, Prog, Créancier, Compte, Montant).
        <a href="{{ route('virements.poste.template') }}">Télécharger le template Excel</a>
    </p>
    <form action="{{ route('virements.poste.virements.store', $bordereau) }}" method="POST" enctype="multipart/form-data" class="row g-3">
        @csrf
        <div class="col-md-4">
            <label class="form-label">Banque <span class="text-danger">*</span></label>
            <select name="banque_id" class="form-select" required>
                <option value="">— Choisir —</option>
                @foreach($banques as $banque)
                    <option value="{{ $banque->id }}">{{ $banque->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">N° VIR <span class="text-danger">*</span></label>
            <input type="text" name="numero_vir" class="form-control" required placeholder="Numéro métier">
        </div>
        <div class="col-md-4">
            <label class="form-label">Montant VIR <span class="text-danger">*</span></label>
            <input type="text" name="montant" class="form-control montant-sep" inputmode="numeric" placeholder="ex. 1 250 000" required>
        </div>
        <div class="col-md-8">
            <label class="form-label">Fichier Excel des détails <span class="text-danger">*</span></label>
            <input type="file" name="fichier_excel" class="form-control" accept=".xlsx,.xls" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">
                <i class="ti tabler-file-spreadsheet me-1"></i>Créer le VIR et importer
            </button>
        </div>
    </form>
</x-vuexy.card>
@endif

@foreach($bordereau->virements as $virement)
<x-vuexy.card class="mb-4" title="VIR {{ $virement->numero_vir }} — {{ $virement->banque->nom ?? '' }}" icon="tabler-building-bank">
    <div class="row mb-3">
        <div class="col-md-3"><strong>Poste :</strong> {{ $bordereau->poste->nom }}</div>
        <div class="col-md-2"><strong>Mois :</strong> {{ $virement->mois }} {{ $virement->annee }}</div>
        <div class="col-md-3"><strong>Montant :</strong> {{ number_format((float) $virement->montant, 0, ',', ' ') }} FCFA</div>
        <div class="col-md-2">
            @if($virement->isPaye())
                <span class="badge bg-label-success">Payé</span>
            @else
                <span class="badge bg-label-secondary">Non payé</span>
            @endif
        </div>
        <div class="col-md-2 text-muted small">Détail : {{ number_format($virement->lignes->sum('montant'), 0, ',', ' ') }}</div>
    </div>

    @if($bordereau->isBrouillon())
        <form action="{{ route('virements.poste.virements.update', [$bordereau, $virement]) }}" method="POST" class="row g-2 mb-3">
            @csrf @method('PUT')
            <div class="col-md-3"><input type="text" name="numero_vir" value="{{ $virement->numero_vir }}" class="form-control form-control-sm" required></div>
            <div class="col-md-3"><input type="text" name="montant" value="{{ number_format((float) $virement->montant, 0, ',', ' ') }}" class="form-control form-control-sm montant-sep" inputmode="numeric" required></div>
            <div class="col-md-3"><button class="btn btn-sm btn-label-primary">Maj VIR</button></div>
            <div class="col-md-3 text-end">
                <button form="del-vir-{{ $virement->id }}" class="btn btn-sm btn-label-danger" onclick="return confirm('Supprimer ce VIR ?')">Supprimer VIR</button>
            </div>
        </form>
        <form id="del-vir-{{ $virement->id }}" action="{{ route('virements.poste.virements.destroy', [$bordereau, $virement]) }}" method="POST">@csrf @method('DELETE')</form>
    @endif

    <div class="table-responsive mb-3">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>N° BE</th><th>N° Mdt</th><th>Ord.</th><th>Prog.</th><th>Créancier</th><th>Compte</th><th class="text-end">Montant</th>
                    @if($bordereau->isBrouillon())<th></th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($virement->lignes as $ligne)
                    <tr>
                        <td>{{ $ligne->num_be }}</td>
                        <td>{{ $ligne->num_mdt }}</td>
                        <td>{{ $ligne->ord }}</td>
                        <td>{{ $ligne->programme }}</td>
                        <td>{{ $ligne->creancier_libelle }}</td>
                        <td>{{ $ligne->num_compte }}</td>
                        <td class="text-end">{{ number_format((float) $ligne->montant, 0, ',', ' ') }}</td>
                        @if($bordereau->isBrouillon())
                            <td class="text-end">
                                <form action="{{ route('virements.poste.lignes.destroy', [$bordereau, $virement, $ligne]) }}" method="POST" onsubmit="return confirm('Supprimer ?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-label-danger btn-sm py-0">×</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-muted text-center">Aucune ligne détail</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bordereau->isBrouillon())
        <div class="border-top pt-3">
            <div class="text-muted small mb-2">Correction manuelle d’une ligne (optionnel)</div>
            <form action="{{ route('virements.poste.lignes.store', [$bordereau, $virement]) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-md-2"><input name="num_be" class="form-control form-control-sm" placeholder="N° BE"></div>
                <div class="col-md-2"><input name="num_mdt" class="form-control form-control-sm" placeholder="N° Mdt"></div>
                <div class="col-md-1"><input name="ord" class="form-control form-control-sm" placeholder="Ord"></div>
                <div class="col-md-2"><input name="programme" class="form-control form-control-sm" placeholder="Prog"></div>
                <div class="col-md-2"><input name="num_compte" class="form-control form-control-sm" value="NEANT" placeholder="Compte"></div>
                <div class="col-md-2"><input type="text" name="montant" class="form-control form-control-sm montant-sep" inputmode="numeric" placeholder="Montant" required></div>
                <div class="col-md-1"><button class="btn btn-sm btn-primary w-100" title="Ajouter une ligne">+</button></div>
            </form>
        </div>
    @endif
</x-vuexy.card>
@endforeach
@endsection

@push('scripts')
<script>
function formatMontantSep(input) {
    input.value = String(input.value || '').replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.montant-sep').forEach(function (input) {
        if (input.value) formatMontantSep(input);
        input.addEventListener('input', function () { formatMontantSep(this); });
    });
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.montant-sep, input[name="montant"]').forEach(function (input) {
                if (input.value) input.value = input.value.replace(/\s+/g, '');
            });
        });
    });
});
</script>
@endpush
