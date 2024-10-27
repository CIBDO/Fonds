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
        // Filtrer les demandes pour les demandes de trésorerie
        $demandesFonds = DemandeFonds::with('poste')->whereHas('poste', function ($query) {
            $query->where('id', 1); // ID du poste trésorier
        })->paginate(8);

        // Calculs spécifiques
        $fondsDemandes = DemandeFonds::sum('total_courant');
        $fondsRecettes = DemandeFonds::sum('montant_disponible');
        $fondsEnCours = DemandeFonds::sum('solde');
        $paiementsEffectues = DemandeFonds::sum('montant');

        return view('dashboard.tresorier', compact('demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues'));
    }
}
