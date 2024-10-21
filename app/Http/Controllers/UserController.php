<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // On récupère tous les utilisateurs
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
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

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
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

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    // Méthode pour désactiver un utilisateur
    public function deactivate(User $user)
    {
        $user->update(['active' => false]);
        return redirect()->route('users.index')->with('success', 'Utilisateur désactivé avec succès.');
    }

    // Méthode pour activer un utilisateur
    public function activate(User $user)
    {
        $user->update(['active' => true]);
        return redirect()->route('users.index')->with('success', 'Utilisateur activé avec succès.');
    }
}
