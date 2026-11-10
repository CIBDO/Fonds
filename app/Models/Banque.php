<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Banque extends Model
{
    protected $fillable = [
        'code',
        'nom',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    public function virements(): HasMany
    {
        return $this->hasMany(Virement::class);
    }

    public function scopeActives($query)
    {
        return $query->where('actif', true);
    }
}
