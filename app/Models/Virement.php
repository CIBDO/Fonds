<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Virement extends Model
{
    protected $fillable = [
        'bordereau_virement_id',
        'poste_id',
        'banque_id',
        'numero_vir',
        'mois',
        'annee',
        'montant',
        'statut_paiement',
        'date_paiement',
        'paye_par',
        'observation',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'datetime',
        'annee' => 'integer',
    ];

    public function bordereau(): BelongsTo
    {
        return $this->belongsTo(BordereauVirement::class, 'bordereau_virement_id');
    }

    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }

    public function banque(): BelongsTo
    {
        return $this->belongsTo(Banque::class);
    }

    public function payePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paye_par');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneVirement::class);
    }

    public function isPaye(): bool
    {
        return $this->statut_paiement === 'paye';
    }

    public function sommeLignes(): float
    {
        return (float) $this->lignes()->sum('montant');
    }
}
