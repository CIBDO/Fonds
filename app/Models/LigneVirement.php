<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneVirement extends Model
{
    protected $table = 'lignes_virements';

    protected $fillable = [
        'virement_id',
        'num_be',
        'num_mdt',
        'ord',
        'programme',
        'creancier_libelle',
        'num_compte',
        'montant',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
    ];

    public function virement(): BelongsTo
    {
        return $this->belongsTo(Virement::class);
    }
}
