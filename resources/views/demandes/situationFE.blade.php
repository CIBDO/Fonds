@extends('layouts.master')

@section('content')

<!-- Tableau des demandes DGTCP -->
<div class="dgtcp-section">
    <div class="dgtcp-card">
        <div class="dgtcp-card-header">
            <div class="dgtcp-card-title">
                <i class="fas fa-chart-line"></i>
                <span>Situation des Fonds Envoyés</span>
            </div>
            <div class="dgtcp-card-actions">
                <div class="dgtcp-stats-mini">
                    <span class="dgtcp-stats-value">{{ $demandeFonds->count() }}</span>
                    <span class="dgtcp-stats-label">Envois</span>
        </div>
    </div>
</div>
        <div class="dgtcp-card-body">
<!-- Formulaire de recherche -->
            <form action="{{ route('demandes-fonds.situationFE') }}" method="GET" class="dgtcp-filter-form mb-4">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                            <input type="text" name="poste" class="dgtcp-form-control" placeholder="Rechercher par poste..." value="{{ request('poste') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                            <select name="mois" class="dgtcp-form-control">
                        <option value="">Sélectionner un mois</option>
                        @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                            <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="form-group">
                            <input type="number" name="annee" class="dgtcp-form-control" placeholder="Année..." value="{{ request('annee', date('Y')) }}" min="2000" max="{{ date('Y') }}">
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                        <button type="submit" class="dgtcp-btn primary w-100">
                            <i class="fas fa-search"></i>
                            <span>Rechercher</span>
                        </button>
        </div>
    </div>
</form>

<!-- Section des totaux globaux -->
            <div class="dgtcp-stats-section mb-4">
        <div class="row">
                    <div class="col-md-4">
                        <div class="dgtcp-stats-card primary">
                            <div class="dgtcp-stats-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="dgtcp-stats-content">
                                <div class="dgtcp-stats-label">Montant Total Demandé</div>
                                <div class="dgtcp-stats-value">{{ number_format($totalDemande, 0, ',', ' ') }}</div>
                                <div class="dgtcp-stats-currency">F CFA</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dgtcp-stats-card success">
                            <div class="dgtcp-stats-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="dgtcp-stats-content">
                                <div class="dgtcp-stats-label">Recettes Douanières</div>
                                <div class="dgtcp-stats-value">{{ number_format($totalRecettes, 0, ',', ' ') }}</div>
                                <div class="dgtcp-stats-currency">F CFA</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="dgtcp-stats-card warning">
                            <div class="dgtcp-stats-icon">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <div class="dgtcp-stats-content">
                                <div class="dgtcp-stats-label">Montant Envoyé</div>
                                <div class="dgtcp-stats-value">{{ number_format($totalSolde, 0, ',', ' ') }}</div>
                                <div class="dgtcp-stats-currency">F CFA</div>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="dgtcp-table-container">
                <table id="demandes-table" class="dgtcp-table">
                <thead>
                    <tr>
                            <th><i class="fas fa-map-marker-alt"></i> Postes</th>
                            <th><i class="fas fa-money-bill-wave"></i> Montant Demandé</th>
                            <th><i class="fas fa-coins"></i> Recettes Douanières</th>
                            <th><i class="fas fa-paper-plane"></i> Montant Envoyé</th>
                            <th><i class="fas fa-calendar-alt"></i> Mois</th>
                            <th><i class="fas fa-clock"></i> Date d'Envoi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandeFonds as $demande)
                    <tr>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->poste->nom }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount">{{ number_format($demande->total_courant, 0, ',', ' ') }}</span>
                                    {{-- <small class="dgtcp-currency">F CFA</small> --}}
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount success">{{ number_format($demande->montant_disponible, 0, ',', ' ') }}</span>
                                    {{-- <small class="dgtcp-currency">F CFA</small> --}}
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount warning">{{ number_format($demande->montant, 0, ',', ' ') }}</span>
                                    {{-- <small class="dgtcp-currency">F CFA</small> --}}
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->mois . ' ' . $demande->annee }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ date('d/m/Y', strtotime($demande->date_envois)) }}</span>
                                </div>
                            </td>
                    </tr>
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

.dgtcp-amount.success {
    color: #16a34a !important;
}

