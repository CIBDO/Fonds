<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutreDemandeEchelon extends Model
{
    use HasFactory;

    protected $table = 'autre_demande_echelons';

    protected $fillable = [
        'autre_demande_id',
        'ordre',
        'date_echeance',
        'montant',
    ];

    protected $casts = [
        'date_echeance' => 'date',
        'montant' => 'decimal:2',
    ];

    public function demande()
    {
        return $this->belongsTo(AutreDemande::class, 'autre_demande_id');
    }
}
