<?php

namespace App\Http\Controllers;

use App\Models\DemandeFonds;
use Illuminate\Http\Request;

class DemandeFondsController extends Controller
{
    public function index()
    {
        $demandes = DemandeFonds::all();
        return view('demandes.index', compact('demandes'));
    }

    public function create()
    {
        return view('demandes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'mois' => 'required|string',
            'annee' => 'required|integer',
            'total_demande' => 'required|numeric',
        ]);

        DemandeFonds::create($validated);
        return redirect()->route('demandes.index')->with('success', 'Demande de fonds créée avec succès.');
    }

    public function show(DemandeFonds $demande)
    {
        return view('demandes.show', compact('demande'));
    }

    public function edit(DemandeFonds $demande)
    {
        return view('demandes.edit', compact('demande'));
    }

    public function update(Request $request, DemandeFonds $demande)
    {
        $validated = $request->validate([
            'mois' => 'required|string',
            'annee' => 'required|integer',
            'total_demande' => 'required|numeric',
            'status' => 'required|string|in:en_attente,approuve,rejete',
        ]);

        $demande->update($validated);
        return redirect()->route('demandes.index')->with('success', 'Demande de fonds mise à jour avec succès.');
    }

    public function destroy(DemandeFonds $demande)
    {
        $demande->delete();
        return redirect()->route('demandes.index')->with('success', 'Demande de fonds supprimée avec succès.');
    }
}
