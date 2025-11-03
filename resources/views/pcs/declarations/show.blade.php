@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-danger">
                        <i class="fas fa-file-alt me-2"></i>Détail de la Déclaration PCS
                    </h3>
                    {{-- <p class="text-muted mb-0">
                        {{ $declaration->nom_entite }} - {{ \Carbon\Carbon::create()->month($declaration->mois)->locale('fr')->translatedFormat('F') }} {{ $declaration->annee }}
                    </p> --}}
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('pcs.declarations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations Principales -->
        <div class="col-lg-8">
            <!-- Statut et Informations générales -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Statut</label>
                            <div>
                                @switch($declaration->statut)
                                    @case('brouillon')
                                        <span class="badge bg-secondary fs-6"><i class="fas fa-pencil-alt"></i> Brouillon</span>
                                        @break
                                    @case('soumis')
                                        <span class="badge bg-primary fs-6"><i class="fas fa-paper-plane"></i> Soumis</span>
                                        @break
                                    @case('valide')
                                        <span class="badge bg-success fs-6"><i class="fas fa-check-circle"></i> Validé</span>
                                        @break
                                    @case('rejete')
                                        <span class="badge bg-danger fs-6"><i class="fas fa-times-circle"></i> Rejeté</span>
                                        @break
                                @endswitch
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Programme</label>
                            <div>
                                <span class="badge bg-{{ $declaration->programme == 'UEMOA' ? 'success' : 'warning' }} fs-6">
                                    {{ $declaration->programme }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Période</label>
                            <div class="fw-bold">
                                {{ \Carbon\Carbon::create()->month($declaration->mois)->locale('fr')->translatedFormat('F') }} {{ $declaration->annee }}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Type d'entité</label>
                            <div>
                                @if($declaration->poste_id)
                                    <span class="badge bg-primary">Poste : {{ $declaration->poste->nom }}</span>
                                @else
                                    <span class="badge bg-info">Bureau : {{ $declaration->bureauDouane->libelle }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Montants -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Montants Déclarés</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-arrow-up text-success fa-2x mb-2"></i>
                                <div class="text-muted small">Recouvrement</div>
                                <div class="fs-5 fw-bold text-success">
                                    {{ number_format($declaration->montant_recouvrement, 0, ',', ' ') }} <small>FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-arrow-down text-primary fa-2x mb-2"></i>
                                <div class="text-muted small">Reversement</div>
                                <div class="fs-5 fw-bold text-primary">
                                    {{ number_format($declaration->montant_reversement, 0, ',', ' ') }} <small>FCFA</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="p-3 bg-light rounded">
                                <i class="fas fa-balance-scale text-warning fa-2x mb-2"></i>
                                <div class="text-muted small">Reste à Reverser</div>
                                <div class="fs-5 fw-bold text-warning">
                                    {{ number_format($declaration->reste_a_reverser, 0, ',', ' ') }} <small>FCFA</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($declaration->observation)
                    <div class="mt-3">
                        <label class="text-muted small">Observation</label>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-comment-alt me-2"></i>
                            {{ $declaration->observation }}
                        </div>
                    </div>
                    @endif

                    @if($declaration->motif_rejet)
                    <div class="mt-3">
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-exclamation-triangle me-2"></i>Motif du rejet :</strong>
                            <p class="mb-0 mt-2">{{ $declaration->motif_rejet }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>


            <!-- Pièces jointes -->
            @if($declaration->piecesJointes->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>Pièces Jointes ({{ $declaration->piecesJointes->count() }})</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($declaration->piecesJointes as $piece)
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <strong>{{ $piece->nom_original }}</strong>
                                    <small class="text-muted">({{ $piece->taille_formatee }})</small>
                                </div>
                                <a href="#" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Historique et Traçabilité -->
        <div class="col-lg-4">
            <!-- Traçabilité -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Traçabilité</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <small class="text-muted">Saisi par</small>
                            <div class="fw-bold">{{ $declaration->saisiPar->name }}</div>
                            <small class="text-muted">{{ $declaration->date_saisie->format('d/m/Y à H:i') }}</small>
                        </li>
                        @if($declaration->date_soumission)
                        <li class="mb-3">
                            <small class="text-muted">Soumis le</small>
                            <div class="fw-bold">{{ $declaration->date_soumission->format('d/m/Y à H:i') }}</div>
                        </li>
                        @endif
                        @if($declaration->validePar)
                        <li class="mb-3">
                            <small class="text-muted">Validé par</small>
                            <div class="fw-bold">{{ $declaration->validePar->name }}</div>
                            @if($declaration->date_validation)
                            <small class="text-muted">{{ $declaration->date_validation->format('d/m/Y à H:i') }}</small>
                            @endif
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Historique des changements -->
            @if($declaration->historiqueStatuts->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Historique des Changements</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($declaration->historiqueStatuts->sortByDesc('date_changement') as $historique)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-circle text-danger" style="font-size: 8px;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="fw-bold">{{ $historique->nouveau_statut }}</div>
                                    <small class="text-muted">
                                        {{ $historique->utilisateur->name }}<br>
                                        {{ $historique->date_changement->format('d/m/Y à H:i') }}
                                    </small>
                                    @if($historique->commentaire)
                                    <div class="mt-1 small text-muted">
                                        <em>{{ $historique->commentaire }}</em>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

