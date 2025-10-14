<?php

namespace App\Http\Controllers;
use App\Models\DemandeFonds;
use App\Models\Paiement;
use App\Models\Poste;
use App\Models\Tresorerie;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
        public function index()
    {
        // Récupérer toutes les demandes pour le tableau détaillé
        $demandesFonds = DemandeFonds::with('poste')
        ->orderBy('created_at', 'desc')
        ->paginate(21);

        // Calculs pour les statistiques - tous postes confondus
        $fondsDemandes = DemandeFonds::sum('total_courant'); // Total des fonds demandés
        $fondsRecettes = DemandeFonds::sum('montant_disponible'); // Total des recettes disponibles

        // Fonds en cours de traitement (en_attente) - cumul des soldes pour tous les postes
        $fondsEnCours = DemandeFonds::where('status', 'en_attente')
            ->sum('solde');

        // Paiements effectués (approuvés) - cumul des montants pour tous les postes
        $paiementsEffectues = DemandeFonds::where('status', 'approuve')
            ->sum('montant');

       /*  $dataTPR = DemandeFonds::where('type', 'TPR')->get();
        $dataACCT = DemandeFonds::where('type', 'ACCT')->get();
 */

        return view('dashboard.admin', compact('demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues'));
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
