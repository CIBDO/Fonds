@extends('layouts.master')

@section('title', 'Demandes de Fonds')

@include('partials.vuexy.datatables-assets')

@section('content')
@if(auth()->user()->hasAnyRole(['admin', 'tresorier']))
<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('demandes-fonds.create') }}" class="btn btn-primary btn-sm">
        <i class="icon-base ti tabler-plus me-1"></i>Nouvelle Demande
    </a>
</div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="icon-base ti tabler-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="icon-base ti tabler-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<x-vuexy.card title="Liste des demandes" icon="tabler-list">
    <x-slot:actions>
        <span class="badge bg-label-secondary">{{ $demandeFonds->count() }} demande(s)</span>
    </x-slot:actions>

    <div class="table-responsive">
        <table id="demandes-table" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Mois</th>
                    <th>Date Réception</th>
                    <th>Poste</th>
                    <th class="text-end">Montant Demandé</th>
                    <th>Date Demande</th>
                    <th>Statut</th>
                    <th class="text-center" width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demandeFonds as $demande)
                <tr>
                    <td class="fw-medium">{{ $demande->mois }}</td>
                    <td>{{ date('d/m/Y', strtotime($demande->date_reception)) }}</td>
                    <td>{{ $demande->poste->nom }}</td>
                    <td class="text-end fw-medium">
                        {{ number_format(abs($demande->solde), 0, ',', ' ') }} <small class="text-body-secondary">FCFA</small>
                    </td>
                    <td>{{ date('d/m/Y', strtotime($demande->created_at)) }}</td>
                    <td>
                        @if($demande->status === 'approuve')
                            <span class="badge bg-label-success">
                                <i class="icon-base ti tabler-circle-check me-1"></i>Approuvé
                            </span>
                        @elseif($demande->status === 'rejete')
                            <span class="badge bg-label-danger">
                                <i class="icon-base ti tabler-circle-x me-1"></i>Rejeté
                            </span>
                        @else
                            <span class="badge bg-label-warning">
                                <i class="icon-base ti tabler-clock me-1"></i>En attente
                            </span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-1">
                            <a href="{{ route('demandes-fonds.show', $demande->id) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip" title="Voir les détails">
                                <i class="icon-base ti tabler-eye icon-22px"></i>
                            </a>

                            @if(auth()->user()->role === 'admin' || (auth()->user()->role === 'tresorier' && ((int) $demande->user_id === (int) auth()->id() || (auth()->user()->poste_id && $demande->poste_id && (int) $demande->poste_id === (int) auth()->user()->poste_id))))
                            <a href="{{ route('demandes-fonds.edit', $demande->id) }}"
                               class="btn btn-icon btn-sm btn-text-secondary rounded-pill"
                               data-bs-toggle="tooltip" title="Modifier">
                                <i class="icon-base ti tabler-edit icon-22px"></i>
                            </a>
                            @endif

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
<script>
$(document).ready(function() {
    $('#demandes-table').DataTable({
        dom: '<"row align-items-center mb-3"<"col-md-6"l><"col-md-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        language: window.DGTCP_DATATABLES_FR,
        responsive: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, 'Tout']],
        order: [[4, 'desc']],
        columnDefs: [
            { targets: [3], className: 'text-end' },
            { targets: [6], orderable: false, searchable: false, className: 'text-center' }
        ]
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush
