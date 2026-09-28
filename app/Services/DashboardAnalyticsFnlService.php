<?php

namespace App\Services;

use App\Models\PaiementFnl;
use App\Models\Poste;

class DashboardAnalyticsFnlService
{
    private const MONTHS_FR = [
        1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr',
        5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Aoû',
        9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
    ];

    public function build(?int $posteId = null): array
    {
        $base = PaiementFnl::query();
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
        $soumis = (clone $query)->where('statut', 'soumis')->count();
        $valide = (clone $query)->where('statut', 'valide')->count();
        $rejete = (clone $query)->where('statut', 'rejete')->count();

        $retenuesTotal = (clone $query)->sum('retenue_fnl');
        $retenuesValides = (clone $query)->where('statut', 'valide')->sum('retenue_fnl');
        $retenuesEnAttente = (clone $query)->where('statut', 'soumis')->sum('retenue_fnl');
        $retenuesRejetees = (clone $query)->where('statut', 'rejete')->sum('retenue_fnl');

        $tauxValidation = $total > 0 ? round(($valide / $total) * 100, 1) : 0;
        $tauxRejet = $total > 0 ? round(($rejete / $total) * 100, 1) : 0;

        $thisMonth = (clone $query)->whereMonth('date_saisie', now()->month)
            ->whereYear('date_saisie', now()->year)->count();
        $lastMonth = (clone $query)->whereMonth('date_saisie', now()->subMonth()->month)
            ->whereYear('date_saisie', now()->subMonth()->year)->count();
        $evolutionMensuelle = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
            : ($thisMonth > 0 ? 100 : 0);

        return [
            'total' => $total,
            'en_attente' => $soumis,
            'approuve' => $valide,
            'rejete' => $rejete,
            'fonds_demandes' => (float) $retenuesTotal,
            'fonds_recettes' => (float) $retenuesValides,
            'fonds_en_cours' => (float) $retenuesEnAttente,
            'paiements_effectues' => (float) $retenuesValides,
            'taux_validation' => $tauxValidation,
            'taux_rejet' => $tauxRejet,
            'ecart_recettes' => 0,
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
        $records = (clone $query)->whereRaw("date_saisie is not null and date_saisie >= ?", [$start])
            ->orWhere(function($q) use ($start) {
                $q->where('annee', '>=', $start->year);
            })
            ->get();

        for ($i = 0; $i < 12; $i++) {
            $date = $start->copy()->addMonths($i);
            $annee = (int) $date->format('Y');
            $mois = (int) $date->format('n');
            $labels[] = self::MONTHS_FR[$mois] . ' ' . $date->format('y');

            $monthData = $records->filter(
                function ($p) use ($annee, $mois) {
                    return (int) $p->annee === $annee && (int) $p->mois === $mois;
                }
            );

            $counts[] = $monthData->count();
            $montants[] = round($monthData->sum('retenue_fnl') / 1_000_000, 2);
            $recettes[] = round($monthData->where('statut', 'valide')->sum('retenue_fnl') / 1_000_000, 2);
            $approuves[] = $monthData->where('statut', 'valide')->count();
        }

        return compact('labels', 'counts', 'montants', 'recettes', 'approuves');
    }

    private function buildStatusDistribution($query): array
    {
        $soumis = (clone $query)->where('statut', 'soumis')->count();
        $valide = (clone $query)->where('statut', 'valide')->count();
        $rejete = (clone $query)->where('statut', 'rejete')->count();

        return [
            'labels' => ['Soumis', 'Validé', 'Rejeté'],
            'series' => [$soumis, $valide, $rejete],
            'colors' => ['#FFD600', '#009739', '#E30613'],
        ];
    }

    private function buildTopPostes(): array
    {
        $postes = PaiementFnl::query()
            ->with('poste')
            ->selectRaw('poste_id, SUM(retenue_fnl) as total_retenue, COUNT(*) as nb')
            ->groupBy('poste_id')
            ->orderByDesc('total_retenue')
            ->limit(8)
            ->get();

        return [
            'labels' => $postes->map(fn ($p) => $p->poste?->nom ?? 'N/A')->values()->all(),
            'demandes' => $postes->map(fn ($p) => round($p->total_retenue / 1_000_000, 2))->values()->all(),
            'recettes' => $postes->map(function ($p) {
                $totalValide = PaiementFnl::where('poste_id', $p->poste_id)->where('statut', 'valide')->sum('retenue_fnl');
                return round($totalValide / 1_000_000, 2);
            })->values()->all(),
            'counts' => $postes->pluck('nb')->values()->all(),
        ];
    }

    private function buildCategoryBreakdown($query): array
    {
        $totals = [
            'retenues' => (clone $query)->sum('retenue_fnl'),
        ];

        $posteIds = (clone $query)->distinct()->pluck('poste_id');
        $categories = Poste::whereIn('id', $posteIds)
            ->selectRaw("COALESCE(SUBSTRING_INDEX(nom, ' ', 1), nom) as cat_nom, COUNT(*) as nb")
            ->groupBy('cat_nom')
            ->limit(7)
            ->pluck('nb', 'cat_nom');

        $labels = $categories->keys()->values()->all();
        if (empty($labels)) {
            $labels = ['Postes'];
        }

        $series = array_map(fn ($n) => round((float) $n / 1_000_000, 2), $categories->values()->all());
        if (empty($series)) {
            $series = [round((float) $totals['retenues'] / 1_000_000, 2)];
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }

    private function buildRecentActivities($query): array
    {
        return (clone $query)
            ->with(['poste', 'saisiPar'])
            ->orderByDesc('date_saisie')
            ->limit(8)
            ->get()
            ->map(function (PaiementFnl $p) {
                $statusConfig = match ($p->statut) {
                    'valide' => ['icon' => 'tabler-circle-check', 'color' => 'success', 'label' => 'Paiement validé'],
                    'rejete' => ['icon' => 'tabler-circle-x', 'color' => 'danger', 'label' => 'Paiement rejeté'],
                    default => ['icon' => 'tabler-clock', 'color' => 'warning', 'label' => 'Paiement soumis'],
                };

                return [
                    'icon' => $statusConfig['icon'],
                    'color' => $statusConfig['color'],
                    'label' => $statusConfig['label'],
                    'detail' => ($p->poste?->nom ?? 'Poste') . ' — ' . number_format($p->retenue_fnl ?? 0, 0, '', ' ') . ' FCFA',
                    'time' => $p->date_saisie?->diffForHumans() ?? '',
                    'mois' => $p->mois,
                ];
            })
            ->all();
    }

    private function buildSparklines($query): array
    {
        $start = now()->subMonths(6)->startOfMonth();
        $records = (clone $query)->get();
        $demandes = [];
        $recettes = [];
        $validations = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addMonths($i);
            $annee = (int) $date->format('Y');
            $mois = (int) $date->format('n');

            $monthData = $records->filter(
                fn ($p) => (int) $p->annee === $annee && (int) $p->mois === $mois
            );

            $demandes[] = round($monthData->sum('retenue_fnl') / 1_000_000, 1);
            $recettes[] = round($monthData->where('statut', 'valide')->sum('retenue_fnl') / 1_000_000, 1);
            $validations[] = $monthData->where('statut', 'valide')->count();
        }

        return compact('demandes', 'recettes', 'validations');
    }

    private function buildAlertes($query, ?int $posteId): array
    {
        $alertes = [];

        $soumis = (clone $query)->where('statut', 'soumis')->count();
        if ($soumis > 0) {
            $alertes[] = [
                'type' => 'warning',
                'message' => "{$soumis} paiement(s) en attente de validation ACCD",
            ];
        }

        $rejets = (clone $query)->where('statut', 'rejete')
            ->where(function ($q) {
                $q->whereMonth('date_validation', now()->month)
                    ->orWhere(function ($q2) {
                        $q2->whereMonth('updated_at', now()->month);
                    });
            })
            ->count();
        if ($rejets > 0) {
            $alertes[] = [
                'type' => 'info',
                'message' => "{$rejets} rejet(s) ce mois — vérifier les motifs",
            ];
        }

        if (empty($alertes)) {
            $alertes[] = [
                'type' => 'success',
                'message' => 'Aucune alerte critique — les paiements FNL sont à jour',
            ];
        }

        return $alertes;
    }
}
