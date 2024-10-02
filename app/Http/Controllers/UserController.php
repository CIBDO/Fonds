<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Liste des utilisateurs
    public function index(Request $request)
    {
        $query = User::query();

        // Filtre par nom
        if ($request->has('name') && $request->input('name') !== '') {
            $query->where('name', 'LIKE', '%' . $request->input('name') . '%');
        }

        // Filtre par email
        if ($request->has('email') && $request->input('email') !== '') {
            $query->where('email', 'LIKE', '%' . $request->input('email') . '%');
        }

        $users = $query->paginate(10);
        return view('users.index', compact('users'));
    }

    // Formulaire de création d'utilisateur
    public function create()
    {
        return view('users.create');
    }

    // Enregistrer un nouvel utilisateur
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4|confirmed',
            'role' => 'required|string|in:tresorier,acct,direction,superviseur,admin',
        ]);

        // Crée un nouvel utilisateur
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'active' => 1, // Par défaut, l'utilisateur est actif
        ]);

        // Assigne le rôle à l'utilisateur
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
    }

    // Formulaire d'édition d'utilisateur
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // Mettre à jour un utilisateur
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:tresorier,acct,direction,superviseur,admin',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Met à jour le rôle de l'utilisateur
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Supprimer un utilisateur
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        $user->active = false; // Désactive l'utilisateur
        $user->save();
    
        return response()->json(['success' => 'Utilisateur désactivé']);
    }
    
    public function activate($id)
    {
        $user = User::findOrFail($id);
        $user->active = true; // Active l'utilisateur
        $user->save();
    
        return response()->json(['success' => 'Utilisateur activé']);
    }

    public function showUsers()
    {
        $users = User::all();
        return view('users', compact('users'));
    }
}
