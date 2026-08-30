@extends('layouts.master')

@section('title', 'Gestion des Bureaux de Douanes')

@include('partials.vuexy.datatables-assets')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('pcs.bureaux.create') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouveau Bureau
    </a>
</div>

<x-vuexy.card title="Liste des Bureaux de Douanes" icon="tabler-list">
    <x-slot:header>
        <span class="badge bg-label-primary">{{ $bureaux->count() }} bureaux</span>
    </x-slot:header>

    @if($bureaux->count() > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle" id="bureaux-table">
            <thead>
                <tr>
                    <th width="10%">Code</th>
                    <th width="40%">Libellé</th>
                    <th width="20%">Poste RGD</th>
                    <th width="15%" class="text-center">Statut</th>
                    <th width="15%" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bureaux as $bureau)
                <tr>
                    <td>
                        <span class="badge bg-label-primary">{{ $bureau->code }}</span>
                    </td>
                    <td class="fw-bold">{{ $bureau->libelle }}</td>
                    <td>
                        <span class="badge bg-label-secondary">{{ $bureau->posteRgd->nom }}</span>
                    </td>
                    <td class="text-center">
                        @if($bureau->actif)
                            <span class="badge bg-label-success">
                                <i class="ti tabler-circle-check"></i> Actif
                            </span>
                        @else
                            <span class="badge bg-label-secondary">
                                <i class="ti tabler-circle-x"></i> Inactif
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('pcs.bureaux.edit', $bureau) }}"
                               class="btn btn-outline-primary"
                               data-bs-toggle="tooltip"
                               title="Modifier">
                                <i class="ti tabler-edit"></i>
                            </a>
                            <form action="{{ route('pcs.bureaux.toggle-actif', $bureau) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                <button type="submit"
                                        class="btn btn-outline-{{ $bureau->actif ? 'warning' : 'success' }}"
                                        data-bs-toggle="tooltip"
                                        title="{{ $bureau->actif ? 'Désactiver' : 'Activer' }}">
                                    <i class="ti tabler-power"></i>
                                </button>
                            </form>
                            <form action="{{ route('pcs.bureaux.destroy', $bureau) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bureau ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="tooltip"
                                        title="Supprimer">
                                    <i class="ti tabler-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <x-vuexy.alert type="info">
        Aucun bureau de douane enregistré. Cliquez sur « Nouveau Bureau » pour commencer.
    </x-vuexy.alert>
    @endif
</x-vuexy.card>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#bureaux-table').DataTable({
            language: datatablesFrench,
            order: [[0, 'asc']],
            pageLength: 25,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p">>'
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
