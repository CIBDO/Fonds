<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use App\Services\PosteRpImportService;
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

        $query = Poste::with('parent')
            // Regrouper : poste supérieur puis ses RP
            ->orderByRaw('COALESCE(parent_id, id)')
            ->orderByRaw('CASE WHEN parent_id IS NULL THEN 0 ELSE 1 END')
            ->orderBy('nom');

        if ($nom) {
            $term = '%'.$nom.'%';
            // Inclure le poste trouvé + toutes les entités liées (RP enfants / parent)
            $idsCorrespondants = Poste::query()
                ->where(function ($q) use ($term) {
                    $q->where('nom', 'like', $term)
                        ->orWhere('intitule_officiel', 'like', $term)
                        ->orWhere('region', 'like', $term);
                })
                ->pluck('id');

            $query->where(function ($q) use ($term, $idsCorrespondants) {
                $q->where('nom', 'like', $term)
                    ->orWhere('intitule_officiel', 'like', $term)
                    ->orWhere('region', 'like', $term)
                    ->orWhereHas('parent', function ($parent) use ($term) {
                        $parent->where('nom', 'like', $term)
                            ->orWhere('intitule_officiel', 'like', $term)
                            ->orWhere('region', 'like', $term);
                    });

                if ($idsCorrespondants->isNotEmpty()) {
                    $q->orWhereIn('parent_id', $idsCorrespondants)
                        ->orWhereIn('id', function ($sub) use ($idsCorrespondants) {
                            $sub->select('parent_id')
                                ->from('postes')
                                ->whereIn('id', $idsCorrespondants)
                                ->whereNotNull('parent_id');
                        });
                }
            });
        }
        if (in_array($type, [Poste::TYPE_SUPERIEUR, Poste::TYPE_RP], true)) {
            $query->where('type', $type);
        }

        // Recherche : afficher toute la famille liée (pas seulement 20 lignes)
        $postes = $query->paginate($nom ? 500 : 20)->appends($request->only(['nom', 'type']));
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

    public function templateImportRp()
    {
        $this->authorizeRole(['admin']);

        return PosteRpImportService::templateResponse();
    }

    public function importRp(Request $request)
    {
        $this->authorizeRole(['admin']);

        $request->validate([
            'fichier' => 'required|file|max:5120',
            'parent_id' => 'nullable|exists:postes,id',
        ]);

        $extension = strtolower($request->file('fichier')->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
            throw ValidationException::withMessages([
                'fichier' => 'Le fichier doit être au format Excel (.xlsx, .xls) ou CSV.',
            ]);
        }

        $parentDefaut = null;
        if ($request->filled('parent_id')) {
            $parentDefaut = Poste::find($request->integer('parent_id'));
            if (!$parentDefaut || !$parentDefaut->isSuperieur()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Le poste par défaut doit être un poste supérieur.',
                ]);
            }
        }

        $resultat = PosteRpImportService::importer(
            $request->file('fichier')->getRealPath(),
            $extension,
            $parentDefaut
        );

        $message = sprintf(
            '%d RP créée(s), %d mise(s) à jour, %d déjà en place.',
            $resultat['creees'],
            $resultat['mises_a_jour'],
            $resultat['inchangees']
        );

        if ($resultat['erreurs'] !== []) {
            session()->flash('import_rp_erreurs', $resultat['erreurs']);
        }

        if ($resultat['creees'] === 0 && $resultat['mises_a_jour'] === 0 && $resultat['erreurs'] !== []) {
            alert()->error('Import impossible', $message);

            return redirect()->route('postes.index');
        }

        if ($resultat['erreurs'] !== []) {
            alert()->warning('Import partiel', $message.' '.count($resultat['erreurs']).' ligne(s) en erreur.');
        } else {
            alert()->success('Import terminé', $message);
        }

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
