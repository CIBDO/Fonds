@extends('layouts.master')

@section('title', 'Gestion des Postes')

@include('partials.vuexy.datatables-assets')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPosteModal">
        <i class="icon-base ti tabler-plus me-1"></i>Nouveau Poste / RP
    </button>
</div>

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('postes.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" value="{{ $nom }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="">Tous</option>
                <option value="superieur" {{ ($type ?? '') === 'superieur' ? 'selected' : '' }}>Poste supérieur</option>
                <option value="rp" {{ ($type ?? '') === 'rp' ? 'selected' : '' }}>Recette-Perception</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">
                <i class="icon-base ti tabler-filter me-1"></i>Filtrer
            </button>
        </div>
    </form>
</x-vuexy.card>

<x-vuexy.card title="Liste des Postes" icon="tabler-briefcase">
    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="postes-table">
            <thead>
                <tr>
                    <th width="8%">ID</th>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Rattaché à</th>
                    <th>Intitulé / Région</th>
                    <th width="12%" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($postes as $poste)
                <tr>
                    <td><span class="badge bg-label-primary">{{ $poste->id }}</span></td>
                    <td>
                        <h6 class="mb-0">{{ $poste->libelleAffiche() }}</h6>
                    </td>
                    <td>
                        @if($poste->isRp())
                            <span class="badge bg-label-info">RP</span>
                        @else
                            <span class="badge bg-label-secondary">Poste supérieur</span>
                        @endif
                    </td>
                    <td>{{ $poste->parent->nom ?? '—' }}</td>
                    <td>
                        <div class="small">{{ $poste->intitule_officiel ?: '—' }}</div>
                        <div class="text-body-secondary small">{{ $poste->region ?: '' }}</div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                                data-bs-toggle="modal"
                                data-bs-target="#editPosteModal{{ $poste->id }}"
                                title="Modifier le poste">
                            <i class="icon-base ti tabler-edit icon-22px"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
        <div class="text-body-secondary small">
            Affichage de {{ $postes->firstItem() ?? 0 }} à {{ $postes->lastItem() ?? 0 }} sur {{ $postes->total() }} résultats
        </div>
        {{ $postes->links('custom.pagination') }}
    </div>
</x-vuexy.card>

@include('postes.add')
@include('postes.edit')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#postes-table').DataTable({
        language: typeof datatablesFrench !== 'undefined' ? datatablesFrench : window.DGTCP_DATATABLES_FR,
        order: [[0, 'desc']],
        paging: false,
        searching: true,
        ordering: true,
        responsive: true
    });

    function toggleParentField(select, parentWrap) {
        if (!select || !parentWrap) return;
        parentWrap.style.display = select.value === 'rp' ? '' : 'none';
        const input = parentWrap.querySelector('select, input');
        if (input) input.required = select.value === 'rp';
    }

    document.querySelectorAll('[data-poste-type]').forEach(function (select) {
        const wrap = document.querySelector(select.dataset.posteType);
        toggleParentField(select, wrap);
        select.addEventListener('change', function () {
            toggleParentField(select, wrap);
        });
    });
});
</script>
@endpush
