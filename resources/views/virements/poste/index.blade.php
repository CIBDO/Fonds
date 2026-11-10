@extends('layouts.master')

@section('title', 'Propositions de virements')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="ti tabler-check me-1"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="ti tabler-alert-circle me-1"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h4 class="mb-0"><i class="ti tabler-building-bank me-2"></i>Propositions de virements</h4>
    <a href="{{ route('virements.poste.template') }}" class="btn btn-label-secondary btn-sm">
        <i class="ti tabler-file-spreadsheet me-1"></i>Template Excel lignes
    </a>
</div>

@if($demandesApprouvees->isNotEmpty())
<x-vuexy.card title="Créer un bordereau depuis une demande approuvée" icon="tabler-circle-plus" class="mb-4">
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Poste</th>
                    <th>Mois</th>
                    <th>Année</th>
                    <th class="text-end">Montant envoyé</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandesApprouvees as $demande)
                    <tr>
                        <td>{{ $demande->poste->nom ?? '-' }}</td>
                        <td>{{ $demande->mois }}</td>
                        <td>{{ $demande->annee }}</td>
                        <td class="text-end">{{ number_format((float) $demande->montant, 0, ',', ' ') }}</td>
                        <td class="text-end">
                            <a href="{{ route('virements.poste.create', ['demande_fonds_id' => $demande->id]) }}" class="btn btn-sm btn-primary">
                                Créer bordereau
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endif

<x-vuexy.card title="Mes bordereaux" icon="tabler-list">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Poste</th>
                    <th>Mois</th>
                    <th>Statut</th>
                    <th class="text-end">Nb VIR</th>
                    <th class="text-end">Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bordereaux as $b)
                    <tr>
                        <td>{{ $b->poste->nom ?? '-' }}</td>
                        <td>{{ $b->mois }} {{ $b->annee }}</td>
                        <td>
                            @if($b->status === 'soumis')
                                <span class="badge bg-label-success">Soumis</span>
                            @else
                                <span class="badge bg-label-warning">Brouillon</span>
                            @endif
                        </td>
                        <td class="text-end">{{ $b->virements->count() }}</td>
                        <td class="text-end">{{ number_format($b->virements->sum('montant'), 0, ',', ' ') }}</td>
                        <td class="text-end">
                            <a href="{{ route('virements.poste.show', $b) }}" class="btn btn-sm btn-label-primary">Ouvrir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Aucun bordereau pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $bordereaux->links() }}
</x-vuexy.card>
@endsection
