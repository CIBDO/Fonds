@extends('layouts.master')
@section('content')

<div class="content container-fluid">
    <!-- En-tête officiel DGTCP -->
    {{-- <div class="dgtcp-header fade-in">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                @include('partials.dgtcp-logo')
            </div>
            <div class="col-md-10">
                <h1>Direction Générale du Trésor et de la Comptabilité Publique</h1>
                <p>Tableau de Bord Administrateur - Système de Gestion des Fonds</p>
            </div>
        </div>
    </div> --}}

    <!-- Alertes importantes -->
    <div class="dgtcp-alert warning fade-in">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Information :</strong>
            Bienvenue sur le système de gestion des fonds DGTCP. Consultez les statistiques ci-dessous.
        </div>
    </div>

    <!-- Cartes de statistiques officielles DGTCP -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.index') }}" class="dgtcp-stat-card funds-requested fade-in">
                <div class="dgtcp-stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="dgtcp-stat-label">💰 Fonds Demandés</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsDemandes, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Total des demandes enregistrées</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.recettes') }}" class="dgtcp-stat-card customs-receipts fade-in">
                <div class="dgtcp-stat-icon" style="background: var(--secondary-gradient);">
                    <i class="fas fa-university"></i>
                </div>
                <div class="dgtcp-stat-label" style="color: var(--dgtcp-gold);">🏛️ Recettes Douanières</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsRecettes, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Montant disponible au trésor</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.solde') }}" class="dgtcp-stat-card funds-to-send fade-in">
                <div class="dgtcp-stat-icon" style="background: var(--accent-gradient);">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="dgtcp-stat-label" style="color: var(--dgtcp-red);">🚀 Fonds à Envoyer</div>
                <div class="dgtcp-stat-value">{{ number_format($fondsEnCours, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">En cours de traitement</div>
            </a>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-4">
            <a href="{{ route('demandes-fonds.situation') }}" class="dgtcp-stat-card funds-sent fade-in">
                <div class="dgtcp-stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="dgtcp-stat-label">✅ Fonds Envoyés</div>
                <div class="dgtcp-stat-value">{{ number_format($paiementsEffectues, 0, '', ' ') }}</div>
                <div class="dgtcp-stat-subtitle">Paiements effectués avec succès</div>
            </a>
        </div>
    </div>

    <!-- Section situation financière -->
    <div class="dgtcp-section slide-in">
        <div class="dgtcp-section-header">
            <i class="fas fa-chart-bar"></i>
            Situation Financière Détaillée - TRÉSOR PUBLIC
        </div>

        <!-- Contrôles de filtrage -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="dgtcp-form-group">
                    <label class="dgtcp-form-label">
                        <i class="fas fa-search me-2"></i>Recherche dans les données
                    </label>
                    <input type="text" id="filterInput" class="dgtcp-form-control"
                           placeholder="Filtrer par mois, poste, montant...">
                </div>
            </div>
            {{-- <div class="col-md-4 d-flex align-items-end">
                <button class="dgtcp-btn secondary w-100">
                    <i class="fas fa-download"></i>
                    Exporter les données
                </button>
            </div> --}}
        </div>

        <!-- Tableau officiel DGTCP -->
        <div class="dgtcp-table-container">
            <table class="dgtcp-table" id="financialTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-alt me-2"></i>Période</th>
                        <th><i class="fas fa-coins me-2"></i>Total Net</th>
                        <th><i class="fas fa-undo me-2"></i>Total Revers</th>
                        <th><i class="fas fa-clock me-2"></i>Total Courant</th>
                        <th><i class="fas fa-history me-2"></i>Total Ancien</th>
                        <th><i class="fas fa-map-marker-alt me-2"></i>Poste</th>
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
                                <i class="fas fa-building text-success me-2"></i>
                                <strong>{{ $demande->poste->nom }}</strong>
                            </td>
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

        <!-- Résumé statistique -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-success">Total Postes</h6>
                    <h4 class="text-success">{{ $demandesFonds->count() }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-primary">Montant Global</h6>
                    <h4 class="text-primary">{{ number_format($demandesFonds->sum('total_net'), 0, '', ' ') }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-warning">Moyenne/Poste</h6>
                    <h4 class="text-warning">{{ $demandesFonds->count() > 0 ? number_format($demandesFonds->sum('total_net') / $demandesFonds->count(), 0, '', ' ') : 0 }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center p-3 bg-light rounded">
                    <h6 class="text-info">Efficacité</h6>
                    <h4 class="text-info">98.5%</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    {{-- <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn primary w-100">
                <i class="fas fa-file-pdf"></i>
                Rapport PDF
            </button>
        </div>
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn secondary w-100">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </button>
        </div>
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn accent w-100">
                <i class="fas fa-chart-line"></i>
                Analytics
            </button>
        </div>
        <div class="col-md-3 mb-3">
            <button class="dgtcp-btn outline w-100">
                <i class="fas fa-cog"></i>
                Paramètres
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
            this.style.background = 'linear-gradient(90deg, rgba(34, 197, 94, 0.05) 0%, white 100%)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.background = '';
        });
    });

    // Messages de confirmation pour les boutons
    document.querySelectorAll('.dgtcp-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (this.textContent.includes('Rapport PDF')) {
                alert('📄 Génération du rapport PDF en cours...');
            } else if (this.textContent.includes('Export Excel')) {
                alert('📊 Export Excel en cours...');
            } else if (this.textContent.includes('Analytics')) {
                alert('📈 Ouverture des analytics...');
            } else if (this.textContent.includes('Paramètres')) {
                alert('⚙️ Accès aux paramètres...');
            } else if (this.textContent.includes('Exporter')) {
                alert('💾 Export des données en cours...');
            }
        });
    });
});
</script>

<style>
/* Styles spécifiques pour ce dashboard */
.fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.slide-in {
    animation: slideInRight 0.8s ease-out;
}

/* Animation pour les statistiques */
.dgtcp-stat-value {
    counter-reset: num var(--num);
    animation: countUp 2s ease-out;
}

@keyframes countUp {
    from {
        --num: 0;
    }
    to {
        --num: var(--target);
    }
}

/* Survol spécial pour les cartes de stats */
.dgtcp-stat-card:hover .dgtcp-stat-icon {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .dgtcp-header h1 {
        font-size: 1.5rem;
    }

    .dgtcp-header p {
        font-size: 0.9rem;
    }

    .dgtcp-stat-card {
        margin-bottom: 1rem;
    }
}
</style>
@endsection
