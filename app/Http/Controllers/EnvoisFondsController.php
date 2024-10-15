<?php

namespace App\Http\Controllers;
use RealRashid\SweetAlert\Facades\Alert; // Assurez-vous que l'importation est correcte
use App\Models\DemandeFonds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Models\Poste;
use App\Models\User;
use App\Notifications\DemandeFondsStatusNotification; // Assurez-vous que le chemin est correct

class EnvoisFondsController extends Controller
{
    public function index(Request $request)
    {
        $query = DemandeFonds::query();
        
        // Filtre par poste
        if ($request->filled('poste')) {
            $query->whereHas('poste', function($q) use ($request) {
                $q->where('nom', 'like', '%' . $request->input('poste') . '%');
            });
        }
    
        // Filtre par mois
        if ($request->filled('mois')) {
            $query->where('mois', 'like', '%' . $request->input('mois') . '%');
        }
    
        // Filtre par montant total courant (exact)
        if ($request->filled('total_courant')) {
            $query->where('total_courant', $request->input('total_courant'));
        }
    
        // Filtre par date de création
        if ($request->filled('created_at')) {
            $query->whereDate('created_at', $request->input('created_at'));
        }
    
        // Pagination avec les filtres appliqués
        $demandeFonds = $query->with('user', 'poste')->orderBy('created_at', 'desc')->paginate(8)->appends($request->except('page'));
    
        return view('envois.index', compact('demandeFonds'));
    }

 public function updateStatus(Request $request, $id)
    {
        $demande = DemandeFonds::findOrFail($id);

        // Validation des données
        $request->validate([
            'date_envois' => 'required|date',
            'status' => 'required|string',
            'montant' => 'required|numeric',
            'observation' => 'nullable|string',
        ]);

        // Mise à jour du statut de la demande
        $demande->status = $request->input('status');
        $demande->montant = $request->input('montant');
        $demande->date_envois = $request->input('date_envois');
        $demande->observation = $request->input('observation');
        $demande->save();

        // Envoyer une notification à l'initiateur
        $demande->user->notify(new DemandeFondsStatusNotification($demande));
        Alert::success('Success', 'Le Fonds a été envoyé avec succès.');
        return redirect()->route('envois-fonds.index')->with('success', 'Statut mis à jour avec succès.');
    }
    
    public function store(Request $request)
    {
        $demande = DemandeFonds::findOrFail($request->id);
        $demande->status = $request->status;
        $demande->montant = $request->montant;
        $demande->date_envois = $request->date_envois;
        $demande->observation = $request->observation;
        $demande->save();
        Alert::success('Success', 'Le Fonds a été envoyé avec succès.');
        return redirect()->route('envois.index')->with('success', 'Statut mis à jour avec succès.');
    }
}
