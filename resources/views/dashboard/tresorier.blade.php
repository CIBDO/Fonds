@extends('layouts.master')
@section('content')

<div class="content container-fluid">
    <!-- Alertes importantes pour Trésorier -->
    <div class="dgtcp-alert info fade-in">
        <i class="fas fa-user-tie"></i>
        <div>
            <strong>Trésorerie Régionale de {{ Auth::user()->poste->nom ?? 'Non défini' }} :</strong>
            Gérez les demandes de fonds et suivez les opérations de votre poste douanier.
        </div>
    </div>

    <!-- Cartes de statistiques spécifiques Trésorier -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.index') }}" class="dgtcp-stat-card funds-requested fade-in">
                <div class="dgtcp-stat-label">💰 Demandes Créées</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsDemandes, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Montant total demandé par votre poste</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.recettes') }}" class="dgtcp-stat-card customs-receipts fade-in">
                <div class="dgtcp-stat-label" style="color: var(--dgtcp-gold);">🏛️ Recettes Disponibles</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsRecettes, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Montant disponible pour votre poste</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.solde') }}" class="dgtcp-stat-card funds-to-send fade-in">
                <div class="dgtcp-stat-label" style="color: var(--dgtcp-red);">⏳ En Attente</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsEnCours, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Fonds en cours de traitement</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.situation') }}" class="dgtcp-stat-card funds-sent fade-in">
                <div class="dgtcp-stat-label">✅ Fonds Reçus</div>
                <div class="dgtcp-stat-value">{{ number_format($paiementsEffectues, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Paiements effectués avec succès</div>
            </a>
        </div>
    </div>

    <!-- Section situation financière du poste -->
    <div class="dgtcp-section slide-in">
        <div class="dgtcp-section-header">
            <i class="fas fa-building"></i>
            Situation Financière - Poste {{ Auth::user()->poste->nom ?? 'Non défini' }}
        </div>

        <!-- Contrôles de filtrage -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="dgtcp-form-group">
                    <label class="dgtcp-form-label">
                        <i class="fas fa-search me-2"></i>Recherche dans vos données
                    </label>
                    <input type="text" id="filterInput" class="dgtcp-form-control"
                           placeholder="Filtrer par mois, montant...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="dgtcp-form-group">
                    <label class="dgtcp-form-label">
                        <i class="fas fa-plus me-2"></i>Action rapide
                    </label>
                    <a href="{{ route('demandes-fonds.create') }}" class="dgtcp-btn primary w-100">
                        <i class="fas fa-plus"></i>
                        Nouvelle Demande de Fonds
                    </a>
                </div>
            </div>
        </div>

        <!-- Tableau spécifique trésorier -->
        <div class="dgtcp-table-container">
            <table class="dgtcp-table" id="financialTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-alt me-2"></i>Période</th>
                        <th><i class="fas fa-coins me-2"></i>Total Net</th>
                        <th><i class="fas fa-undo me-2"></i>Total Revers</th>
                        <th><i class="fas fa-clock me-2"></i>Total Courant</th>
                        <th><i class="fas fa-history me-2"></i>Total Ancien</th>
                        <th><i class="fas fa-calendar me-2"></i>Date Création</th>
                        {{-- <th><i class="fas fa-cog me-2"></i>Actions</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach($demandesFonds as $demande)
                        <tr>
                            <td>
                                <strong>{{ $demande->mois }}</strong>
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
                                <i class="fas fa-calendar text-success me-2"></i>
                                {{ $demande->created_at->format('d/m/Y') }}
                            </td>
                            {{-- <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('demandes-fonds.show', $demande->id) }}"
                                       class="dgtcp-btn outline sm" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($demande->status !== 'approuve')
                                        <a href="{{ route('demandes-fonds.edit', $demande->id) }}"
                                           class="dgtcp-btn secondary sm" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
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

    <!-- Section aide trésorier -->
    {{-- <div class="row mt-4">
        <div class="col-md-4">
            <div class="dgtcp-card">
                <div class="dgtcp-card-header">
                    <i class="fas fa-question-circle me-2"></i>
                    Aide Rapide
                </div>
                <div class="dgtcp-card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Créer une demande de fonds</li>
                        <li><i class="fas fa-check text-success me-2"></i>Suivre le statut des demandes</li>
                        <li><i class="fas fa-check text-success me-2"></i>Consulter l'historique</li>
                        <li><i class="fas fa-check text-success me-2"></i>Générer des rapports</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dgtcp-card">
                <div class="dgtcp-card-header">
                    <i class="fas fa-bell me-2"></i>
                    Notifications
                </div>
                <div class="dgtcp-card-body">
                    <div class="notification-item">
                        <i class="fas fa-info-circle text-success"></i>
                        <span>Nouvelle procédure disponible</span>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-exclamation-triangle text-success"></i>
                        <span>Échéance rapport mensuel</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dgtcp-card">
                <div class="dgtcp-card-header">
                    <i class="fas fa-chart-bar me-2"></i>
                    Performance
                </div>
                <div class="dgtcp-card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="metric-small">
                                <div class="metric-value">{{ $demandesFonds->count() }}</div>
                                <div class="metric-label">Demandes</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="metric-small">
                                <div class="metric-value">98%</div>
                                <div class="metric-label">Conformité</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            this.style.background = 'linear-gradient(90deg, rgba(34, 197, 94, 0.05) 0%, white 100%)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.background = '';
        });
    });
});
</script>

<style>
/* Styles spécifiques pour le dashboard trésorier */
.fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.slide-in {
    animation: slideInRight 0.8s ease-out;
}

.notification-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item i {
    margin-right: 10px;
    width: 16px;
}

.metric-small {
    padding: 10px;
}

.metric-small .metric-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dgtcp-green);
}

.metric-small .metric-label {
    font-size: 0.75rem;
    color: #6B7280;
    text-transform: uppercase;
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
        margin-bottom: 5px;
    }
}
</style>
@endsection
