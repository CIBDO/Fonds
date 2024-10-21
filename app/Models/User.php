<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'active', // Ajout de la gestion du statut actif
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Vérifier si l'utilisateur est admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Vérifier si l'utilisateur est superviseur
    public function isSuperviseur()
    {
        return $this->role === 'superviseur';
    }

    // Vérifier si l'utilisateur est actif
    public function isActive()
    {
        return $this->active;
    }

    // Vérifier si l'utilisateur a un rôle spécifique
    public function hasRole($role)
    {
        return $this->role === $role;
    }
   
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }
}
