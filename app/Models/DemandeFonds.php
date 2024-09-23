<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeFonds extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'mois', 
        'annee', 
        'total_demande', 
        'status'
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec les lignes de demande
    public function lignes()
    {
        return $this->hasMany(LigneDemande::class, 'demande_id');
    }

    // Relation avec les réceptions de fonds
    public function receptions()
    {
        return $this->hasMany(ReceptionFonds::class, 'demande_id');
    }
}
