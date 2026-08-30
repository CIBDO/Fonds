<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;

class AcctController extends Controller
{
    public function __construct(private DashboardAnalyticsService $analytics) {}

    public function index()
    {
        $demandesFonds = DemandeFonds::with('poste')
            ->orderBy('created_at', 'desc')
            ->paginate(21);

        $analytics = $this->analytics->build();

        $fondsDemandes = $analytics['kpis']['fonds_demandes'];
        $fondsRecettes = $analytics['kpis']['fonds_recettes'];
        $fondsEnCours = $analytics['kpis']['fonds_en_cours'];
        $paiementsEffectues = $analytics['kpis']['paiements_effectues'];

        return view('dashboard.acct', compact(
            'demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues', 'analytics'
        ));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
