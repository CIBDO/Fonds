<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BureauDouane extends Model
{
    use HasFactory;

    protected $table = 'bureaux_douanes';

    protected $fillable = [
        'poste_rgd_id',
        'code',
        'libelle',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec le poste RGD
     */
    public function posteRgd()
    {
        return $this->belongsTo(Poste::class, 'poste_rgd_id');
    }

    /**
     * Relation avec les déclarations PCS
     */
    public function declarationsPcs()
    {
        return $this->hasMany(DeclarationPcs::class, 'bureau_douane_id');
    }

    /**
     * Scope pour les bureaux actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Récupérer les déclarations pour un mois et une année donnés
     */
    public function getDeclarationsMois($mois, $annee, $programme = null)
    {
        $query = $this->declarationsPcs()
            ->where('mois', $mois)
            ->where('annee', $annee);

        if ($programme) {
            $query->where('programme', $programme);
        }

        return $query->get();
    }
}


