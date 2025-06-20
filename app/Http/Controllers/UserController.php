<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Poste;

class UserController extends Controller
{
    private function authorizeRole(array $roles)
{
    if (!in_array(Auth::user()->role, $roles)) {
        abort(403, '🚫 Accès refusé ! Vous n\'avez pas les permissions nécessaires pour accéder à cette page. Si vous pensez qu\'il s\'agit d\'une erreur, veuillez contacter votre administrateur.');
    }
}

    public function index()
    {
        $this->authorizeRole(['admin','tresorier']);
        $name = request('name');
        $email = request('email');
        $nom = request('nom');
        // On récupère tous les utilisateurs
        $users = User::where(function ($query) use ($name, $email, $nom) {
            if ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            }
            if ($email) {
                $query->where('email', 'like', '%' . $email . '%');
            }
            if ($nom) {
                $query->whereHas('poste', function ($query) use ($nom) {
                    $query->where('nom', 'like', '%' . $nom . '%');
                });
            }

        })->paginate(10);
        $postes = Poste::all();
        return view('users.index', compact('users', 'postes', 'name', 'email', 'nom'));
    }


    public function create()
    {
        $this->authorizeRole(['admin']);
        $postes = Poste::all();
        return view('users.create', compact('postes'));
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['admin','tresorier']);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:tresorier,acct,direction,superviseur,admin',
            'active' => 'required|boolean',
            'poste_id' => 'required|exists:postes,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
                'active' => $request->active, // Ajouter l'activation
            'poste_id' => $request->poste_id,
        ]);
        alert()->success('Success', 'Utilisateur créé avec succès.');
        return redirect()->route('users.index');
    }

    /* public function edit(User $user)
    {
        $this->authorizeRole(['admin']);
        $postes = Poste::all();
        return view('users.edit', compact('user', 'postes'));
    }
 */

    public function edit(User $user)
    {
        $this->authorizeRole(['tresorier', 'admin']);
        if (Auth::user()->id !== $user->id && Auth::user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Vous n\'êtes pas autorisé à modifier ces informations.');
        }

        $postes = Poste::all();
        return view('users.edit', compact('user', 'postes'));
    }

    /* public function update(Request $request, User $user)
    {
        $this->authorizeRole(['admin']);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:tresorier,acct,direction,superviseur,admin',
            'active' => 'required|boolean', // Validation du statut actif/inactif
            'poste_id' => 'required|exists:postes,id',
        ]);

        $user->update($request->only(['name', 'email', 'role', 'active', 'poste_id']));

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }
        alert()->success('Success', 'Utilisateur mis à jour avec succès.');
        return redirect()->route('users.index');
    } */
    /* public function update(Request $request, User $user)
    {
        $this->authorizeRole(['tresorier', 'admin']);
        // Vérification d'autorisation
        if (Auth::user()->id !== $user->id && Auth::user()->role !== 'admin') {
            return redirect()->route('users.index')->with('error', 'Vous n\'êtes pas autorisé à modifier ces informations.');
        }

        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:4|confirmed',
        ]);

        // Mise à jour des informations de l'utilisateur
        $user->name = $request->input('name');
        $user->email = $request->input('email');

        // Mise à jour du mot de passe s'il est fourni
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Informations mises à jour avec succès.');
    }
 */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Exclure le password des données de mise à jour principales
        unset($validatedData['password']);

        // Si l'utilisateur connecté est un trésorier
        if (Auth::user()->role === 'tresorier') {
            // Forcer les valeurs existantes pour les champs qu'il ne peut pas modifier
            $validatedData['role'] = $user->role;
            $validatedData['active'] = $user->active;
            $validatedData['poste_id'] = $user->poste_id;
        } else {
            // Valider les champs supplémentaires uniquement pour l'admin
            $additionalValidation = $request->validate([
                'role' => 'required|in:admin,tresorier,acct,superviseur',
                'active' => 'required|boolean',
                'poste_id' => 'required|exists:postes,id',
            ]);

            // Ajouter les champs validés aux données de mise à jour
            $validatedData = array_merge($validatedData, $additionalValidation);
        }

        // Mise à jour de l'utilisateur (sans le password)
        $user->update($validatedData);

        // Mettre à jour le mot de passe seulement s'il est fourni et non vide
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        alert()->success('Success', 'Utilisateur mis à jour avec succès.');
        return redirect()->route('users.index');
    }


    public function destroy(User $user)
    {
        $this->authorizeRole(['admin']);
        $user->delete();
        alert()->success('Success', 'Utilisateur supprimé avec succès.');
        return redirect()->route('users.index');
    }

        // Méthode pour désactiver un utilisateur
    public function deactivate(User $user)
    {
        try {
            $this->authorizeRole(['admin']);
            $user->update(['active' => false]);

            // Vérifier si c'est une requête AJAX
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur désactivé avec succès.'
                ]);
            }

            alert()->success('Success', 'Utilisateur désactivé avec succès.');
            return redirect()->route('users.index');

        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la désactivation de l\'utilisateur.'
                ], 500);
            }

            alert()->error('Erreur', 'Erreur lors de la désactivation de l\'utilisateur.');
            return redirect()->route('users.index');
        }
    }

    // Méthode pour activer un utilisateur
    public function activate(User $user)
    {
        try {
            $this->authorizeRole(['admin']);
            $user->update(['active' => true]);

            // Vérifier si c'est une requête AJAX
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur activé avec succès.'
                ]);
            }

            alert()->success('Success', 'Utilisateur activé avec succès.');
            return redirect()->route('users.index');

        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'activation de l\'utilisateur.'
                ], 500);
            }

            alert()->error('Erreur', 'Erreur lors de l\'activation de l\'utilisateur.');
            return redirect()->route('users.index');
        }
    }
}
