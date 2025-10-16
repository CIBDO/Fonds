@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-folder-open me-2"></i>Autres Demandes Financières
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.autres-demandes.create') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-plus me-1"></i>Nouvelle Demande
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Année</label>
                    <select name="annee" class="form-select">
                        <option value="">Toutes</option>
                        @for($i = date('Y'); $i >= date('Y') - 3; $i--)
                            <option value="{{ $i }}" {{ request('annee') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Statut</label>
                    <select name="statut" class="form-select">
                        <option value="">Tous</option>
                        <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                        <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                        <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-danger d-block w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des demandes -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-danger text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Demandes</h5>
                <span class="badge bg-white text-danger">{{ $demandes->total() }} demandes</span>
            </div>
        </div>

        <div class="card-body">
            @if($demandes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-building"></i> Poste</th>
                            <th><i class="fas fa-tag"></i> Désignation</th>
                            <th class="text-end"><i class="fas fa-money-bill-wave"></i> Montant</th>
                            <th class="text-center"><i class="fas fa-flag"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandes as $demande)
                        <tr>
                            <td>{{ $demande->date_demande->format('d/m/Y') }}</td>
                            <td><span class="badge bg-primary poste-badge">{{ $demande->poste->nom }}</span></td>
                            <td class="fw-bold">{{ Str::limit($demande->designation, 50) }}</td>
                            <td class="text-end">
                                <div class="fw-bold text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</div>
                                @if($demande->montant_accord !== null)
                                    <div class="small text-success">Accordé: {{ number_format($demande->montant_accord, 0, ',', ' ') }} FCFA</div>
                                    @if($demande->montant_accord != $demande->montant)
                                        <div class="small">
                                            <span class="badge {{ $demande->montant_accord > $demande->montant ? 'bg-warning' : 'bg-info' }} montant-badge">
                                                {{ $demande->montant_accord > $demande->montant ? '+' : '' }}{{ number_format($demande->montant_accord - $demande->montant, 0, ',', ' ') }}
                                                ({{ $demande->montant > 0 ? round(($demande->montant_accord / $demande->montant) * 100, 1) : 0 }}%)
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($demande->statut)
                                    @case('brouillon')
                                        <span class="badge bg-secondary">Brouillon</span>
                                        @break
                                    @case('soumis')
                                        <span class="badge bg-primary">Soumis</span>
                                        @break
                                    @case('valide')
                                        <span class="badge bg-success">Validé</span>
                                        @break
                                    @case('rejete')
                                        <span class="badge bg-danger">Rejeté</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('pcs.autres-demandes.show', $demande) }}"
                                       class="btn btn-outline-info"
                                       data-bs-toggle="tooltip"
                                       title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($demande->statut == 'brouillon' && $demande->saisi_par == auth()->id())
                                        <a href="{{ route('pcs.autres-demandes.edit', $demande) }}"
                                           class="btn btn-outline-primary"
                                           data-bs-toggle="tooltip"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->peut_valider_pcs && $demande->statut == 'soumis')
                                        <button type="button"
                                                class="btn btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#validationModal{{ $demande->id }}"
                                                data-bs-toggle="tooltip"
                                                title="Valider avec montant">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $demandes->links() }}
            </div>
            @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucune demande trouvée. Cliquez sur "Nouvelle Demande" pour commencer.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales de Validation -->
@foreach($demandes as $demande)
@if($demande->statut == 'soumis')
<div class="modal fade" id="validationModal{{ $demande->id }}" tabindex="-1" aria-labelledby="validationModalLabel{{ $demande->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="validationModalLabel{{ $demande->id }}">
                    <i class="fas fa-check-circle me-2"></i>Valider la Demande
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('pcs.autres-demandes.valider', $demande) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Poste :</strong> {{ $demande->poste->nom }}
                        </div>
                        <div class="col-md-6">
                            <strong>Date :</strong> {{ $demande->date_demande->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>Désignation :</strong>
                        <p class="text-muted">{{ $demande->designation }}</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title text-primary">Montant Demandé</h6>
                                    <h4 class="text-primary">{{ number_format($demande->montant, 0, ',', ' ') }} FCFA</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Montant à Accorder</h6>
                                    <div class="input-group">
                                        <input type="number"
                                               class="form-control form-control-lg text-center"
                                               name="montant_accord"
                                               id="montant_accord{{ $demande->id }}"
                                               value="{{ $demande->montant }}"
                                               step="0.01"
                                               min="0"
                                               max="{{ $demande->montant * 2 }}"
                                               required>
                                        <span class="input-group-text">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($demande->observation)
                    <div class="mb-3">
                        <strong>Observation :</strong>
                        <p class="text-muted">{{ $demande->observation }}</p>
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note :</strong> Vous pouvez accorder un montant différent de celui demandé selon les disponibilités budgétaires.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Valider avec ce Montant
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endforeach

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush
@endsection

