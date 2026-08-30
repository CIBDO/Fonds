<?php

namespace App\Services;

use App\Models\DemandeFonds;
use App\Models\Poste;

class DashboardAnalyticsService
{
    private const MONTHS_FR = [
        1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
        5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Aoû',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
    ];

    public function build(?int $posteId = null): array
    {
        $base = DemandeFonds::query();
        if ($posteId) {
            $base->where('poste_id', $posteId);
        }

        $kpis = $this->buildKpis(clone $base);
        $monthly = $this->buildMonthlySeries(clone $base);
        $status = $this->buildStatusDistribution(clone $base);
        $topPostes = $posteId ? [] : $this->buildTopPostes();
        $categories = $this->buildCategoryBreakdown(clone $base);
        $activities = $this->buildRecentActivities(clone $base);
        $sparklines = $this->buildSparklines(clone $base);
        $alertes = $this->buildAlertes(clone $base, $posteId);

        return compact('kpis', 'monthly', 'status', 'topPostes', 'categories', 'activities', 'sparklines', 'alertes')
            + ['posteId' => $posteId];
    }

    private function buildKpis($query): array
    {
        $total = (clone $query)->count();
        $enAttente = (clone $query)->where('status', 'en_attente')->count();
        $approuve = (clone $query)->where('status', 'approuve')->count();
        $rejete = (clone $query)->where('status', 'rejete')->count();

        $fondsDemandes = (clone $query)->sum('total_courant');
        $fondsRecettes = (clone $query)->sum('montant_disponible');
        $fondsEnCours = (clone $query)->where('status', 'en_attente')->sum('solde');
        $paiementsEffectues = (clone $query)->where('status', 'approuve')->sum('montant');

        $tauxValidation = $total > 0 ? round(($approuve / $total) * 100, 1) : 0;
        $tauxRejet = $total > 0 ? round(($rejete / $total) * 100, 1) : 0;
        $ecartRecettes = $fondsRecettes > 0
            ? round((($fondsDemandes - $fondsRecettes) / $fondsRecettes) * 100, 1)
            : 0;

        $thisMonth = (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $lastMonth = (clone $query)->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)->count();
        $evolutionMensuelle = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            'total' => $total,
            'en_attente' => $enAttente,
            'approuve' => $approuve,
            'rejete' => $rejete,
            'fonds_demandes' => (float) $fondsDemandes,
            'fonds_recettes' => (float) $fondsRecettes,
            'fonds_en_cours' => (float) $fondsEnCours,
            'paiements_effectues' => (float) $paiementsEffectues,
            'taux_validation' => $tauxValidation,
            'taux_rejet' => $tauxRejet,
            'ecart_recettes' => $ecartRecettes,
            'evolution_mensuelle' => $evolutionMensuelle,
            'demandes_ce_mois' => $thisMonth,
            'postes_actifs' => Poste::count(),
        ];
    }

    private function buildMonthlySeries($query): array
    {
        $labels = [];
        $counts = [];
        $montants = [];
        $recettes = [];
        $approuves = [];

        $start = now()->subMonths(11)->startOfMonth();
        $records = (clone $query)->where('created_at', '>=', $start)->get();

        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $key = $date->format('Y-m');
            $labels[] = self::MONTHS_FR[(int) $date->format('n')] . ' ' . $date->format('y');

            $monthData = $records->filter(
                fn ($d) => $d->created_at && $d->created_at->format('Y-m') === $key
            );

            $counts[] = $monthData->count();
            $montants[] = round($monthData->sum('total_courant') / 1_000_000, 2);
            $recettes[] = round($monthData->sum('montant_disponible') / 1_000_000, 2);
            $approuves[] = $monthData->where('status', 'approuve')->count();
        }

        return compact('labels', 'counts', 'montants', 'recettes', 'approuves');
    }

    private function buildStatusDistribution($query): array
    {
        $enAttente = (clone $query)->where('status', 'en_attente')->count();
        $approuve = (clone $query)->where('status', 'approuve')->count();
        $rejete = (clone $query)->where('status', 'rejete')->count();

        return [
            'labels' => ['En attente', 'Approuvé', 'Rejeté'],
            'series' => [$enAttente, $approuve, $rejete],
            'colors' => ['#FFD600', '#009739', '#E30613'],
        ];
    }

    private function buildTopPostes(): array
    {
        $postes = DemandeFonds::query()
            ->with('poste')
            ->selectRaw('poste_id, SUM(total_courant) as total_demande, SUM(montant_disponible) as total_recettes, COUNT(*) as nb')
            ->groupBy('poste_id')
            ->orderByDesc('total_demande')
            ->limit(8)
            ->get();

        return [
            'labels' => $postes->map(fn ($p) => $p->poste?->nom ?? 'N/A')->values()->all(),
            'demandes' => $postes->map(fn ($p) => round($p->total_demande / 1_000_000, 2))->values()->all(),
            'recettes' => $postes->map(fn ($p) => round($p->total_recettes / 1_000_000, 2))->values()->all(),
            'counts' => $postes->pluck('nb')->values()->all(),
        ];
    }

    private function buildCategoryBreakdown($query): array
    {
        $totals = (clone $query)->selectRaw('
            SUM(fonctionnaires_bcs_total_demande) as bcs,
            SUM(collectivite_sante_total_demande) as sante,
            SUM(collectivite_education_total_demande) as education,
            SUM(personnels_saisonniers_total_demande) as saisonniers,
            SUM(epn_total_demande) as epn,
            SUM(ced_total_demande) as ced,
            SUM(ecom_total_demande) as ecom
        ')->first();

        return [
            'labels' => ['BCS', 'Santé', 'Éducation', 'Saisonniers', 'EPN', 'CED', 'ECOM'],
            'series' => [
                round((float) ($totals->bcs ?? 0) / 1_000_000, 2),
                round((float) ($totals->sante ?? 0) / 1_000_000, 2),
                round((float) ($totals->education ?? 0) / 1_000_000, 2),
                round((float) ($totals->saisonniers ?? 0) / 1_000_000, 2),
                round((float) ($totals->epn ?? 0) / 1_000_000, 2),
                round((float) ($totals->ced ?? 0) / 1_000_000, 2),
                round((float) ($totals->ecom ?? 0) / 1_000_000, 2),
            ],
        ];
    }

    private function buildRecentActivities($query): array
    {
        return (clone $query)
            ->with(['poste', 'user'])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get()
            ->map(function (DemandeFonds $d) {
                $statusConfig = match ($d->status) {
                    'approuve' => ['icon' => 'tabler-circle-check', 'color' => 'success', 'label' => 'Demande approuvée'],
                    'rejete' => ['icon' => 'tabler-circle-x', 'color' => 'danger', 'label' => 'Demande rejetée'],
                    default => ['icon' => 'tabler-clock', 'color' => 'warning', 'label' => 'Demande en attente'],
                };

                return [
                    'icon' => $statusConfig['icon'],
                    'color' => $statusConfig['color'],
                    'label' => $statusConfig['label'],
                    'detail' => ($d->poste?->nom ?? 'Poste') . ' — ' . number_format($d->total_courant ?? 0, 0, '', ' ') . ' FCFA',
                    'time' => $d->updated_at?->diffForHumans() ?? '',
                    'mois' => $d->mois,
                ];
            })
            ->all();
    }

    private function buildSparklines($query): array
    {
        $start = now()->subMonths(6)->startOfMonth();
        $records = (clone $query)->where('created_at', '>=', $start)->get();
        $demandes = [];
        $recettes = [];
        $validations = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addMonths($i);
            $key = $date->format('Y-m');
            $monthData = $records->filter(fn ($d) => $d->created_at?->format('Y-m') === $key);

            $demandes[] = round($monthData->sum('total_courant') / 1_000_000, 1);
            $recettes[] = round($monthData->sum('montant_disponible') / 1_000_000, 1);
            $validations[] = $monthData->where('status', 'approuve')->count();
        }

        return compact('demandes', 'recettes', 'validations');
    }

    private function buildAlertes($query, ?int $posteId): array
    {
        $alertes = [];

        $enAttente = (clone $query)->where('status', 'en_attente')->count();
        if ($enAttente > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$enAttente} demande(s) en attente de validation",
            ];
        }

        $rejets = (clone $query)->where('status', 'rejete')
            ->whereMonth('updated_at', now()->month)->count();
        if ($rejets > 0) {
            $alertes[] = [
                'type' => 'info',
                'message' => "{$rejets} rejet(s) ce mois — vérifier les motifs",
            ];
        }

        if (empty($alertes)) {
            $alertes[] = [
                'type' => 'success',
                'message' => 'Aucune alerte critique — les opérations sont à jour',
            ];
        }

        return $alertes;
    }
}
