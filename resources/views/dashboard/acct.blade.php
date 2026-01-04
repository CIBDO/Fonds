@extends('layouts.master')
@section('content')

<div class="content container-fluid">
    <!-- Alertes importantes pour ACCT -->
    <div class="dgtcp-alert warning fade-in">
        <i class="fas fa-user-check"></i>
        <div>
            <strong>Agence Comptable Centrale du Trésor(ACCT) :</strong>
            Validez les demandes de fonds et supervisez les opérations comptables du trésor public.
        </div>
    </div>

    <!-- Cartes de statistiques spécifiques ACCT -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.index') }}" class="dgtcp-stat-card funds-requested fade-in">
                <div class="dgtcp-stat-label">📊 Demandes Totales</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsDemandes, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Montant total des demandes reçues</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.recettes') }}" class="dgtcp-stat-card customs-receipts fade-in">
                <div class="dgtcp-stat-label" style="color: var(--dgtcp-gold);">🏛️ Recettes Contrôlées</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsRecettes, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Recettes douanières validées ACCT</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.solde') }}" class="dgtcp-stat-card funds-to-send fade-in">
                <div class="dgtcp-stat-label" style="color: var(--dgtcp-red);">⚖️ À Valider</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsEnCours, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Demandes en attente de validation</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.situation') }}" class="dgtcp-stat-card funds-sent fade-in">
                <div class="dgtcp-stat-label">✅ Validées</div>
                <div class="dgtcp-stat-value">{{ number_format($paiementsEffectues, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Opérations validées et traitées</div>
            </a>
        </div>
    </div>

    <!-- Section contrôle comptable -->
    <div class="dgtcp-section slide-in">
        <div class="dgtcp-section-header">
            <i class="fas fa-calculator"></i>
            Contrôle Comptable - Vue d'Ensemble des Postes
        </div>

        <!-- Contrôles ACCT -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="dgtcp-form-group">
                    <label class="dgtcp-form-label">
                        <i class="fas fa-search me-2"></i>Recherche globale
                    </label>
                    <input type="text" id="filterInput" class="dgtcp-form-control"
                           placeholder="Filtrer par poste, montant...">
                </div>
            </div>
            <div class="col-md-4">
                <div class="dgtcp-form-group">
                    <label class="dgtcp-form-label">
                        <i class="fas fa-check-double me-2"></i> Validation rapide
                    </label>
                    <a href="{{ route('demandes-fonds.envois') }}" class="dgtcp-btn success w-100">
                        <i class="fas fa-check-circle"></i>
                        Valider les Demandes
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="dgtcp-form-group">
                    <label class="dgtcp-form-label">
                        <i class="fas fa-file-export me-2"></i>Export comptable
                    </label>
                    <button class="dgtcp-btn secondary w-100" onclick="exportAccountingData()">
                        <i class="fas fa-file-excel"></i>
                        Export ACCT
                    </button>
                </div>
            </div>
        </div>

        <!-- Tableau de contrôle ACCT -->
        <div class="dgtcp-table-container">
            <table class="dgtcp-table" id="financialTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-building me-2"></i>Poste </th>
                        <th><i class="fas fa-coins me-2"></i>Total Net</th>
                        <th><i class="fas fa-undo me-2"></i>Total Revers</th>
                        <th><i class="fas fa-clock me-2"></i>Total Courant</th>
                        <th><i class="fas fa-history me-2"></i>Total Ancien</th>
                        <th><i class="fas fa-calculator me-2"></i>Écart Comptable</th>
                        <th><i class="fas fa-traffic-light me-2"></i>Statut</th>
                        {{-- <th><i class="fas fa-cog me-2"></i>Actions ACCT</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandesFonds as $demande)
                        @php
                            $ecart = $demande->total_courant - $demande->total_ancien;
                            $statut = abs($ecart) < 1000 ? 'conforme' : 'attention';
                        @endphp
                        <tr class="{{ $statut === 'attention' ? 'table-warning' : '' }}">
                            <td>
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                <strong>{{ $demande->poste->nom }}</strong>
                            </td>
                            <td>
                                <span class="dgtcp-badge">
                                    {{ number_format($demande->total_net, 0, '', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="dgtcp-badge">
                                    {{ number_format($demande->total_revers, 0, '', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="dgtcp-badge">
                                    {{ number_format($demande->total_courant, 0, '', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="dgtcp-badge">
                                    {{ number_format($demande->total_ancien, 0, '', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                <span class="dgtcp-badge">
                                    {{ number_format($ecart, 0, '', ' ') }} FCFA
                                </span>
                            </td>
                            <td>
                                @if($statut === 'conforme')
                                    <span class="dgtcp-badge success">
                                        <i class="fas fa-check-circle me-1"></i>Conforme
                                    </span>
                                @else
                                    <span class="dgtcp-badge warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Attention
                                    </span>
                                @endif
                            </td>
                            {{-- <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('demandes-fonds.show', $demande->id) }}"
                                       class="dgtcp-btn outline sm" title="Examiner">
                                        <i class="fas fa-search"></i>
                                    </a>
                                    <button class="dgtcp-btn success sm"
                                            onclick="validateRequest({{ $demande->id }})"
                                            title="Valider">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="dgtcp-btn danger sm"
                                            onclick="rejectRequest({{ $demande->id }})"
                                            title="Rejeter">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination DGTCP -->
        <div class="dgtcp-pagination-container">
            <div class="pagination-info">
                <i class="fas fa-info-circle me-2"></i>
                Affichage de
                <span class="badge">{{ $demandesFonds->firstItem() ?? 0 }}</span>
                à
                <span class="badge">{{ $demandesFonds->lastItem() ?? 0 }}</span>
                sur
                <span class="badge">{{ $demandesFonds->total() }}</span>
                résultats
            </div>
            {{ $demandesFonds->links('custom.pagination') }}
        </div>
    </div>

    <!-- Tableau de bord de contrôle ACCT -->
   {{--  <div class="row mt-4">
        <div class="col-md-6">
            <div class="dgtcp-card">
                <div class="dgtcp-card-header">
                    <i class="fas fa-chart-pie me-2"></i>
                    Répartition par Statut
                </div>
                <div class="dgtcp-card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="metric-acct">
                                <div class="metric-value text-success">{{ $demandesFonds->where('status', 'approuve')->count() }}</div>
                                <div class="metric-label">Approuvées</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-acct">
                                <div class="metric-value text-warning">{{ $demandesFonds->where('status', 'en_attente')->count() }}</div>
                                <div class="metric-label">En Attente</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-acct">
                                <div class="metric-value text-danger">{{ $demandesFonds->where('status', 'rejete')->count() }}</div>
                                <div class="metric-label">Rejetées</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dgtcp-card">
                <div class="dgtcp-card-header">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alertes Comptables
                </div>
                <div class="dgtcp-card-body">
                    <div class="alert-item">
                        <i class="fas fa-calculator text-success"></i>
                        <span>{{ $demandesFonds->filter(function($d) { return abs($d->total_courant - $d->total_ancien) >= 1000; })->count() }} écarts significatifs détectés</span>
                    </div>
                    <div class="alert-item">
                        <i class="fas fa-clock text-success"></i>
                        <span>{{ $demandesFonds->whereNull('status')->count() }} demandes nécessitent une validation</span>
                    </div>
                    <div class="alert-item">
                        <i class="fas fa-file-alt text-success"></i>
                        <span>Conformité globale : {{ $demandesFonds->count() > 0 ? round(($demandesFonds->where('status', 'approuve')->count() / $demandesFonds->count()) * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Actions rapides ACCT -->
    {{-- <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn primary w-100" onclick="generateAcctReport()">
                <i class="fas fa-file-pdf"></i>
                Rapport ACCT
            </button>
        </div>
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn secondary w-100" onclick="exportAccounting()">
                <i class="fas fa-file-excel"></i>
                Export Comptable
            </button>
        </div>
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn accent w-100" onclick="auditTrail()">
                <i class="fas fa-search"></i>
                Piste d'Audit
            </button>
        </div>
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn outline w-100" onclick="acctSettings()">
                <i class="fas fa-cog"></i>
                Paramètres ACCT
            </button>
        </div>
    </div> --}}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation séquentielle des cartes
    const cards = document.querySelectorAll('.dgtcp-stat-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.2}s`;
    });

    // Filtre de tableau amélioré
    document.getElementById('filterInput').addEventListener('keyup', function() {
        const filter = this.value.toUpperCase();
        const rows = document.querySelector("#financialTable tbody").rows;

        for (let i = 0; i < rows.length; i++) {
            const cells = rows[i].cells;
            let match = false;
            for (let j = 0; j < cells.length; j++) {
                if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }

            if (match) {
                rows[i].style.display = "";
                rows[i].style.animation = 'fadeInUp 0.3s ease';
            } else {
                rows[i].style.display = "none";
            }
        }
    });

    // Effet de survol sur les lignes du tableau
    const tableRows = document.querySelectorAll('.dgtcp-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(90deg, rgba(245, 158, 11, 0.05) 0%, white 100%)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.background = '';
        });
    });
});

// Fonctions spécifiques ACCT
function validateRequest(id) {
    if(confirm('Confirmer la validation de cette demande ?')) {
        // Ici vous pouvez ajouter l'appel AJAX pour valider
        alert('✅ Demande validée avec succès');
    }
}

function rejectRequest(id) {
    if(confirm('Confirmer le rejet de cette demande ?')) {
        // Ici vous pouvez ajouter l'appel AJAX pour rejeter
        alert('❌ Demande rejetée');
    }
}

function validatePendingRequests() {
    alert('🔍 Validation des demandes en attente...');
}

function exportAccountingData() {
    alert('📊 Export des données comptables en cours...');
}

function generateAcctReport() {
    alert('📄 Génération du rapport ACCT...');
}

function exportAccounting() {
    alert('📊 Export comptable Excel...');
}

function auditTrail() {
    alert('🔍 Ouverture de la piste d\'audit...');
}

function acctSettings() {
    alert('⚙️ Paramètres ACCT...');
}
</script>

<style>
/* Styles spécifiques pour le dashboard ACCT */
.fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.slide-in {
    animation: slideInRight 0.8s ease-out;
}

.metric-acct {
    padding: 15px;
}

.metric-acct .metric-value {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.metric-acct .metric-label {
    font-size: 0.8rem;
    color: #6B7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.alert-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.alert-item:last-child {
    border-bottom: none;
}

.alert-item i {
    margin-right: 12px;
    width: 20px;
}

.table-warning {
    background-color: rgba(245, 158, 11, 0.1) !important;
}

/* Boutons spécialisés ACCT */
.dgtcp-btn.sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

/* Réduction du vert - couleurs plus subtiles et neutres */
.dgtcp-section-header {
    background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%) !important;
    color: white !important;
}

.dgtcp-btn.success {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    opacity: 0.85;
}

.dgtcp-btn.success:hover {
    opacity: 1;
}

.dgtcp-table thead {
    background: linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%) !important;
}

.dgtcp-table thead th {
    color: #f5f5f6 !important;
    font-weight: 600;
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .dgtcp-stat-card {
        margin-bottom: 1rem;
    }

    .btn-group {
        flex-direction: column;
    }

    .btn-group .dgtcp-btn {
        margin-bottom: 3px;
    }

    .metric-acct .metric-value {
        font-size: 1.5rem;
    }
}
</style>
@endsection
