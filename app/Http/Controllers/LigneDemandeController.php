<?php

namespace App\Http\Controllers;

use App\Models\LigneDemande;
use App\Models\DemandeFonds;
use Illuminate\Http\Request;

class LigneDemandeController extends Controller
{
    public function index()
    {
        $lignes = LigneDemande::all();
        return view('lignes.index', compact('lignes'));
    }

    public function create(DemandeFonds $demande)
    {
        return view('lignes.create', compact('demande'));
    }

    public function store(Request $request, DemandeFonds $demande)
    {
        $validated = $request->validate([
            'libelle' => 'required|string',
            'salaire_net' => 'required|numeric',
            'revers_salaire' => 'nullable|numeric',
            'total_mois_courant' => 'required|numeric',
            'salaire_mois_anterieur' => 'required|numeric',
        ]);

        $demande->lignes()->create($validated);
        return redirect()->route('demandes.show', $demande)->with('success', 'Ligne de demande ajoutée avec succès.');
    }

    public function show(LigneDemande $ligne)
    {
        return view('lignes.show', compact('ligne'));
    }

    public function edit(LigneDemande $ligne)
    {
        return view('lignes.edit', compact('ligne'));
    }

    public function update(Request $request, LigneDemande $ligne)
    {
        $validated = $request->validate([
            'libelle' => 'required|string',
            'salaire_net' => 'required|numeric',
            'revers_salaire' => 'nullable|numeric',
            'total_mois_courant' => 'required|numeric',
            'salaire_mois_anterieur' => 'required|numeric',
        ]);

        $ligne->update($validated);
        return redirect()->route('lignes.index')->with('success', 'Ligne mise à jour avec succès.');
    }

    public function destroy(LigneDemande $ligne)
    {
        $ligne->delete();
        return redirect()->route('lignes.index')->with('success', 'Ligne supprimée avec succès.');
    }
}
