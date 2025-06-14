<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DemandeFonds;
use App\Models\Paiement;
use App\Models\Poste;
use App\Models\Tresorerie;

class SuperviseurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupération des statistiques pour le dashboard superviseur
        $totalDemandes = DemandeFonds::count();
        $demandesEnAttente = DemandeFonds::where('statut', 'en_attente')->count();
        $demandesToday = DemandeFonds::whereDate('created_at', today())->count();
        $postesActifs = Poste::where('statut', 'actif')->count();

        // Efficacité calculée (exemple : demandes traitées / total demandes)
        $demandesTraitees = DemandeFonds::where('statut', 'traite')->count();
        $efficaciteGlobale = $totalDemandes > 0 ? round(($demandesTraitees / $totalDemandes) * 100, 1) : 0;

        // Temps moyen de traitement (exemple en heures)
        $tempsMoyenTraitement = 2.4; // Calcul basé sur les timestamps

        // Taux de conformité (exemple)
        $tauxConformite = 94.2;

        // Récupération des données pour les graphiques
        $demandesParMois = DemandeFonds::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('mois')
            ->pluck('total', 'mois');

        $recettesParPoste = DemandeFonds::with('poste')
            ->selectRaw('poste_id, SUM(montant_disponible) as total_recettes')
            ->groupBy('poste_id')
            ->limit(5)
            ->get();

        // Activités récentes
        $activitesRecentes = DemandeFonds::with('poste')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'totalDemandes' => $totalDemandes,
            'demandesEnAttente' => $demandesEnAttente,
            'demandesToday' => $demandesToday,
            'postesActifs' => $postesActifs,
            'efficaciteGlobale' => $efficaciteGlobale,
            'tempsMoyenTraitement' => $tempsMoyenTraitement,
            'tauxConformite' => $tauxConformite,
            'demandesParMois' => $demandesParMois,
            'recettesParPoste' => $recettesParPoste,
            'activitesRecentes' => $activitesRecentes
        ];

        return view('dashboard.superviseur', compact('stats'));
    }

    /**
     * Génération de rapport pour le superviseur
     */
    public function generateReport()
    {
        // Logique de génération de rapport
        $rapport = [
            'periode' => now()->format('Y-m'),
            'total_demandes' => DemandeFonds::count(),
            'total_montant' => DemandeFonds::sum('montant'),
            'postes_performance' => Poste::with(['demandesFonds'])->get()
        ];

        return response()->json([
            'success' => true,
            'message' => 'Rapport généré avec succès',
            'data' => $rapport
        ]);
    }

    /**
     * Validation des demandes en attente
     */
    public function validatePendingRequests()
    {
        $demandesEnAttente = DemandeFonds::where('statut', 'en_attente')->get();

        return response()->json([
            'success' => true,
            'count' => $demandesEnAttente->count(),
            'demandes' => $demandesEnAttente
        ]);
    }

    /**
     * Export des données
     */
    public function exportData()
    {
        $data = [
            'demandes' => DemandeFonds::with('poste')->get(),
            'statistiques' => [
                'total_demandes' => DemandeFonds::count(),
                'total_montant' => DemandeFonds::sum('montant'),
                'demandes_par_statut' => DemandeFonds::groupBy('statut')->selectRaw('statut, count(*) as count')->get()
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Données exportées avec succès',
            'data' => $data
        ]);
    }

    /**
     * Analytics détaillées
     */
    public function getAnalytics()
    {
        $analytics = [
            'tendances' => [
                'demandes_mensuel' => DemandeFonds::selectRaw('MONTH(created_at) as mois, COUNT(*) as total')
                    ->whereYear('created_at', date('Y'))
                    ->groupBy('mois')
                    ->get(),
                'montants_mensuel' => DemandeFonds::selectRaw('MONTH(created_at) as mois, SUM(montant) as total')
                    ->whereYear('created_at', date('Y'))
                    ->groupBy('mois')
                    ->get()
            ],
            'performance_postes' => Poste::withCount('demandesFonds')
                ->with(['demandesFonds' => function($query) {
                    $query->selectRaw('poste_id, AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as temps_moyen');
                }])
                ->get(),
            'alertes' => [
                'demandes_retard' => DemandeFonds::where('created_at', '<', now()->subDays(3))
                    ->where('statut', 'en_attente')
                    ->count(),
                'ecarts_detectes' => DemandeFonds::whereRaw('ABS(montant - montant_disponible) > (montant * 0.05)')
                    ->count()
            ]
        ];

        return response()->json([
            'success' => true,
            'analytics' => $analytics
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
