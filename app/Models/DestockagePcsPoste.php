<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestockagePcsPoste extends Model
{
    use HasFactory;

    protected $table = 'destockages_pcs_postes';

    protected $fillable = [
        'destockage_pcs_id',
        'poste_id',
        'bureau_douane_id',
        'montant_collecte',
        'montant_destocke',
        'solde_avant',
        'solde_apres',
    ];

    protected $casts = [
        'montant_collecte' => 'decimal:2',
        'montant_destocke' => 'decimal:2',
        'solde_avant' => 'decimal:2',
        'solde_apres' => 'decimal:2',
    ];

    /**
     * Relation avec le déstockage
     */
    public function destockage()
    {
        return $this->belongsTo(DestockagePcs::class, 'destockage_pcs_id');
    }

    /**
     * Relation avec le poste
     */
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    /**
     * Relation avec le bureau de douane
     */
    public function bureauDouane()
    {
        return $this->belongsTo(BureauDouane::class, 'bureau_douane_id');
    }

    /**
     * Accesseur : Nom de l'entité (Poste ou Bureau)
     */
    public function getNomEntiteAttribute()
    {
        if ($this->poste_id) {
            return $this->poste->nom ?? 'N/A';
        }
        if ($this->bureau_douane_id) {
            return $this->bureauDouane->libelle ?? 'N/A';
        }
        return 'N/A';
    }
}
