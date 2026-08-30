<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TresorierController extends Controller
{
    public function __construct(private DashboardAnalyticsService $analytics) {}

    public function index()
    {
        $user = Auth::user();
        $posteId = $user->poste_id;

        $demandesFonds = DemandeFonds::where('poste_id', $posteId)
            ->with('poste')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $analytics = $this->analytics->build($posteId);

        $fondsDemandes = $analytics['kpis']['fonds_demandes'];
        $fondsRecettes = $analytics['kpis']['fonds_recettes'];
        $fondsEnCours = $analytics['kpis']['fonds_en_cours'];
        $paiementsEffectues = $analytics['kpis']['paiements_effectues'];

        return view('dashboard.tresorier', compact(
            'demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues', 'analytics'
        ));
    }
}
