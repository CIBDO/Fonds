<?php

namespace App\Http\Controllers\TRIE;

use App\Http\Controllers\Controller;
use App\Models\BureauTrie;
use App\Models\Poste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class BureauTrieController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Liste des bureaux TRIE
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Filtrer les postes selon le profil de l'utilisateur
        if (in_array($user->role, ['admin', 'acct'])) {
            // Admin/ACCT peuvent voir tous les postes
            $postes = Poste::orderBy('nom')->get();
        } else {
            // Les autres utilisateurs ne voient que leur propre poste
            if (!$user->poste_id) {
                // Utilisateur sans poste associé - on envoie une collection vide
                $postes = collect();
                Alert::warning('Attention', 'Vous n\'êtes pas associé à un poste. Veuillez contacter l\'administrateur.');
            } else {
                $postes = Poste::where('id', $user->poste_id)->get();
            }
        }
        
        return view('trie.bureaux.index', compact('postes'));
    }

    /**
     * Gérer les bureaux d'un poste spécifique
     */
    public function manage($posteId)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a le droit d'accéder à ce poste
        if (!in_array($user->role, ['admin', 'acct'])) {
            if ($user->poste_id != $posteId) {
                Alert::error('Erreur', 'Vous n\'avez pas l\'autorisation d\'accéder aux bureaux de ce poste.');
                return redirect()->route('trie.bureaux.index');
            }
        }
        
        $poste = Poste::findOrFail($posteId);
        $bureaux = BureauTrie::where('poste_id', $posteId)
            ->orderBy('code_bureau')
            ->get();
        
        return view('trie.bureaux.manage', compact('poste', 'bureaux'));
    }

    /**
     * Stocker un nouveau bureau
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'poste_id' => 'required|exists:postes,id',
            'code_bureau' => 'required|unique:bureaux_trie,code_bureau|max:50',
            'nom_bureau' => 'required|max:255',
            'description' => 'nullable|string',
            'actif' => 'boolean',
        ], [
            'poste_id.required' => 'Le poste est obligatoire.',
            'code_bureau.required' => 'Le code du bureau est obligatoire.',
            'code_bureau.unique' => 'Ce code de bureau existe déjà.',
            'nom_bureau.required' => 'Le nom du bureau est obligatoire.',
        ]);

        // Vérifier que l'utilisateur peut créer des bureaux pour ce poste
        if (!in_array($user->role, ['admin', 'acct'])) {
            if ($user->poste_id != $request->poste_id) {
                Alert::error('Erreur', 'Vous ne pouvez créer des bureaux que pour votre propre poste.');
                return redirect()->back();
            }
        }

        BureauTrie::create($validated);

        Alert::success('Succès', 'Le bureau TRIE a été créé avec succès.');
        
        return redirect()->route('trie.bureaux.manage', $request->poste_id);
    }

    /**
     * Mettre à jour un bureau
     */
    public function update(Request $request, BureauTrie $bureau)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur peut modifier ce bureau
        if (!in_array($user->role, ['admin', 'acct'])) {
            if ($user->poste_id != $bureau->poste_id) {
                Alert::error('Erreur', 'Vous ne pouvez modifier que les bureaux de votre propre poste.');
                return redirect()->back();
            }
        }
        
        $validated = $request->validate([
            'code_bureau' => 'required|max:50|unique:bureaux_trie,code_bureau,' . $bureau->id,
            'nom_bureau' => 'required|max:255',
            'description' => 'nullable|string',
            'actif' => 'boolean',
        ], [
            'code_bureau.required' => 'Le code du bureau est obligatoire.',
            'code_bureau.unique' => 'Ce code de bureau existe déjà.',
            'nom_bureau.required' => 'Le nom du bureau est obligatoire.',
        ]);

        $bureau->update($validated);

        Alert::success('Succès', 'Le bureau TRIE a été modifié avec succès.');
        
        return redirect()->route('trie.bureaux.manage', $bureau->poste_id);
    }

    /**
     * Activer/Désactiver un bureau
     */
    public function toggleStatus(BureauTrie $bureau)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur peut modifier ce bureau
        if (!in_array($user->role, ['admin', 'acct'])) {
            if ($user->poste_id != $bureau->poste_id) {
                Alert::error('Erreur', 'Vous ne pouvez modifier que les bureaux de votre propre poste.');
                return redirect()->back();
            }
        }
        
        $bureau->actif = !$bureau->actif;
        $bureau->save();

        $statut = $bureau->actif ? 'activé' : 'désactivé';
        Alert::success('Succès', "Le bureau a été $statut avec succès.");
        
        return redirect()->back();
    }

    /**
     * Supprimer un bureau (soft delete ou vérifier s'il a des cotisations)
     */
    public function destroy(BureauTrie $bureau)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur peut supprimer ce bureau
        if (!in_array($user->role, ['admin', 'acct'])) {
            if ($user->poste_id != $bureau->poste_id) {
                Alert::error('Erreur', 'Vous ne pouvez supprimer que les bureaux de votre propre poste.');
                return redirect()->back();
            }
        }
        
        // Vérifier s'il y a des cotisations associées
        if ($bureau->cotisations()->count() > 0) {
            Alert::error('Erreur', 'Impossible de supprimer ce bureau car il a des cotisations enregistrées. Désactivez-le plutôt.');
            return redirect()->back();
        }

        $posteId = $bureau->poste_id;
        $bureau->delete();

        Alert::success('Succès', 'Le bureau a été supprimé avec succès.');
        
        return redirect()->route('trie.bureaux.manage', $posteId);
    }

    /**
     * API pour récupérer les bureaux d'un poste (pour select dynamique)
     */
    public function getBureaux($posteId)
    {
        $bureaux = BureauTrie::where('poste_id', $posteId)
            ->where('actif', true)
            ->orderBy('nom_bureau')
            ->get(['id', 'code_bureau', 'nom_bureau']);

        return response()->json($bureaux);
    }
}
