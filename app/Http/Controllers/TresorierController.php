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
        // Inclure toutes les demandes pour le tableau détaillé
        $demandesFonds = DemandeFonds::where('poste_id', $user->poste_id)->with('poste')
        ->orderBy('created_at', 'desc')
        ->paginate(12);

        // Calculs spécifiques pour les totaux uniquement pour le poste de l'utilisateur
        $fondsDemandes = DemandeFonds::where('poste_id', $user->poste_id)->sum('total_courant');
        $fondsRecettes = DemandeFonds::where('poste_id', $user->poste_id)->sum('montant_disponible');

        // Fonds en cours de traitement (en_attente) - cumul des soldes
        $fondsEnCours = DemandeFonds::where('poste_id', $user->poste_id)
            ->where('status', 'en_attente')
            ->sum('solde');

        // Paiements effectués (approuvés) - cumul des montants
        $paiementsEffectues = DemandeFonds::where('poste_id', $user->poste_id)
            ->where('status', 'approuve')
            ->sum('montant');

        return view('dashboard.tresorier', compact('demandesFonds', 'fondsDemandes', 'fondsRecettes', 'fondsEnCours', 'paiementsEffectues'));
    }
}
