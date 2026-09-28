<?php

namespace App\Http\Controllers;

use App\Models\PaiementFnl;
use App\Services\DashboardAnalyticsFnlService;
use Illuminate\Http\Request;

class AccdController extends Controller
{
    public function __construct(private DashboardAnalyticsFnlService $analytics) {}

    public function index()
    {
        $paiementsFnl = PaiementFnl::with(['poste', 'saisiPar'])
            ->orderBy('annee', 'desc')
            ->orderBy('mois', 'desc')
            ->paginate(21);

        $analytics = $this->analytics->build();

        $fondsDemandes = $analytics['kpis']['fonds_demandes'];
        $fondsRecettes = $analytics['kpis']['fonds_recettes'];
        $fondsEnCours = $analytics['kpis']['fonds_en_cours'];
        $paiementsEffectues = $analytics['kpis']['paiements_effectues'];

        return view('dashboard.accd', compact(
            'paiementsFnl', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues', 'analytics'
        ));
    }

    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
