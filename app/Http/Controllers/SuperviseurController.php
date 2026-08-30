<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;

class SuperviseurController extends Controller
{
    public function __construct(private DashboardAnalyticsService $analytics) {}

    public function index()
    {
        $analytics = $this->analytics->build();

        $demandesFonds = DemandeFonds::with('poste')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'totalDemandes' => $analytics['kpis']['total'],
            'demandesEnAttente' => $analytics['kpis']['en_attente'],
            'demandesToday' => DemandeFonds::whereDate('created_at', today())->count(),
            'postesActifs' => $analytics['kpis']['postes_actifs'],
            'efficaciteGlobale' => $analytics['kpis']['taux_validation'],
            'tempsMoyenTraitement' => 2.4,
            'tauxConformite' => 100 - $analytics['kpis']['taux_rejet'],
            'demandesParMois' => collect($analytics['monthly']['counts']),
            'recettesParPoste' => DemandeFonds::with('poste')
                ->selectRaw('poste_id, SUM(montant_disponible) as total_recettes')
                ->groupBy('poste_id')
                ->limit(5)
                ->get(),
            'activitesRecentes' => DemandeFonds::with('poste')
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get(),
        ];

        return view('dashboard.superviseur', compact('stats', 'analytics', 'demandesFonds'));
    }

    public function generateReport()
    {
        $rapport = [
            'periode' => now()->format('Y-m'),
            'total_demandes' => DemandeFonds::count(),
            'total_montant' => DemandeFonds::sum('montant'),
            'postes_performance' => \App\Models\Poste::with(['demandesFonds'])->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Rapport généré avec succès',
            'data' => $rapport,
        ]);
    }

    public function validatePendingRequests()
    {
        $demandesEnAttente = DemandeFonds::where('status', 'en_attente')->get();

        return response()->json([
            'success' => true,
            'count' => $demandesEnAttente->count(),
            'demandes' => $demandesEnAttente,
        ]);
    }

    public function exportData()
    {
        return response()->json([
            'success' => true,
            'message' => 'Données exportées avec succès',
            'data' => [
                'demandes' => DemandeFonds::with('poste')->get(),
                'statistiques' => [
                    'total_demandes' => DemandeFonds::count(),
                    'total_montant' => DemandeFonds::sum('montant'),
                    'demandes_par_statut' => DemandeFonds::groupBy('status')->selectRaw('status, count(*) as count')->get(),
                ],
            ],
        ]);
    }

    public function getAnalytics()
    {
        return response()->json([
            'success' => true,
            'analytics' => $this->analytics->build(),
        ]);
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
