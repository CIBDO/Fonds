@extends('layouts.master')

@section('content')
<!-- Section principale DGTCP -->
<div class="dgtcp-section">
    <!-- Formulaire de recherche DGTCP -->
    <div class="dgtcp-card mb-4">
        <div class="dgtcp-card-header">
            <div class="dgtcp-card-title">
                <i class="fas fa-search"></i>
                <span>Recherche et Filtres</span>
            </div>
        </div>
        <div class="dgtcp-card-body">
            <form action="{{ route('demandes-fonds.fonctionnaires') }}" method="GET" class="dgtcp-filter-form">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="dgtcp-form-label">
                            <i class="fas fa-building"></i>
                            Poste
                        </label>
                        <input type="text" name="poste" class="dgtcp-form-control"
                               placeholder="Rechercher par poste..." value="{{ request('poste') }}">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="dgtcp-form-label">
                            <i class="fas fa-calendar"></i>
                            Mois
                        </label>
                        <select name="mois" class="dgtcp-form-control">
                            <option value="">Sélectionner un mois</option>
                            @foreach(['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'] as $mois)
                                <option value="{{ $mois }}" {{ request('mois') == $mois ? 'selected' : '' }}>{{ $mois }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="dgtcp-form-label">
                            <i class="fas fa-calendar-alt"></i>
                            Date de début
                        </label>
                        <input type="date" name="date_debut" class="dgtcp-form-control"
                               value="{{ request('date_debut') }}">
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="dgtcp-form-label">
                            <i class="fas fa-calendar-check"></i>
                            Date de fin
                        </label>
                        <input type="date" name="date_fin" class="dgtcp-form-control"
                               value="{{ request('date_fin') }}">
                    </div>
                    <div class="col-12 d-flex justify-content-center mt-3">
                        <button type="submit" class="dgtcp-btn primary">
                            <i class="fas fa-search"></i>
                            <span>Rechercher</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes statistiques DGTCP -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card primary">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">BCS</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalBCS, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-arrow-up"></i>
                    <span>Fonctionnaires BCS</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card success">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">Santé</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalSante, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-heartbeat"></i>
                    <span>Collectivité Santé</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card info">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">Éducation</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalEducation, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Collectivité Éducation</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card warning">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">Saisonnier</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalSaisonnier, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-clock"></i>
                    <span>Personnels Saisonniers</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de cartes -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card danger">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">EPN</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalEPN, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-wifi"></i>
                    <span>Personnels EPN</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card secondary">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">CED</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalCED, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-city"></i>
                    <span>Personnels CED</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card dark">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">ECOM</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalECOM, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-store"></i>
                    <span>Personnels ECOM</span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="dgtcp-stats-card light">
                <div class="dgtcp-stats-content">
                    <div class="dgtcp-stats-info">
                        <div class="dgtcp-stats-title">CFP-CPAM</div>
                        <div class="dgtcp-stats-value">{{ number_format($totalCFPCPAM, 0, ',', ' ') }}</div>
                        <div class="dgtcp-stats-unit">F CFA</div>
                    </div>
                    <div class="dgtcp-stats-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
                <div class="dgtcp-stats-footer">
                    <i class="fas fa-briefcase"></i>
                    <span>Personnels CFP-CPAM</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des fonctionnaires DGTCP -->
    <div class="dgtcp-card">
        <div class="dgtcp-card-header">
            <div class="dgtcp-card-title">
                <i class="fas fa-table"></i>
                <span>Demandes de Fonds - Fonctionnaires</span>
            </div>
            <div class="dgtcp-card-actions">
                <div class="dgtcp-stats-mini">
                    <span class="dgtcp-stats-value">{{ $demandeFonds->count() }}</span>
                    <span class="dgtcp-stats-label">Demandes</span>
                </div>
            </div>
        </div>
        <div class="dgtcp-card-body">
            <div class="dgtcp-table-container">
                <table id="demandes-table" class="dgtcp-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-building"></i> Poste</th>
                            <th><i class="fas fa-users"></i> BCS</th>
                            <th><i class="fas fa-hospital"></i> Santé</th>
                            <th><i class="fas fa-school"></i> Éducation</th>
                            <th><i class="fas fa-calendar-alt"></i> Saisonnier</th>
                            <th><i class="fas fa-network-wired"></i> EPN</th>
                            <th><i class="fas fa-building"></i> CED</th>
                            <th><i class="fas fa-shopping-cart"></i> ECOM</th>
                            <th><i class="fas fa-user-tie"></i> CFP-CPAM</th>
                            <th><i class="fas fa-calendar"></i> Mois</th>
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
                                    <span class="dgtcp-amount primary">{{ number_format($demande->fonctionnaires_bcs_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount success">{{ number_format($demande->collectivite_sante_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount info">{{ number_format($demande->collectivite_education_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount warning">{{ number_format($demande->personnels_saisonniers_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount danger">{{ number_format($demande->epn_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount secondary">{{ number_format($demande->ced_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount dark">{{ number_format($demande->ecom_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-amount light">{{ number_format($demande->cfp_cpam_total_courant, 0, ',', ' ') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="dgtcp-cell-content">
                                    <span class="dgtcp-cell-primary">{{ $demande->mois . ' ' . $demande->annee }}</span>
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
/* Styles DGTCP pour la vue fonctionnaires */
.dgtcp-section {
    padding: 2rem 0;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
}

.dgtcp-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    overflow: hidden;
    transition: all 0.3s ease;
}

.dgtcp-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
}

.dgtcp-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.dgtcp-card-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
}

.dgtcp-card-title i {
    color: #22c55e;
    font-size: 1.5rem;
}

.dgtcp-card-body {
    padding: 2rem;
}

.dgtcp-card-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Formulaire de recherche */
.dgtcp-filter-form {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.dgtcp-form-label {
    color: #374151;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.dgtcp-form-label i {
    color: #22c55e;
    font-size: 0.875rem;
}

.dgtcp-form-control {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    color: #1f2937;
    background: white;
    transition: all 0.3s ease;
    width: 100%;
}

.dgtcp-form-control:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.dgtcp-btn.primary {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.dgtcp-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(34, 197, 94, 0.3);
}

/* Cartes statistiques */
.dgtcp-stats-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    height: 100%;
}

.dgtcp-stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #22c55e 0%, #f59e0b 100%);
}

.dgtcp-stats-card.primary::before {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.dgtcp-stats-card.success::before {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.dgtcp-stats-card.info::before {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
}

.dgtcp-stats-card.warning::before {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.dgtcp-stats-card.danger::before {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.dgtcp-stats-card.secondary::before {
    background: linear-gradient(135deg, #64748b 0%, #475569 100%);
}

.dgtcp-stats-card.dark::before {
    background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
}

.dgtcp-stats-card.light::before {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.dgtcp-stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.dgtcp-stats-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.dgtcp-stats-info {
    flex: 1;
}

.dgtcp-stats-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.dgtcp-stats-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.dgtcp-stats-unit {
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

.dgtcp-stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(245, 158, 11, 0.1) 100%);
    color: #22c55e;
    font-size: 1.5rem;
}

.dgtcp-stats-card.primary .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(29, 78, 216, 0.1) 100%);
    color: #3b82f6;
}

.dgtcp-stats-card.success .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%);
    color: #22c55e;
}

.dgtcp-stats-card.info .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(8, 145, 178, 0.1) 100%);
    color: #06b6d4;
}

.dgtcp-stats-card.warning .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(217, 119, 6, 0.1) 100%);
    color: #f59e0b;
}

.dgtcp-stats-card.danger .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
    color: #ef4444;
}

.dgtcp-stats-card.secondary .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(100, 116, 139, 0.1) 0%, rgba(71, 85, 105, 0.1) 100%);
    color: #64748b;
}

.dgtcp-stats-card.dark .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(55, 65, 81, 0.1) 0%, rgba(31, 41, 55, 0.1) 100%);
    color: #374151;
}

.dgtcp-stats-card.light .dgtcp-stats-icon {
    background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(124, 58, 237, 0.1) 100%);
    color: #8b5cf6;
}

