<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapportPaiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reception_id', 
        'montant_paye', 
        'date_paiement', 
        'status'
    ];

    // Relation avec la réception de fonds
    public function reception()
    {
        return $this->belongsTo(ReceptionFonds::class, 'reception_id');
    }
}
