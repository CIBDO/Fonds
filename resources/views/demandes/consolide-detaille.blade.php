@extends('layouts.master')

@section('title', 'Vue Par Type de Personnel - Demandes de Fonds')

@section('content')

<x-vuexy.card title="Filtres" icon="tabler-filter" class="mb-4">
    <form method="GET" action="{{ route('demandes-fonds.consolide-detaille') }}">
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="poste" class="form-label">Poste</label>
                <select class="form-select" id="poste" name="poste">
                    <option value="">Tous les postes</option>
                    @foreach($postes as $poste)
                        <option value="{{ $poste->nom }}" {{ request('poste') == $poste->nom ? 'selected' : '' }}>{{ $poste->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="mois" class="form-label">Mois</label>
                <select class="form-select" id="mois" name="mois">
                    <option value="">Tous</option>
                    @foreach($mois as $moisItem)
                        <option value="{{ $moisItem }}" {{ request('mois') == $moisItem ? 'selected' : '' }}>{{ $moisItem }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="annee" class="form-label">Année</label>
                <select class="form-select" id="annee" name="annee">
                    <option value="">Toutes</option>
                    @foreach($annees as $annee)
                        <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>{{ $annee }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous</option>
                    <option value="en_attente" {{ request('status') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="approuve" {{ request('status') == 'approuve' ? 'selected' : '' }}>Approuvé</option>
                    <option value="rejete" {{ request('status') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="user_id" class="form-label">Trésorier</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">Tous</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <label for="date_type" class="form-label">Type de date</label>
                <select class="form-select" id="date_type" name="date_type">
                    <option value="">Sélectionner</option>
                    <option value="created_at" {{ request('date_type') == 'created_at' ? 'selected' : '' }}>Date création</option>
                    <option value="date_envois" {{ request('date_type') == 'date_envois' ? 'selected' : '' }}>Date envoi</option>
                    <option value="date_reception" {{ request('date_type') == 'date_reception' ? 'selected' : '' }}>Date réception</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-6 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-filter me-1"></i>Filtrer</button>
                <a href="{{ route('demandes-fonds.consolide-detaille') }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-x me-1"></i>Réinitialiser</a>
                <a href="{{ route('demandes-fonds.consolide-detaille.export-csv', request()->query()) }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV</a>
                <a href="{{ route('demandes-fonds.consolide-detaille.export-pdf', request()->query()) }}" class="btn btn-label-secondary"><i class="icon-base ti tabler-file-type-pdf me-1"></i>PDF</a>
            </div>
        </div>
    </form>
</x-vuexy.card>

    <!-- Résumé des filtres appliqués -->
    @if(request()->hasAny(['poste', 'mois', 'annee', 'status', 'user_id', 'date_debut', 'date_fin']))
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6 class="mb-2"><i class="icon-base ti tabler-info-circle me-2"></i>Filtres appliqués</h6>
                <div class="d-flex flex-wrap gap-2">
                    @if(request('poste'))
                        <span class="badge bg-label-secondary">Poste : {{ request('poste') }}</span>
                    @endif
                    @if(request('mois'))
                        <span class="badge bg-label-secondary">Mois : {{ request('mois') }}</span>
                    @endif
                    @if(request('annee'))
                        <span class="badge bg-label-secondary">Année : {{ request('annee') }}</span>
                    @endif
                    @if(request('status'))
                        <span class="badge bg-label-secondary">Statut : {{ ucfirst(request('status')) }}</span>
                    @endif
                    @if(request('user_id'))
                        @php
                            $selectedUser = $users->where('id', request('user_id'))->first();
                        @endphp
                        @if($selectedUser)
                            <span class="badge bg-label-secondary">Trésorier : {{ $selectedUser->name }}</span>
                        @endif
                    @endif
                    @if(request('date_debut'))
                        <span class="badge bg-label-secondary">Du : {{ \Carbon\Carbon::parse(request('date_debut'))->format('d/m/Y') }}</span>
                    @endif
                    @if(request('date_fin'))
                        <span class="badge bg-label-secondary">Au : {{ \Carbon\Carbon::parse(request('date_fin'))->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

@if(count($typesPersonnel) > 0)
<div class="row g-4 mb-4">
    @include('partials.demandes.stat-box', ['label' => 'Total Net', 'value' => number_format($totaux['total_net'], 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Total Reversement', 'value' => number_format($totaux['total_revers'], 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Total Courant', 'value' => number_format($totaux['total_courant'], 0, ',', ' ') . ' FCFA'])
    @include('partials.demandes.stat-box', ['label' => 'Total Demande', 'value' => number_format($totaux['total_demande'], 0, ',', ' ') . ' FCFA'])
</div>
@endif

<x-vuexy.card title="Montants par type de personnel" icon="tabler-table">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Désignation</th>
                                    <th class="text-end" style="width: 15%;">Salaire Net (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Reversement (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Total Courant (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Salaire Ancien (FCFA)</th>
                                    <th class="text-end" style="width: 15%;">Ecart (FCFA)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($typesPersonnel as $type)
                                <tr>
                    <td class="fw-medium">{{ $type['designation'] }}</td>
                    <td class="text-end">{{ number_format($type['net'], 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($type['revers'], 0, ',', ' ') }}</td>
                    <td class="text-end fw-medium">{{ number_format($type['total_courant'], 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($type['salaire_ancien'], 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($type['total_demande'], 0, ',', ' ') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-body-secondary py-4">
                                        <i class="icon-base ti tabler-inbox icon-lg d-block mb-2"></i>
                                        Aucune donnée trouvée
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($typesPersonnel) > 0)
                            <tfoot class="table-light">
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
</x-vuexy.card>
@endsection