.dgtcp-stats-footer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #64748b;
    font-weight: 500;
}

.dgtcp-stats-footer i {
    color: #22c55e;
}

/* Styles tableau */
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

.dgtcp-amount {
    font-weight: 700;
    font-size: 0.95rem;
}

.dgtcp-amount.primary {
    color: #3b82f6 !important;
}

.dgtcp-amount.success {
    color: #22c55e !important;
}

.dgtcp-amount.info {
    color: #06b6d4 !important;
}

.dgtcp-amount.warning {
    color: #f59e0b !important;
}

.dgtcp-amount.danger {
    color: #ef4444 !important;
}

.dgtcp-amount.secondary {
    color: #64748b !important;
}

.dgtcp-amount.dark {
    color: #374151 !important;
}

.dgtcp-amount.light {
    color: #8b5cf6 !important;
}

.dgtcp-stats-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.25rem;
}

.dgtcp-stats-mini .dgtcp-stats-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #22c55e;
}

.dgtcp-stats-mini .dgtcp-stats-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
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
    .dgtcp-section {
        padding: 1rem 0;
    }

    .dgtcp-card-body {
        padding: 1rem;
    }

    .dgtcp-stats-card {
        margin-bottom: 1rem;
    }

    .dgtcp-table thead th {
        padding: 12px 8px;
        font-size: 0.75rem;
    }

    .dgtcp-table tbody td {
        padding: 10px 8px;
        font-size: 0.8rem;
    }
}
</style>

<script>
$(document).ready(function() {
    // Configuration DataTable DGTCP pour les fonctionnaires
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
                title: 'Demandes de Fonds Fonctionnaires DGTCP',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'dt-button',
                title: 'Demandes de Fonds Fonctionnaires DGTCP',
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
                title: 'Demandes de Fonds Fonctionnaires DGTCP',
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
        order: [[9, 'desc']], // Trier par mois décroissant
        columnDefs: [
            {
                targets: [1, 2, 3, 4, 5, 6, 7, 8], // Colonnes de montants
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

    // Animation des cartes statistiques au chargement
    $('.dgtcp-stats-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(30px)'
        }).delay(index * 100).animate({
            'opacity': '1'
        }, 500).css('transform', 'translateY(0px)');
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