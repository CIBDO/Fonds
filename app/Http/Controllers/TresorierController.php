<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TresorierController extends Controller
{
    /**
     * Affiche le tableau de bord pour le trésorier.
     */
    public function index()
    {
        $user = Auth::user();

        // Filtrer les demandes de fonds pour le poste de l'utilisateur connecté
        $demandesFonds = DemandeFonds::where('poste_id', $user->poste_id)->with('poste')->paginate(8);

        // Calculs spécifiques pour les totaux uniquement pour le poste de l'utilisateur
        $fondsDemandes = DemandeFonds::where('poste_id', $user->poste_id)->sum('total_courant');
        $fondsRecettes = DemandeFonds::where('poste_id', $user->poste_id)->sum('montant_disponible');
        $fondsEnCours = DemandeFonds::where('poste_id', $user->poste_id)->sum('solde');
        $paiementsEffectues = DemandeFonds::where('poste_id', $user->poste_id)->sum('montant');

        return view('dashboard.tresorier', compact('demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues'));
    }
}
