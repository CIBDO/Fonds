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
        'poste_id',
        'peut_saisir_pcs',
        'peut_valider_pcs',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'peut_saisir_pcs' => 'boolean',
        'peut_valider_pcs' => 'boolean',
    ];

    // Mutateur : normalise le rôle en minuscule, sans espaces, à la sauvegarde
    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = $value === null ? null : strtolower(trim((string) $value));
    }

    // Vérifier si l'utilisateur est admin
    public function isAdmin()
    {
        return strtolower((string) $this->role) === 'admin';
    }

    // Vérifier si l'utilisateur est superviseur
    public function isSuperviseur()
    {
        return strtolower((string) $this->role) === 'superviseur';
    }

    // Vérifier si l'utilisateur est actif
    public function isActive()
    {
        return $this->active;
    }

    // Rôle normalisé (minuscule, sans espaces)
    public function getNormalizedRoleAttribute()
    {
        return strtolower(trim((string) $this->role));
    }

    // Vérifier si l'utilisateur a un rôle spécifique
    public function hasRole($role)
    {
        return $this->normalized_role === strtolower(trim((string) $role));
    }

    public function hasAnyRole(array $roles)
    {
        $normalizedRoles = array_map(fn ($r) => strtolower(trim((string) $r)), $roles);

        return in_array($this->normalized_role, $normalizedRoles, true);
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class);
    }

    public function isTresorier()
    {
        return $this->hasRole('tresorier');
    }

    /* public function notifications()
    {
        return $this->hasMany(Notification::class);
    } */
}
