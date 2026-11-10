<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BordereauVirement extends Model
{
    protected $table = 'bordereau_virements';

    protected $fillable = [
        'demande_fonds_id',
        'poste_id',
        'user_id',
        'mois',
        'annee',
        'periode_debut',
        'periode_fin',
        'status',
        'fichier_joint',
        'soumis_at',
    ];

    protected $casts = [
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'soumis_at' => 'datetime',
        'annee' => 'integer',
    ];

    public function demandeFonds(): BelongsTo
    {
        return $this->belongsTo(DemandeFonds::class, 'demande_fonds_id');
    }

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function virements(): HasMany
    {
        return $this->hasMany(Virement::class, 'bordereau_virement_id');
    }

    public function isBrouillon(): bool
    {
        return $this->status === 'brouillon';
    }

    public function isSoumis(): bool
    {
        return $this->status === 'soumis';
    }

    public function totalMontant(): float
    {
        return (float) $this->virements()->sum('montant');
    }
}
