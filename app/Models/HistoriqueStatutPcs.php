<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueStatutPcs extends Model
{
    use HasFactory;

    protected $table = 'historique_statuts_pcs';

    public $timestamps = false; // Utilise seulement created_at

    protected $fillable = [
        'declaration_pcs_id',
        'ancien_statut',
        'nouveau_statut',
        'utilisateur_id',
        'commentaire',
        'date_changement',
    ];

    protected $casts = [
        'date_changement' => 'datetime',
    ];

    /**
     * Relation avec la déclaration PCS
     */
    public function declarationPcs()
    {
        return $this->belongsTo(DeclarationPcs::class, 'declaration_pcs_id');
    }

    /**
     * Relation avec l'utilisateur
     */
    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }
}


