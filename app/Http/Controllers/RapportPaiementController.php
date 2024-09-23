<?php

namespace App\Http\Controllers;

use App\Models\RapportPaiement;
use App\Models\ReceptionFonds;
use Illuminate\Http\Request;

class RapportPaiementController extends Controller
{
    public function index()
    {
        $rapports = RapportPaiement::all();
        return view('rapports.index', compact('rapports'));
    }

    public function create(ReceptionFonds $reception)
    {
        return view('rapports.create', compact('reception'));
    }

    public function store(Request $request, ReceptionFonds $reception)
    {
        $validated = $request->validate([
            'montant_paye' => 'required|numeric',
            'date_paiement' => 'required|date',
            'status' => 'required|string|in:paye,en_cours',
        ]);

        $reception->rapports()->create($validated);
        return redirect()->route('rapports.index')->with('success', 'Rapport de paiement ajouté avec succès.');
    }

    public function show(RapportPaiement $rapport)
    {
        return view('rapports.show', compact('rapport'));
    }

    public function edit(RapportPaiement $rapport)
    {
        return view('rapports.edit', compact('rapport'));
    }

    public function update(Request $request, RapportPaiement $rapport)
    {
        $validated = $request->validate([
            'montant_paye' => 'required|numeric',
            'date_paiement' => 'required|date',
            'status' => 'required|string|in:paye,en_cours',
        ]);

        $rapport->update($validated);
        return redirect()->route('rapports.index')->with('success', 'Rapport de paiement mis à jour avec succès.');
    }

    public function destroy(RapportPaiement $rapport)
    {
        $rapport->delete();
        return redirect()->route('rapports.index')->with('success', 'Rapport de paiement supprimé avec succès.');
    }
}
