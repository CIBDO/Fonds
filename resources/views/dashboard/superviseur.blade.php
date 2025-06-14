@extends('layouts.master')
@section('content')
<style>
    .supervisor-dashboard {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 20px 0;
    }

    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 20px;
        margin-bottom: 30px;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
        text-align: center;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .metric-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .metric-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }

    .metric-value {
        font-size: 36px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 10px;
    }

    .metric-label {
        color: #718096;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    .metric-trend {
        display: flex;
        align-items: center;
        font-size: 14px;
        font-weight: 600;
    }

    .trend-up {
        color: #48bb78;
    }

    .trend-down {
        color: #f56565;
    }

    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .chart-title {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .alert-panel {
        background: linear-gradient(135deg, #fed7d7 0%, #fbb6ce 100%);
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        border-left: 5px solid #f56565;
    }

    .alert-title {
        color: #c53030;
        font-weight: 700;
        font-size: 18px;
        margin-bottom: 10px;
    }

    .performance-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .activity-feed {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        max-height: 500px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e2e8f0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 16px;
        color: white;
    }

    .icon-success {
        background: linear-gradient(135deg, #48bb78, #38a169);
    }

    .icon-warning {
        background: linear-gradient(135deg, #ed8936, #dd6b20);
    }

    .icon-info {
        background: linear-gradient(135deg, #4299e1, #3182ce);
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .action-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 15px;
        padding: 20px;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .action-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
    }

    @media (max-width: 768px) {
        .performance-grid {
            grid-template-columns: 1fr;
        }

        .metrics-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="supervisor-dashboard">
    <div class="content container-fluid">
        <!-- En-tête officiel DGTCP Superviseur -->
        <div class="dgtcp-header fade-in">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    @include('partials.dgtcp-logo')
                </div>
                <div class="col-md-10">
                    <h1>🎯 DGTCP - Supervision & Contrôle</h1>
                    <p>Tableau de Bord Superviseur - Monitoring des Opérations du Trésor Public</p>
                </div>
            </div>
        </div>

        <!-- Alertes officielles DGTCP -->
        <div class="dgtcp-alert warning fade-in">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>Alertes de Supervision :</strong>
                <ul class="mb-0 mt-2">
                    <li>3 demandes de fonds en attente de validation</li>
                    <li>Dépassement de délai sur 2 postes douaniers</li>
                    <li>Rapport mensuel à valider avant le 30</li>
                </ul>
            </div>
        </div>

        <!-- Métriques principales -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">Efficacité Globale</div>
                <div class="metric-value">87%</div>
                <div class="metric-trend trend-up">
                    <i class="fas fa-arrow-up me-1"></i>
                    +5% par rapport au mois dernier
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Temps Moyen de Traitement</div>
                <div class="metric-value">2.4h</div>
                <div class="metric-trend trend-down">
                    <i class="fas fa-arrow-down me-1"></i>
                    -0.6h par rapport au mois dernier
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Postes Actifs</div>
                <div class="metric-value">24</div>
                <div class="metric-trend trend-up">
                    <i class="fas fa-arrow-up me-1"></i>
                    +2 nouveaux postes ce mois
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-label">Taux de Conformité</div>
                <div class="metric-value">94%</div>
                <div class="metric-trend trend-up">
                    <i class="fas fa-arrow-up me-1"></i>
                    +3% par rapport au mois dernier
                </div>
            </div>
        </div>

        <!-- Graphiques de performance -->
        <div class="performance-grid">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-line me-2 text-primary"></i>
                    Évolution des Demandes de Fonds
                </div>
                <canvas id="demandesChart" height="300"></canvas>
            </div>

            <div class="activity-feed">
                <h5 class="mb-4">📋 Activités Récentes</h5>

                <div class="activity-item">
                    <div class="activity-icon icon-success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <strong>Demande approuvée</strong><br>
                        <small class="text-muted">Poste de Cotonou - 2.5M FCFA</small><br>
                        <small class="text-muted">Il y a 15 minutes</small>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon icon-info">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div>
                        <strong>Rapport généré</strong><br>
                        <small class="text-muted">Rapport mensuel des recettes</small><br>
                        <small class="text-muted">Il y a 1 heure</small>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon icon-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <strong>Attention requise</strong><br>
                        <small class="text-muted">Écart détecté - Poste de Lomé</small><br>
                        <small class="text-muted">Il y a 2 heures</small>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon icon-success">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div>
                        <strong>Paiement effectué</strong><br>
                        <small class="text-muted">Transfert vers Parakou - 1.8M FCFA</small><br>
                        <small class="text-muted">Il y a 3 heures</small>
                    </div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon icon-info">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div>
                        <strong>Nouveau utilisateur</strong><br>
                        <small class="text-muted">Agent comptable ajouté</small><br>
                        <small class="text-muted">Il y a 5 heures</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des recettes par poste -->
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-chart-pie me-2 text-success"></i>
                Répartition des Recettes par Poste
            </div>
            <canvas id="recettesChart" height="200"></canvas>
        </div>

        <!-- Actions officielles DGTCP -->
        <div class="row mt-4">
            <div class="col-md-3 mb-3">
                <button class="dgtcp-btn primary w-100" onclick="generateReport()">
                    <i class="fas fa-file-pdf"></i>
                    Générer Rapport
                </button>
            </div>
            <div class="col-md-3 mb-3">
                <button class="dgtcp-btn secondary w-100" onclick="validatePendingRequests()">
                    <i class="fas fa-check-circle"></i>
                    Valider Demandes
                </button>
            </div>
            <div class="col-md-3 mb-3">
                <button class="dgtcp-btn accent w-100" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    Exporter Données
                </button>
            </div>
            <div class="col-md-3 mb-3">
                <button class="dgtcp-btn outline w-100" onclick="viewAnalytics()">
                    <i class="fas fa-chart-line"></i>
                    Voir Analytics
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Graphique des demandes de fonds
    const demandesCtx = document.getElementById('demandesChart').getContext('2d');
    new Chart(demandesCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun'],
            datasets: [{
                label: 'Demandes de Fonds (Millions FCFA)',
                data: [12.5, 19.2, 15.8, 22.1, 18.7, 25.3],
                borderColor: 'rgb(102, 126, 234)',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Graphique des recettes par poste
    const recettesCtx = document.getElementById('recettesChart').getContext('2d');
    new Chart(recettesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Cotonou', 'Parakou', 'Lomé', 'Niamey', 'Autres'],
            datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#f5576c',
                    '#4facfe'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });

    // Animation des cartes métriques
    const metricCards = document.querySelectorAll('.metric-card');
    metricCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150);
    });
});

// Fonctions pour les actions rapides
function generateReport() {
    alert('Génération du rapport en cours...');
}

function validatePendingRequests() {
    alert('Redirection vers les demandes en attente...');
}

function exportData() {
    alert('Export des données en cours...');
}

function viewAnalytics() {
    alert('Ouverture des analytics détaillées...');
}
</script>
@endsection
