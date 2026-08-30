@extends('layouts.master')

@section('title', 'Situation des Demandes de Fonds')

@include('partials.vuexy.datatables-assets')

@section('content')
<div class="d-flex justify-content-end mb-4">
    <span class="badge bg-label-secondary">{{ $demandeFonds->count() }} demande(s)</span>
</div>

<x-vuexy.card title="Liste des demandes" icon="tabler-chart-pie">
    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mois</th>
                    <th>Date Réception</th>
                    <th>Poste</th>
                    <th class="text-end">Montant Demandé</th>
                    <th class="text-end">Montant Envoyé</th>
                    <th>Statut</th>
                    <th class="text-center" width="80">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->mois }}</td>
                    <td>{{ date('d/m/Y', strtotime($demande->date_reception)) }}</td>
                    <td>{{ $demande->poste->nom }}</td>
                    <td class="text-end">{{ number_format($demande->solde, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($demande->montant, 0, ',', ' ') }}</td>
                    <td>@include('partials.demandes.status-badge', ['status' => $demande->status])</td>
                    <td>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip" title="Générer PDF" target="_blank">
                                <i class="icon-base ti tabler-file-type-pdf icon-22px"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-vuexy.card>
@endsection

@push('scripts')
@include('partials.demandes.datatable-init', ['tableId' => 'demandes-table', 'orderCol' => 1, 'title' => 'Situation des Demandes'])
@endpush
