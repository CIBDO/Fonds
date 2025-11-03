@extends('layouts.master')

@section('content')
<div class="content container-fluid">
    <!-- En-tête de page -->
    <div class="page-header mb-4">
        <div class="row align-items-center">
            <div class="col">
                <div class="page-sub-header">
                    <h3 class="page-title fw-bold text-primary">
                        <i class="fas fa-building me-2"></i>Bureaux TRIE - CCIM
                    </h3>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('trie.cotisations.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-coins me-1"></i>Cotisations
                </a>
            </div>
        </div>
    </div>

    <!-- En-tête d'information -->
    @if(in_array(Auth::user()->role, ['admin', 'acct']))
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Vue Administrateur :</strong> Vous pouvez voir et gérer les bureaux de tous les postes.
    </div>
    @else
    <div class="alert alert-success mb-4">
        <i class="fas fa-building me-2"></i>
        <strong>Mon Poste :</strong> Vous gérez les bureaux de votre poste uniquement.
    </div>
    @endif

    <!-- Postes avec leurs bureaux -->
    @if($postes && $postes->count() > 0)
    <div class="row">
        @foreach($postes as $poste)
            @php
                $bureauxPoste = $poste->bureauxTrie;
            @endphp

            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>{{ $poste->nom }}
                        </h5>
                        <a href="{{ route('trie.bureaux.manage', $poste->id) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-cog"></i> Gérer les Bureaux
                        </a>
                    </div>
                    <div class="card-body">
                        @if($bureauxPoste->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> Code Bureau</th>
                                            <th><i class="fas fa-building"></i> Nom du Bureau</th>
                                            <th><i class="fas fa-info-circle"></i> Description</th>
                                            <th class="text-center"><i class="fas fa-toggle-on"></i> Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bureauxPoste as $bureau)
                                        <tr>
                                            <td><strong class="text-primary">{{ $bureau->code_bureau }}</strong></td>
                                            <td>{{ $bureau->nom_bureau }}</td>
                                            <td><small class="text-muted">{{ $bureau->description ?? '-' }}</small></td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $bureau->actif ? 'success' : 'secondary' }}">
                                                    <i class="fas fa-{{ $bureau->actif ? 'check' : 'times' }}-circle"></i>
                                                    {{ $bureau->actif ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <p class="mb-2"><strong>Aucun bureau enregistré pour ce poste.</strong></p>
                                <p class="text-muted mb-3">Vous devez créer au moins un bureau pour pouvoir saisir des cotisations TRIE.</p>
                                <a href="{{ route('trie.bureaux.manage', $poste->id) }}" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-1"></i>Créer le premier bureau
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-danger text-center">
        <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
        <h4>Aucun poste associé</h4>
        <p class="mb-0">Vous n'êtes pas associé à un poste. Veuillez contacter l'administrateur.</p>
    </div>
    @endif
</div>
@endsection

