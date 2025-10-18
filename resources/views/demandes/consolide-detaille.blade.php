@extends('layouts.master')

@section('title', 'Vue Par Type de Personnel - Demandes de Fonds')

@section('content')
<div class="content">
    <!-- En-tête de page -->
    <div class="page-header">
        <div class="add-item d-flex">
            <div class="page-title">
                <h4>
                    <i class="fas fa-users text-primary me-2"></i>
                    Vue Par Type de Personnel
                </h4>
                <h6 class="text-muted">Montants alloués par catégorie de personnel selon les filtres appliqués</h6>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-filter text-primary me-2"></i>
                        Filtres de recherche
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('demandes-fonds.consolide-detaille') }}">
                        <div class="row">
                            <!-- Filtre par poste -->
                            <div class="col-md-3 mb-3">
                                <label for="poste" class="form-label">Poste</label>
                                <select class="form-select" id="poste" name="poste">
                                    <option value="">Tous les postes</option>
                                    @foreach($postes as $poste)
                                        <option value="{{ $poste->nom }}" {{ request('poste') == $poste->nom ? 'selected' : '' }}>
                                            {{ $poste->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par mois -->
                            <div class="col-md-2 mb-3">
                                <label for="mois" class="form-label">Mois</label>
                                <select class="form-select" id="mois" name="mois">
                                    <option value="">Tous les mois</option>
                                    @foreach($mois as $moisItem)
                                        <option value="{{ $moisItem }}" {{ request('mois') == $moisItem ? 'selected' : '' }}>
                                            {{ $moisItem }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par année -->
                            <div class="col-md-2 mb-3">
                                <label for="annee" class="form-label">Année</label>
                                <select class="form-select" id="annee" name="annee">
                                    <option value="">Toutes les années</option>
                                    @foreach($annees as $annee)
                                        <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                                            {{ $annee }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtre par statut -->
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">Statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Tous les statuts</option>
                                    <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="approuve" {{ request('status') == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                                    <option value="rejete" {{ request('status') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>

                            <!-- Filtre par trésorier -->
                            <div class="col-md-3 mb-3">
                                <label for="user_id" class="form-label">Trésorier</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="">Tous les trésoriers</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Filtres de dates -->
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="date_type" class="form-label">Type de date</label>
                                <select class="form-select" id="date_type" name="date_type">
                                    <option value="">Sélectionner</option>
                                    <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                    <option value="date_envois" {{ request('date_type') == 'date_envois' ? 'selected' : '' }}>Date d'envoi</option>
                                    <option value="date_reception" {{ request('date_type') == 'date_reception' ? 'selected' : '' }}>Date de réception</option>
                                </select>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="date_debut" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                            </div>

                            <div class="col-md-2 mb-3">
                                <label for="date_fin" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                            </div>

                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('demandes-fonds.consolide-detaille') }}" class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-1"></i>Réinitialiser
                                </a>
                                <a href="{{ route('demandes-fonds.consolide-detaille.export-csv', request()->query()) }}" class="btn btn-success me-2">
                                    <i class="fas fa-file-csv me-1"></i>Export CSV
                                </a>
                                <a href="{{ route('demandes-fonds.consolide-detaille.export-pdf', request()->query()) }}" class="btn btn-danger">
                                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé des filtres appliqués -->
    @if(request()->hasAny(['poste', 'mois', 'annee', 'status', 'user_id', 'date_debut', 'date_fin']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Filtres appliqués :</h6>
                <div class="d-flex flex-wrap gap-2">
                    @if(request('poste'))
                        <span class="badge bg-primary">Poste : {{ request('poste') }}</span>
                    @endif
                    @if(request('mois'))
                        <span class="badge bg-primary">Mois : {{ request('mois') }}</span>
                    @endif
                    @if(request('annee'))
                        <span class="badge bg-primary">Année : {{ request('annee') }}</span>
                    @endif
                    @if(request('status'))
                        <span class="badge bg-primary">Statut : {{ ucfirst(request('status')) }}</span>
                    @endif
                    @if(request('user_id'))
                        @php
                            $selectedUser = $users->where('id', request('user_id'))->first();
                        @endphp
                        @if($selectedUser)
                            <span class="badge bg-primary">Trésorier : {{ $selectedUser->name }}</span>
                        @endif
                    @endif
                    @if(request('date_debut'))
                        <span class="badge bg-primary">Du : {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}</span>
                    @endif
                    @if(request('date_fin'))
                        <span class="badge bg-primary">Au : {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Cartes de totaux -->
    {{-- <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Net</h6>
                            <h4>{{ number_format($totaux['total_net'], 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Reversement</h6>
                            <h4>{{ number_format($totaux['total_revers'], 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Courant</h6>
                            <h4>{{ number_format($totaux['total_courant'], 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="card-title">Total Demande</h6>
                            <h4>{{ number_format($totaux['total_demande'], 0, ',', ' ') }} FCFA</h4>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-invoice-dollar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Tableau agrégé par type de personnel -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table text-primary me-2"></i>
                        Montants par Type de Personnel
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 25%;">Désignation</th>
                                    <th class="text-end" style="width: 15%;">Salaire Net (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Reversement (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Total Courant (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Salaire Ancien (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Total Demande (FCFA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($typesPersonnel as $type)
                                <tr>
                                    <td><strong><i class="fas fa-user-tie me-2 text-primary"></i>{{ $type['designation'] }}</strong></td>
                                    <td class="text-end">{{ number_format($type['net'], 0, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($type['revers'], 0, ',', ' ') }}</td>
                                    <td class="text-end"><strong class="text-primary">{{ number_format($type['total_courant'], 0, ',', ' ') }}</strong></td>
                                    <td class="text-end">{{ number_format($type['salaire_ancien'], 0, ',', ' ') }}</td>
                                    <td class="text-end"><strong class="text-success">{{ number_format($type['total_demande'], 0, ',', ' ') }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                        Aucune donnée trouvée avec les critères sélectionnés
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($typesPersonnel) > 0)
                            <tfoot class="table-dark">
                                <tr>
                                    <th>TOTAUX GÉNÉRAUX</th>
                                    <th class="text-end">{{ number_format($totaux['total_net'], 0, ',', ' ') }}</th>
                                    <th class="text-end">{{ number_format($totaux['total_revers'], 0, ',', ' ') }}</th>
                                    <th class="text-end">{{ number_format($totaux['total_courant'], 0, ',', ' ') }}</th>
                                    <th class="text-end">{{ number_format($totaux['total_ancien'], 0, ',', ' ') }}</th>
                                    <th class="text-end">{{ number_format($totaux['total_demande'], 0, ',', ' ') }}</th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <!-- Info supplémentaire -->

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .gap-2 {
        gap: 0.5rem !important;
    }

    .badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
</style>
@endpush
