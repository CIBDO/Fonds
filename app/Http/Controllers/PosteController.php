<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class PosteController extends Controller
{
    // Afficher la liste des postes
    public function index()
    {
        $postes = Poste::paginate(10); 
        return view('postes.index', compact('postes'));
    }

    // Afficher le formulaire pour créer un nouveau poste
    public function create()
    {
       
        return view('postes.add');
    }

    // Enregistrer un nouveau poste
    public function store(Request $request)
{
    // Valider la requête
    $request->validate([
        'nom' => 'required|string|max:255',
    ]);

    // Créer le poste
    Poste::create([
        'nom' => $request->nom,
    ]);

    Alert::success('Success', 'Poste créé avec succès.');
    return redirect()->route('postes.index')->with('success', 'Poste créé avec succès.');
}

    // Afficher les détails d'un poste
    public function show(Poste $poste)
    {
        return view('postes.show', compact('poste'));
    }

    // Afficher le formulaire pour éditer un poste
    public function edit(Poste $poste)
    {
        return view('postes.edit', compact('poste'));
    }

    // Mettre à jour un poste
    public function update(Request $request, Poste $poste)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $poste->update($request->all());

        Alert::success('Success', 'Poste mis à jour avec succès.');
        return redirect()->route('postes.index')->with('success', 'Poste mis à jour avec succès.');
    }

    // Supprimer un poste
    public function destroy(Poste $poste)
    {
        $poste->delete();

        Alert::success('Success', 'Poste supprimé avec succès.');
        return redirect()->route('postes.index')->with('success', 'Poste supprimé avec succès.');
    }
}
