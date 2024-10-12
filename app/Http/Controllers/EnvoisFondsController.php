<?php

namespace App\Http\Controllers;
use Alert;
use App\Models\DemandeFonds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Models\Poste;
use App\Models\User;

class EnvoisFondsController extends Controller
{
    public function index(Request $request)
    {
        $query = DemandeFonds::query();
    
        // Filtrer par référence si le champ est renseigné
        if ($request->filled('poste')) {
                $query->whereHas('poste', function($q) use ($request) {
                    $q->where('nom', 'like', '%' . $request->input('poste') . '%');
            });
        }
    
        // Filtrer par expéditeur si le champ est renseigné
        if ($request->filled('mois')) {
            $query->where('mois', 'like', '%' . $request->input('mois') . '%');
        }
    
        // Filtrer par montant total courant si le champ est renseigné
        if ($request->filled('total_courant')) {
            $query->where('total_courant', 'like', '%' . $request->input('total_courant') . '%');
        }
    
        // Filtrer par date d'arrivée si le champ est renseigné
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }
    
        // Afficher toutes les demandes de fonds
        $demandeFonds = $query->with('user', 'poste')->orderBy('created_at', 'desc')->paginate(8);
        return view('envois.index', compact('demandeFonds'));
    }
}
