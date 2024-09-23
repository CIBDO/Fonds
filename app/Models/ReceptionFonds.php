<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceptionFonds extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id', 
        'montant_recu', 
        'date_reception', 
        'observations'
    ];

    // Relation avec la demande de fonds
    public function demande()
    {
        return $this->belongsTo(DemandeFonds::class, 'demande_id');
    }
}
