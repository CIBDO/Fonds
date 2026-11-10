<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PosteController extends Controller
{
    private function authorizeRole(array $roles)
    {
        if (!in_array(Auth::user()->role, $roles)) {
            abort(403, 'Accès refusé. Vous n\'avez pas les permissions nécessaires.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeRole(['admin']);

        $nom = $request->input('nom');
        $type = $request->input('type');

        $query = Poste::with('parent')->orderBy('type')->orderBy('nom');

        if ($nom) {
            $query->where('nom', 'like', '%'.$nom.'%');
        }
        if (in_array($type, [Poste::TYPE_SUPERIEUR, Poste::TYPE_RP], true)) {
            $query->where('type', $type);
        }

        $postes = $query->paginate(20)->appends($request->only(['nom', 'type']));
        $postesSuperieurs = Poste::where('type', Poste::TYPE_SUPERIEUR)->orderBy('nom')->get();

        return view('postes.index', compact('postes', 'nom', 'type', 'postesSuperieurs'));
    }

    public function create()
    {
        $this->authorizeRole(['admin']);

        return view('postes.add');
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['admin']);

        $validated = $this->validerPoste($request);

        Poste::create($validated);

        alert()->success('Succès', 'Poste créé avec succès.');

        return redirect()->route('postes.index');
    }

    public function show(Poste $poste)
    {
        return view('postes.show', compact('poste'));
    }

    public function edit(Poste $poste)
    {
        $this->authorizeRole(['admin']);

        return view('postes.edit', compact('poste'));
    }

    public function update(Request $request, Poste $poste)
    {
        $this->authorizeRole(['admin']);

        $validated = $this->validerPoste($request, $poste);
        $poste->update($validated);

        alert()->success('Succès', 'Poste mis à jour avec succès.');

        return redirect()->route('postes.index');
    }

    public function destroy(Poste $poste)
    {
        $this->authorizeRole(['admin']);

        if ($poste->recettesPerceptions()->exists()) {
            alert()->error('Erreur', 'Impossible de supprimer un poste qui a des recettes-perceptions rattachées.');

            return redirect()->route('postes.index');
        }

        $poste->delete();

        alert()->success('Succès', 'Poste supprimé avec succès.');

        return redirect()->route('postes.index');
    }

    private function validerPoste(Request $request, ?Poste $poste = null): array
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type' => ['required', Rule::in([Poste::TYPE_SUPERIEUR, Poste::TYPE_RP])],
            'parent_id' => [
                Rule::requiredIf(fn () => $request->input('type') === Poste::TYPE_RP),
                'nullable',
                'exists:postes,id',
            ],
            'intitule_officiel' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
        ]);

        if ($validated['type'] === Poste::TYPE_SUPERIEUR) {
            $validated['parent_id'] = null;
        } else {
            $parent = Poste::find($validated['parent_id'] ?? null);
            if (!$parent || !$parent->isSuperieur()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'La RP doit être rattachée à un poste supérieur.',
                ]);
            }
            if ($poste && (int) $validated['parent_id'] === (int) $poste->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Un poste ne peut pas être son propre parent.',
                ]);
            }
        }

        return $validated;
    }
}
