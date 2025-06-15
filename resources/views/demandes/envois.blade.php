@extends('layouts.master')
@section('content')
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">Demandes de fonds</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Demandes de fonds</li>
            </ul>
        </div>
    </div>
</div>

<!-- Tableau des demandes DGTCP -->
<div class="dgtcp-section">
    <div class="dgtcp-card">
        <div class="dgtcp-card-header">
            <div class="dgtcp-card-title">
                <i class="fas fa-table"></i>
                <span>Liste des Demandes de Fonds</span>
            </div>
            <div class="dgtcp-card-actions">
                <div class="dgtcp-stats-mini">
                    <span class="dgtcp-stats-value">{{ $demandeFonds->count() }}</span>
                    <span class="dgtcp-stats-label">Demandes</span>
                </div>
            </div>
        </div>
        <div class="dgtcp-card-body">
            {{-- <form action="{{ route('demandes-fonds.envois') }}" method="GET" class="dgtcp-filter-form mb-4">
                <div class="row">
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <input type="text" name="poste" class="dgtcp-form-control" placeholder="Rechercher par poste ..." value="{{ request('poste') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <input type="text" name="mois" class="dgtcp-form-control" placeholder="Rechercher par mois ..." value="{{ request('mois') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <input type="date" name="date_debut" class="dgtcp-form-control" placeholder="Date de début" value="{{ request('date_debut') }}">
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <div class="form-group">
                            <input type="date" name="date_fin" class="dgtcp-form-control" placeholder="Date de fin" value="{{ request('date_fin') }}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="dgtcp-btn primary">
                            <i class="fas fa-search"></i>
                            <span>Rechercher</span>
                        </button>
                    </div>
                </div>
            </form>
 --}}
            <div class="dgtcp-table-container">
                <table id="demandes-table" class="dgtcp-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i> Mois</th>
                            <th><i class="fas fa-clock"></i> Date Réception</th>
                            <th><i class="fas fa-map-marker-alt"></i> Poste</th>
                            <th><i class="fas fa-money-bill-wave"></i> Fonds Demandés</th>
                            <th><i class="fas fa-calendar-plus"></i> Date Création</th>
                            <th><i class="fas fa-info-circle"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandeFonds as $demande)
                        <tr>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->mois }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ date('d/m/Y', strtotime($demande->date_reception)) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->poste->nom }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount">{{ number_format($demande->total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ date('d/m/Y', strtotime($demande->created_at)) }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-status-container">
                                    @if($demande->status === 'approuve')
                                        <span class="dgtcp-status success">
                                            <i class="fas fa-check-circle"></i>
                                            <span>Approuvé</span>
                                        </span>
                                    @elseif($demande->status === 'rejete')
                                        <span class="dgtcp-status danger">
                                            <i class="fas fa-times-circle"></i>
                                            <span>Rejeté</span>
                                        </span>
                                    @else
                                        <span class="dgtcp-status warning">
                                            <i class="fas fa-clock"></i>
                                            <span>En Attente</span>
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-actions">
                                    <a href="{{ route('demandes-fonds.show', $demande->id) }}"
                                       class="dgtcp-action-btn success" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="dgtcp-action-btn primary"
                                            data-bs-toggle="modal" data-bs-target="#approveModal-{{ $demande->id }}"
                                            title="Valider">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="dgtcp-action-btn danger"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $demande->id }}"
                                            title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <a href="{{ route('demande-fonds.generate.pdf', $demande->id) }}"
                                       class="dgtcp-action-btn info" title="Générer PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal pour approuver la demande -->
                        <div class="modal fade" id="approveModal-{{ $demande->id }}" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Approuver la demande pour {{ $demande->poste->nom }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <input type="hidden" name="status" value="approuve">
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="date_envois">Date d'envoi :</label>
                                                <input type="text" name="date_envois" class="dgtcp-form-control" value="{{ now()->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="montant">Montant :</label>
                                                <input type="text" name="montant" class="dgtcp-form-control" oninput="formatNumberInput(this)" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="montant_disponible">Recette Douanière :</label>
                                                <input type="text" name="montant_disponible" class="dgtcp-form-control" value="{{ number_format($demande->montant_disponible, 0, ',', ' ') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="solde">Solde du mois :</label>
                                                <input type="text" name="solde" class="dgtcp-form-control" value="{{ number_format($demande->solde, 0, ',', ' ') }}" readonly>
                                            </div>
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="observation">Observation :</label>
                                                <textarea name="observation" class="dgtcp-form-control" rows="1"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="dgtcp-btn secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="dgtcp-btn primary">Envoyer</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal pour rejeter la demande -->
                        <div class="modal fade" id="rejectModal-{{ $demande->id }}" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Rejeter la demande pour {{ $demande->poste->nom }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('demandes-fonds.update-status', $demande->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <input type="hidden" name="status" value="rejete">
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="date_envois">Date d'envoi :</label>
                                                <input type="date" name="date_envois" class="dgtcp-form-control" value="{{ now()->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="dgtcp-form-label" for="observation">Raison du rejet :</label>
                                                <textarea name="observation" class="dgtcp-form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="dgtcp-btn secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="dgtcp-btn danger">Soumettre</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@section('add-js')
<!-- DataTables CSS moderne -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<style>
/* Styles DataTable DGTCP */
.dgtcp-table-container {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.dgtcp-table {
    width: 100% !important;
    border-collapse: separate;
    border-spacing: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #1e293b !important;
}

.dgtcp-table thead th {
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%) !important;
    color: white !important;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 16px 12px;
    text-align: left;
    border: none;
    position: relative;
    white-space: nowrap;
}

.dgtcp-table thead th i {
    margin-right: 6px;
    opacity: 0.9;
    color: white !important;
}

.dgtcp-table tbody td {
    padding: 14px 12px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
    font-size: 0.875rem;
    color: #1e293b !important;
    background: white !important;
}

.dgtcp-table tbody tr {
    transition: all 0.3s ease;
    background: white !important;
}

.dgtcp-table tbody tr:hover {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.05) 0%, rgba(245, 158, 11, 0.05) 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.dgtcp-cell-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.dgtcp-cell-primary {
    font-weight: 600;
    color: #1e293b !important;
}

.dgtcp-cell-secondary {
    color: #64748b !important;
    font-size: 0.75rem;
}

.dgtcp-amount {
    font-weight: 700;
    color: #22c55e !important;
    font-size: 0.95rem;
}

.dgtcp-currency {
    color: #64748b !important;
    font-size: 0.7rem;
    font-weight: 500;
}

.dgtcp-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.dgtcp-status.success {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #166534;
    border: 1px solid #22c55e;
}

.dgtcp-status.warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.dgtcp-status.danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
    border: 1px solid #dc2626;
}

.dgtcp-actions {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.dgtcp-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 0.875rem;
    position: relative;
    overflow: hidden;
    border: none;
    cursor: pointer;
}

.dgtcp-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.dgtcp-action-btn:hover::before {
    left: 100%;
}

.dgtcp-action-btn.success {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.dgtcp-action-btn.success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.4);
}

.dgtcp-action-btn.warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.dgtcp-action-btn.warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(245, 158, 11, 0.4);
}

