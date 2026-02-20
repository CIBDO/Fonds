@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-eye me-2"></i>Détail de la Cotisation TRIE
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.cotisations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
                <span class="badge bg-success fs-6"><i class="fas fa-check-circle"></i> Validé</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%"><i class="fas fa-calendar text-primary"></i> Période:</th>
                            <td><strong>{{ $cotisation->nom_mois }} {{ $cotisation->annee }}</strong></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-map-marker-alt text-primary"></i> Poste:</th>
                            <td><strong>{{ $cotisation->poste->nom }}</strong></td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-building text-primary"></i> Bureau:</th>
                            <td>
                                <span class="text-primary">{{ $cotisation->bureauTrie->code_bureau }}</span> - 
                                <span class="text-muted">{{ $cotisation->bureauTrie->nom_bureau }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%"><i class="fas fa-user text-primary"></i> Saisi par:</th>
                            <td>{{ $cotisation->saisiPar->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-clock text-primary"></i> Date saisie:</th>
                            <td>{{ $cotisation->date_saisie->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-user-check text-success"></i> Validé par:</th>
                            <td>{{ $cotisation->validePar->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th><i class="fas fa-check-circle text-success"></i> Date validation:</th>
                            <td>{{ $cotisation->date_validation?->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Montants -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Détail des Montants</h5>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="text-primary"><i class="fas fa-money-bill-wave"></i> Cotisation Courante</h6>
                            <h3 class="text-primary fw-bold">
                                {{ number_format($cotisation->montant_cotisation_courante, 0, ',', ' ') }} FCFA
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6 class="text-warning"><i class="fas fa-undo"></i> Apurement</h6>
                            <h3 class="text-warning fw-bold">
                                {{ number_format($cotisation->montant_apurement, 0, ',', ' ') }} FCFA
                            </h3>
                            @if($cotisation->detail_apurement)
                            <small class="text-muted">{{ $cotisation->detail_apurement }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-success"><i class="fas fa-coins"></i> Montant Total</h6>
                            <h3 class="text-success fw-bold">
                                {{ number_format($cotisation->montant_total, 0, ',', ' ') }} FCFA
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de paiement -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Informations de Paiement</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="fw-bold text-muted">Mode de Paiement:</label>
                    <p>
                        @if($cotisation->mode_paiement)
                            <span class="badge bg-secondary">{{ ucfirst($cotisation->mode_paiement) }}</span>
                        @else
                            <span class="text-muted">Non renseigné</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">Référence de Paiement:</label>
                    <p>{{ $cotisation->reference_paiement ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <label class="fw-bold text-muted">Date de Paiement:</label>
                    <p>{{ $cotisation->date_paiement?->format('d/m/Y') ?? '-' }}</p>
                </div>
            </div>
            @if($cotisation->preuve_paiement)
            <div class="row mt-3">
                <div class="col-12">
                    <label class="fw-bold text-muted">Preuve de paiement:</label>
                    <p>
                        <a href="{{ route('trie.cotisations.preuve', $cotisation) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-paperclip me-1"></i>Télécharger le fichier
                        </a>
                    </p>
                </div>
            </div>
            @endif
            @if($cotisation->observation)
            <div class="row mt-3">
                <div class="col-12">
                    <label class="fw-bold text-muted">Observation:</label>
                    <p class="border rounded p-3 bg-light">{{ $cotisation->observation }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    @php
                        $user = Auth::user();
                        $peutModifier = in_array($user->role, ['admin', 'acct']) || $user->poste_id == $cotisation->poste_id;
                    @endphp
                    
                    @if($peutModifier)
                        <a href="{{ route('trie.cotisations.edit', $cotisation) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalSuppression">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    @endif
                </div>
                <div>
                    <a href="{{ route('trie.cotisations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmation Suppression -->
<div class="modal fade" id="modalSuppression" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('trie.cotisations.destroy', $cotisation) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la Suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention !</strong> Cette action est irréversible.
                    </div>
                    <p>Êtes-vous sûr de vouloir supprimer cette cotisation ?</p>
                    <div class="card border-danger">
                        <div class="card-body">
                            <p class="mb-1"><strong>Période :</strong> {{ $cotisation->nom_mois }} {{ $cotisation->annee }}</p>
                            <p class="mb-1"><strong>Bureau :</strong> {{ $cotisation->bureauTrie->nom_complet }}</p>
                            <p class="mb-0"><strong>Montant Total :</strong> <span class="text-danger">{{ number_format($cotisation->montant_total, 0, ',', ' ') }} FCFA</span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Confirmer la Suppression
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
