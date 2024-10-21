<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        $this->authorizeRole(['admin']);
        // On récupère tous les utilisateurs
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorizeRole(['admin']);
        return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorizeRole(['admin']);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:tresorier,acct,direction,superviseur,admin',
            'active' => 'required|boolean', // Ajouter un champ pour le statut actif/inactif
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => $request->active, // Ajouter l'activation
        ]);
        alert()->success('Success', 'Utilisateur créé avec succès.');
        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $this->authorizeRole(['admin']);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeRole(['admin']);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:tresorier,acct,direction,superviseur,admin',
            'active' => 'required|boolean', // Validation du statut actif/inactif
        ]);

        $user->update($request->only(['name', 'email', 'role', 'active']));

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
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
        $this->authorizeRole(['admin']);
        $user->update(['active' => false]);
        alert()->success('Success', 'Utilisateur désactivé avec succès.');
        return redirect()->route('users.index');
    }

    // Méthode pour activer un utilisateur
    public function activate(User $user)
    {
        $this->authorizeRole(['admin']);
        $user->update(['active' => true]);
        alert()->success('Success', 'Utilisateur activé avec succès.');
        return redirect()->route('users.index');
    }
}
