<?php

namespace App\Http\Controllers;

use App\Models\ReceptionFonds;
use App\Models\DemandeFonds;
use Illuminate\Http\Request;

class ReceptionFondsController extends Controller
{
    public function index()
    {
        $receptions = ReceptionFonds::all();
        return view('receptions.index', compact('receptions'));
    }

    public function create(DemandeFonds $demande)
    {
        return view('receptions.create', compact('demande'));
    }

    public function store(Request $request, DemandeFonds $demande)
    {
        $validated = $request->validate([
            'montant_recu' => 'required|numeric',
            'date_reception' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        $demande->receptions()->create($validated);
        return redirect()->route('receptions.index')->with('success', 'Réception de fonds enregistrée avec succès.');
    }

    public function show(ReceptionFonds $reception)
    {
        return view('receptions.show', compact('reception'));
    }

    public function edit(ReceptionFonds $reception)
    {
        return view('receptions.edit', compact('reception'));
    }

    public function update(Request $request, ReceptionFonds $reception)
    {
        $validated = $request->validate([
            'montant_recu' => 'required|numeric',
            'date_reception' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        $reception->update($validated);
        return redirect()->route('receptions.index')->with('success', 'Réception de fonds mise à jour avec succès.');
    }

    public function destroy(ReceptionFonds $reception)
    {
        $reception->delete();
        return redirect()->route('receptions.index')->with('success', 'Réception de fonds supprimée avec succès.');
    }
}
