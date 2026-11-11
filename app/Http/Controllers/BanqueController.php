<?php

namespace App\Http\Controllers;

use App\Models\Banque;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class BanqueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function authorizeAdmin(): void
    {
        if (!Auth::user()?->hasRole('admin')) {
            abort(403, 'Accès refusé. Seul un administrateur peut gérer les banques.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = Banque::query()->orderBy('nom');

        if ($request->filled('q')) {
            $term = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($term) {
                $q->where('code', 'like', $term)->orWhere('nom', 'like', $term);
            });
        }

        if ($request->filled('actif') && in_array($request->input('actif'), ['0', '1'], true)) {
            $query->where('actif', (bool) $request->input('actif'));
        }

        $banques = $query->paginate(25)->appends($request->query());

        return view('virements.banques.index', compact('banques'));
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('virements.banques.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:banques,code'],
            'nom' => ['required', 'string', 'max:255'],
            'actif' => ['sometimes', 'boolean'],
        ], [
            'code.required' => 'Le code est obligatoire.',
            'code.unique' => 'Ce code banque existe déjà.',
            'nom.required' => 'Le nom est obligatoire.',
        ]);

        Banque::create([
            'code' => strtoupper(trim($validated['code'])),
            'nom' => trim($validated['nom']),
            'actif' => $request->boolean('actif', true),
        ]);

        Alert::success('Succès', 'Banque créée avec succès.');

        return redirect()->route('banques.index');
    }

    public function edit(Banque $banque)
    {
        $this->authorizeAdmin();

        return view('virements.banques.edit', compact('banque'));
    }

    public function update(Request $request, Banque $banque)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('banques', 'code')->ignore($banque->id),
            ],
            'nom' => ['required', 'string', 'max:255'],
            'actif' => ['sometimes', 'boolean'],
        ], [
            'code.required' => 'Le code est obligatoire.',
            'code.unique' => 'Ce code banque existe déjà.',
            'nom.required' => 'Le nom est obligatoire.',
        ]);

        $banque->update([
            'code' => strtoupper(trim($validated['code'])),
            'nom' => trim($validated['nom']),
            'actif' => $request->boolean('actif'),
        ]);

        Alert::success('Succès', 'Banque mise à jour avec succès.');

        return redirect()->route('banques.index');
    }

    public function destroy(Banque $banque)
    {
        $this->authorizeAdmin();

        if ($banque->virements()->exists()) {
            Alert::error('Erreur', 'Impossible de supprimer une banque déjà utilisée dans des VIR.');

            return redirect()->route('banques.index');
        }

        $banque->delete();

        Alert::success('Succès', 'Banque supprimée avec succès.');

        return redirect()->route('banques.index');
    }

    public function toggleActif(Banque $banque)
    {
        $this->authorizeAdmin();

        $banque->update(['actif' => !$banque->actif]);

        Alert::success(
            'Succès',
            $banque->actif ? 'Banque activée.' : 'Banque désactivée.'
        );

        return redirect()->route('banques.index');
    }
}
