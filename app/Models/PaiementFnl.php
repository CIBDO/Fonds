<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaiementFnl extends Model
{
    use HasFactory;

    protected $table = 'paiements_fnl';

    protected $fillable = [
        'poste_id',
        'mois',
        'annee',
        'retenue_fnl',
        'reference_paiement',
        'preuve_paiement',
        'date_paiement',
        'observation',
        'statut',
        'motif_rejet',
        'date_saisie',
        'date_validation',
        'saisi_par',
        'valide_par',
    ];

    protected $casts = [
        'retenue_fnl' => 'decimal:2',
        'date_paiement' => 'date',
        'date_saisie' => 'datetime',
        'date_validation' => 'datetime',
    ];

    public static function moisList(): array
    {
        return [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];
    }

    public function getNomMoisAttribute(): string
    {
        return self::moisList()[(int) $this->mois] ?? (string) $this->mois;
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    public function saisiPar()
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
}
