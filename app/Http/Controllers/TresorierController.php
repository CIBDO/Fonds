<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use Illuminate\Http\Request;


class TresorierController extends Controller
{
    /**
     * Affiche le tableau de bord pour le trésorier.
     */
    public function index()
    {
      
        // Calculs spécifiques
        $demandesFonds = DemandeFonds::with('poste')->paginate(8);
        $fondsDemandes = DemandeFonds::sum('total_courant');
        $fondsRecettes = DemandeFonds::sum('montant_disponible');
        $fondsEnCours = DemandeFonds::sum('solde');
        $paiementsEffectues = DemandeFonds::sum('montant');

        return view('dashboard.tresorier', compact('demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues'));
    }
}
