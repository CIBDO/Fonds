<?php

namespace App\Http\Controllers\PCS;

use App\Http\Controllers\Controller;
use App\Models\BureauDouane;
use App\Models\Poste;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class BureauDouaneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des bureaux de douanes
     */
    public function index()
    {
        $bureaux = BureauDouane::with('posteRgd')
            ->orderBy('code')
            ->get();

        return view('pcs.bureaux.index', compact('bureaux'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        // Récupérer le poste RGD
        $posteRgd = Poste::where('nom', 'RGD')->first();

        if (!$posteRgd) {
            Alert::error('Erreur', 'Le poste RGD n\'existe pas dans la base de données.');
            return redirect()->back();
        }

        return view('pcs.bureaux.create', compact('posteRgd'));
    }

    /**
     * Enregistrement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:bureaux_douanes,code',
            'libelle' => 'required|string|max:255',
            'actif' => 'boolean',
        ], [
            'code.required' => 'Le code est obligatoire',
            'code.unique' => 'Ce code existe déjà',
            'libelle.required' => 'Le libellé est obligatoire',
        ]);

        // Récupérer le poste RGD
        $posteRgd = Poste::where('nom', 'RGD')->firstOrFail();

        BureauDouane::create([
            'poste_rgd_id' => $posteRgd->id,
            'code' => $validated['code'],
            'libelle' => $validated['libelle'],
            'actif' => $request->has('actif'),
        ]);

        Alert::success('Succès', 'Bureau de douane créé avec succès');
        return redirect()->route('pcs.bureaux.index');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(BureauDouane $bureau)
    {
        return view('pcs.bureaux.edit', compact('bureau'));
    }

    /**
     * Mise à jour
     */
    public function update(Request $request, BureauDouane $bureau)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:bureaux_douanes,code,' . $bureau->id,
            'libelle' => 'required|string|max:255',
            'actif' => 'boolean',
        ]);

        $bureau->update([
            'code' => $validated['code'],
            'libelle' => $validated['libelle'],
            'actif' => $request->has('actif'),
        ]);

        Alert::success('Succès', 'Bureau de douane modifié avec succès');
        return redirect()->route('pcs.bureaux.index');
    }

    /**
     * Suppression
     */
    public function destroy(BureauDouane $bureau)
    {
        try {
            $bureau->delete();
            Alert::success('Succès', 'Bureau de douane supprimé avec succès');
        } catch (\Exception $e) {
            Alert::error('Erreur', 'Impossible de supprimer ce bureau (déclarations existantes)');
        }

        return redirect()->route('pcs.bureaux.index');
    }

    /**
     * Toggle actif/inactif
     */
    public function toggleActif(BureauDouane $bureau)
    {
        $bureau->actif = !$bureau->actif;
        $bureau->save();

        $statut = $bureau->actif ? 'activé' : 'désactivé';
        Alert::success('Succès', "Bureau {$statut} avec succès");

        return redirect()->back();
    }
}

