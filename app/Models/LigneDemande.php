<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id', 
        'libelle', 
        'salaire_net', 
        'revers_salaire', 
        'total_mois_courant', 
        'salaire_mois_anterieur'
    ];

    // Relation avec la demande de fonds
    public function demande()
    {
        return $this->belongsTo(DemandeFonds::class, 'demande_id');
    }
}
