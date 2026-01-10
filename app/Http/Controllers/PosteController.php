<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Auth;
class PosteController extends Controller
{
    private function authorizeRole(array $roles)
{
    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, '🚫 Accès refusé ! Vous n\'avez pas les permissions nécessaires pour accéder à cette page. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter votre administrateur.');
    }
}

    // Afficher la liste des postes
    public function index(Request $request)
    {
        $this->authorizeRole(['admin']);
        $nom = $request->input('nom');
        $postes = Poste::paginate(10)->appends($request->only(['nom']));
        return view('postes.index', compact('postes', 'nom'));
    }

    // Afficher le formulaire pour créer un nouveau poste
    public function create()
    {
        $this->authorizeRole(['admin']);
        return view('postes.add');
    }

    // Enregistrer un nouveau poste
    public function store(Request $request)
    {
        $this->authorizeRole(['admin']);
        // Valider la requête
        $request->validate([
        'nom' => 'required|string|max:255',
    ]);

    // Créer le poste
    Poste::create([
        'nom' => $request->nom,
    ]);

    alert()->success('Success', 'Poste créé avec succès.');
        return redirect()->route('postes.index');
    }

    // Afficher les détails d'un poste
    public function show(Poste $poste)
    {
        return view('postes.show', compact('poste'));
    }

    // Afficher le formulaire pour éditer un poste
    public function edit(Poste $poste)
    {
        $this->authorizeRole(['admin']);
        return view('postes.edit', compact('poste'));
    }

    // Mettre à jour un poste
    public function update(Request $request, Poste $poste)
    {
        $this->authorizeRole(['admin']);
        $request->validate([
            'nom' => 'required|string|max:255',
        ]);

        $poste->update($request->all());

        alert()->success('Success', 'Poste mis à jour avec succès.');
        return redirect()->route('postes.index');
    }

    // Supprimer un poste
    public function destroy(Poste $poste)
    {
        $this->authorizeRole(['admin']);
        $poste->delete();

        alert()->success('Success', 'Poste supprimé avec succès.');
        return redirect()->route('postes.index');
    }
}
