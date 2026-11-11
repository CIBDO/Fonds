<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestinataireMailAlerte extends Model
{
    protected $table = 'destinataires_mail_alertes';

    public const EVENEMENTS = [
        'fonds_soumise' => 'Demande de fonds — soumission',
        'fonds_statut' => 'Demande de fonds — changement de statut',
        'autre_soumise' => 'Autre demande — soumission',
        'autre_validee' => 'Autre demande — validation',
        'autre_rejetee' => 'Autre demande — rejet',
        'vir_soumission' => 'VIR — soumission bordereau',
        'vir_paiement' => 'VIR — marquage payé',
        'fnl_soumise' => 'FNL — soumission paiement',
    ];

    protected $fillable = [
        'email',
        'nom',
        'actif',
        'fonds_soumise',
        'fonds_statut',
        'autre_soumise',
        'autre_validee',
        'autre_rejetee',
        'vir_soumission',
        'vir_paiement',
        'fnl_soumise',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'fonds_soumise' => 'boolean',
        'fonds_statut' => 'boolean',
        'autre_soumise' => 'boolean',
        'autre_validee' => 'boolean',
        'autre_rejetee' => 'boolean',
        'vir_soumission' => 'boolean',
        'vir_paiement' => 'boolean',
        'fnl_soumise' => 'boolean',
    ];

    public function scopeActifs($query)
    {
        return $query->where('actif', true);
    }

    public function scopePourEvenement($query, string $evenement)
    {
        if (! array_key_exists($evenement, self::EVENEMENTS)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->actifs()->where($evenement, true);
    }
}
