<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    use HasFactory;

    protected $table = 'postes'; // Nom de la table

    protected $fillable = [
        'nom', // Nom du poste
        'created_at',
        'updated_at',
    ];

    // Si vous souhaitez définir une relation avec d'autres modèles, vous pouvez le faire ici
    public function demandesFonds()
    {
        return $this->hasMany(DemandeFonds::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