.dgtcp-amount.warning {
    color: #f59e0b !important;
}

.dgtcp-amount.info {
    color: #3b82f6 !important;
}

.dgtcp-currency {
    color: #64748b !important;
    font-size: 0.7rem;
    font-weight: 500;
}

/* Stats Cards */
.dgtcp-stats-section {
    margin-bottom: 2rem;
}

.dgtcp-stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    border-left: 4px solid;
    margin-bottom: 1rem;
}

.dgtcp-stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.dgtcp-stats-card.primary {
    border-left-color: #3b82f6;
}

.dgtcp-stats-card.success {
    border-left-color: #22c55e;
}

.dgtcp-stats-card.warning {
    border-left-color: #f59e0b;
}

.dgtcp-stats-card.info {
    border-left-color: #06b6d4;
}

.dgtcp-stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.dgtcp-stats-card.primary .dgtcp-stats-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.dgtcp-stats-card.success .dgtcp-stats-icon {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.dgtcp-stats-card.warning .dgtcp-stats-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.dgtcp-stats-card.info .dgtcp-stats-icon {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.dgtcp-stats-content {
    flex: 1;
}

.dgtcp-stats-label {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.dgtcp-stats-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.dgtcp-stats-currency {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
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

.dgtcp-stats-mini {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

.dgtcp-stats-mini .dgtcp-stats-value {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #22c55e !important;
}

.dgtcp-stats-mini .dgtcp-stats-label {
    font-size: 0.75rem !important;
    color: #6b7280 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
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

    .dgtcp-stats-card {
        padding: 1rem;
        flex-direction: column;
        text-align: center;
    }

    .dgtcp-stats-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
}
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

.dgtcp-amount.success {
    color: #16a34a !important;
}

.dgtcp-amount.warning {
    color: #f59e0b !important;
}

.dgtcp-amount.info {
    color: #3b82f6 !important;
}

.dgtcp-currency {
    color: #64748b !important;
    font-size: 0.7rem;
    font-weight: 500;
}

/* Stats Cards */
.dgtcp-stats-section {
    margin-bottom: 2rem;
}

.dgtcp-stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
    border-left: 4px solid;
    margin-bottom: 1rem;
}

.dgtcp-stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.dgtcp-stats-card.primary {
    border-left-color: #3b82f6;
}

.dgtcp-stats-card.success {
    border-left-color: #22c55e;
}

.dgtcp-stats-card.warning {
    border-left-color: #f59e0b;
}

.dgtcp-stats-card.info {
    border-left-color: #06b6d4;
}

.dgtcp-stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.dgtcp-stats-card.primary .dgtcp-stats-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.dgtcp-stats-card.success .dgtcp-stats-icon {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.dgtcp-stats-card.warning .dgtcp-stats-icon {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.dgtcp-stats-card.info .dgtcp-stats-icon {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.dgtcp-stats-content {
    flex: 1;
}

.dgtcp-stats-label {
    font-size: 0.75rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.dgtcp-stats-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1.2;
}

.dgtcp-stats-currency {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
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

.dgtcp-stats-mini {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

.dgtcp-stats-mini .dgtcp-stats-value {
    font-size: 1.5rem !important;
    font-weight: 700 !important;
    color: #22c55e !important;
}

.dgtcp-stats-mini .dgtcp-stats-label {
    font-size: 0.75rem !important;
    color: #6b7280 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
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

    .dgtcp-stats-card {
        padding: 1rem;
        flex-direction: column;
        text-align: center;
    }

    .dgtcp-stats-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
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
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'dt-button',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'dt-button',
                title: 'Situation Fonds Envoyés DGTCP',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'dt-button',
                title: 'Situation Fonds Envoyés DGTCP',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimer',
                className: 'dt-button',
                title: 'Situation Fonds Envoyés DGTCP',
                exportOptions: {
                    columns: ':visible'
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
        order: [[5, 'desc']], // Trier par date d'envoi décroissante
        columnDefs: [
            {
                targets: [1, 2, 3], // Colonnes montants
                type: 'num-fmt',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'type') {
                        return data;
                    }
                    return data.replace(/[^\d]/g, '');
                }
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
    });
</script>
@stop
@endsection