.dgtcp-action-btn.info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.dgtcp-action-btn.info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
}

.dgtcp-action-btn.primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.dgtcp-action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
}

.dgtcp-action-btn.danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
}

.dgtcp-action-btn.danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(220, 38, 38, 0.4);
}

/* Personnalisation DataTables */
.dataTables_wrapper {
    padding: 0;
    color: #1e293b !important;
}

.dataTables_length,
.dataTables_filter,
.dataTables_info,
.dataTables_paginate {
    margin: 16px 0;
    color: #1e293b !important;
}

.dataTables_length label,
.dataTables_filter label,
.dataTables_info {
    color: #1e293b !important;
    font-weight: 500;
}

.dataTables_length select,
.dataTables_filter input {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    color: #1e293b !important;
    background: white !important;
}

.dataTables_length select:focus,
.dataTables_filter input:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.dataTables_paginate .paginate_button {
    padding: 8px 16px !important;
    margin: 0 2px !important;
    border-radius: 8px !important;
    border: 2px solid #e2e8f0 !important;
    background: white !important;
    color: #64748b !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
}

.dataTables_paginate .paginate_button:hover {
    background: #22c55e !important;
    color: white !important;
    border-color: #22c55e !important;
    transform: translateY(-1px);
}

.dataTables_paginate .paginate_button.current {
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%) !important;
    color: white !important;
    border-color: #22c55e !important;
}

.dt-buttons {
    margin-bottom: 16px;
}

.dt-button {
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%) !important;
    color: white !important;
    border: none !important;
    padding: 10px 20px !important;
    border-radius: 8px !important;
    margin-right: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
}

.dt-button:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .dgtcp-table thead th {
        padding: 12px 8px;
        font-size: 0.75rem;
    }

    .dgtcp-table tbody td {
        padding: 10px 8px;
        font-size: 0.8rem;
    }

    .dgtcp-actions {
        flex-direction: column;
        gap: 4px;
    }

    .dgtcp-action-btn {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
    }
}

/* Effet ripple */
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    pointer-events: none;
}

.ripple.animate {
    animation: ripple-animation 0.6s linear;
}

