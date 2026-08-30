@extends('layouts.master')

@section('title', 'Gestion des Postes')

@include('partials.vuexy.datatables-assets')

@section('content')
<x-vuexy.page-header title="Gestion des Postes" subtitle="Administration des postes DGTCP">
    <x-slot:actions>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPosteModal">
            <i class="icon-base ti tabler-plus me-1"></i>Nouveau Poste
        </button>
    </x-slot:actions>
</x-vuexy.page-header>

<x-vuexy.card title="Liste des Postes" icon="tabler-briefcase">
    <div class="table-responsive">
        <table class="table table-bordered align-middle" id="postes-table">
            <thead>
                <tr>
                    <th width="10%">ID</th>
                    <th>Nom du Poste</th>
                    <th width="15%" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($postes as $poste)
                <tr>
                    <td><span class="badge bg-label-primary">{{ $poste->id }}</span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-3">
                                <span class="avatar-initial rounded-circle bg-label-secondary">
                                    <i class="icon-base ti tabler-user"></i>
                                </span>
                            </span>
                            <div>
                                <h6 class="mb-0">{{ $poste->nom }}</h6>
                                <small class="text-body-secondary">Poste #{{ $poste->id }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#editPosteModal{{ $poste->id }}"
                                title="Modifier le poste">
                            <i class="icon-base ti tabler-edit"></i>
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
});
</script>
@endpush
