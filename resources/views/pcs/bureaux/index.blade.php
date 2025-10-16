@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page moderne -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-building me-2"></i>Gestion des Bureaux de Douanes
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.bureaux.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouveau Bureau
                </a>
            </div>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Bureaux de Douanes</h5>
                <span class="badge bg-white text-danger">{{ $bureaux->count() }} bureaux</span>
            </div>
        </div>

        <div class="card-body">
            @if($bureaux->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="bureaux-table">
                    <thead class="table-light">
                        <tr>
                            <th width="10%"><i class="fas fa-hashtag"></i> Code</th>
                            <th width="40%"><i class="fas fa-tag"></i> Libellé</th>
                            <th width="20%"><i class="fas fa-map-marker-alt"></i> Poste RGD</th>
                            <th width="15%" class="text-center"><i class="fas fa-toggle-on"></i> Statut</th>
                            <th width="15%" class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bureaux as $bureau)
                        <tr>
                            <td>
                                <span class="badge bg-danger">{{ $bureau->code }}</span>
                            </td>
                            <td class="fw-bold">{{ $bureau->libelle }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $bureau->posteRgd->nom }}</span>
                            </td>
                            <td class="text-center">
                                @if($bureau->actif)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times-circle"></i> Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pcs.bureaux.edit', $bureau) }}"
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pcs.bureaux.toggle-actif', $bureau) }}"
                                          method="POST"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-outline-{{ $bureau->actif ? 'warning' : 'success' }}"
                                                data-bs-toggle="tooltip"
                                                title="{{ $bureau->actif ? 'Désactiver' : 'Activer' }}">
                                            <i class="fas fa-power-off"></i>
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
                                            <i class="fas fa-trash"></i>
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
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucun bureau de douane enregistré. Cliquez sur "Nouveau Bureau" pour commencer.</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#bureaux-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            },
            order: [[0, 'asc']],
            pageLength: 25
        });

        // Initialiser les tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
@endsection