@keyframes ripple-animation {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

/* Corrections de visibilité */
.dgtcp-card {
    background: white !important;
    color: #1e293b !important;
}

.dgtcp-card-header {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
}

.dgtcp-card-title {
    color: #1e293b !important;
    font-weight: 600;
}

.dgtcp-card-title i {
    color: #22c55e !important;
}

.dgtcp-form-label {
    color: #374151 !important;
    font-weight: 500;
    margin-bottom: 0.5rem;
    display: block;
}

.dgtcp-form-label i {
    color: #22c55e !important;
    margin-right: 0.5rem;
}

.dgtcp-form-control {
    border: 2px solid #e5e7eb !important;
    border-radius: 8px !important;
    padding: 0.75rem 1rem !important;
    font-size: 0.875rem !important;
    color: #1f2937 !important;
    background: white !important;
    transition: all 0.3s ease !important;
}

.dgtcp-form-control:focus {
    outline: none !important;
    border-color: #22c55e !important;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1) !important;
}

.dgtcp-btn.primary {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
    color: white !important;
    border: none !important;
    padding: 0.75rem 1.5rem !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.dgtcp-btn.primary:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3) !important;
}

.dgtcp-btn.secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
    color: white !important;
    border: none !important;
    padding: 0.75rem 1.5rem !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.dgtcp-btn.secondary:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 16px rgba(107, 114, 128, 0.3) !important;
}

.dgtcp-btn.danger {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
    color: white !important;
    border: none !important;
    padding: 0.75rem 1.5rem !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.dgtcp-btn.danger:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 16px rgba(220, 38, 38, 0.3) !important;
}

.dgtcp-stats-mini {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

.dgtcp-stats-value {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #22c55e !important;
}

.dgtcp-stats-label {
    font-size: 0.75rem !important;
    color: #6b7280 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
}
</style>

<script>
$(document).ready(function() {
    // Configuration DataTable DGTCP
    const table = $('#demandes-table').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"B>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copier',
                className: 'dt-button',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'dt-button',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'dt-button',
                title: 'Demandes de Fonds DGTCP',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'dt-button',
                title: 'Demandes de Fonds DGTCP',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimer',
                className: 'dt-button',
                title: 'Demandes de Fonds DGTCP',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json",
            buttons: {
                copy: 'Copier',
                csv: 'CSV',
                excel: 'Excel',
                pdf: 'PDF',
                print: 'Imprimer'
            }
        },
        responsive: true,
        pageLength: 15,
        lengthMenu: [[10, 15, 25, 50, -1], [10, 15, 25, 50, "Tout"]],
        order: [[4, 'desc']], // Trier par date de demande décroissante
        columnDefs: [
            {
                targets: [3], // Colonne montant
                type: 'num-fmt',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'type') {
                        return data;
                    }
                    return data.replace(/[^\d]/g, '');
                }
            },
            {
                targets: [6], // Colonne actions
                orderable: false,
                searchable: false
            }
        ],
        initComplete: function() {
            // Animation d'entrée pour les lignes
            $('.dgtcp-table tbody tr').each(function(index) {
                $(this).css({
                    'opacity': '0',
                    'transform': 'translateY(20px)'
                }).delay(index * 50).animate({
                    'opacity': '1'
                }, 300).css('transform', 'translateY(0px)');
            });
        }
    });

    // Fonction pour réinitialiser les filtres
    window.resetFilters = function() {
        $('.dgtcp-filter-form')[0].reset();
        table.search('').columns().search('').draw();
    };

    // Animation des boutons d'action
    $(document).on('mouseenter', '.dgtcp-action-btn', function() {
        $(this).addClass('animate__animated animate__pulse');
    }).on('mouseleave', '.dgtcp-action-btn', function() {
        $(this).removeClass('animate__animated animate__pulse');
    });

    // Effet de ripple sur les boutons
    $(document).on('click', '.dgtcp-action-btn', function(e) {
        const button = $(this);
        const ripple = $('<span class="ripple"></span>');

        button.append(ripple);

        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.css({
            width: size,
            height: size,
            left: x,
            top: y
        }).addClass('animate');

        setTimeout(() => ripple.remove(), 600);
    });
});

// Fonction pour formater les nombres avec un séparateur de milliers (espace)
function formatNumberInput(input) {
    input.value = input.value.replace(/\D/g, '')
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
}

// Fonction pour nettoyer les champs avant soumission du formulaire
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function () {
            const inputs = form.querySelectorAll('input[name="montant"], input[name="solde"], input[name="montant_disponible"]');

            inputs.forEach(input => {
                if (input.value) {
                    input.value = input.value.replace(/\s+/g, '');
                }
            });
        });
    });
});
</script>
@stop

@endsection
