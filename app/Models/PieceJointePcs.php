<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PieceJointePcs extends Model
{
    use HasFactory;

    protected $table = 'pieces_jointes_pcs';

    protected $fillable = [
        'declaration_pcs_id',
        'nom_fichier',
        'nom_original',
        'chemin_fichier',
        'type_mime',
        'taille',
        'uploaded_by',
    ];

    /**
     * Relation avec la déclaration PCS
     */
    public function declarationPcs()
    {
        return $this->belongsTo(DeclarationPcs::class, 'declaration_pcs_id');
    }

    /**
     * Relation avec l'utilisateur qui a uploadé
     */
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Accesseur : URL du fichier
     */
    public function getUrlAttribute()
    {
        return Storage::url($this->chemin_fichier);
    }

    /**
     * Accesseur : Taille formatée
     */
    public function getTailleFormateeAttribute()
    {
        $bytes = $this->taille;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' octets';
    }

    /**
     * Méthode : Supprimer le fichier du storage
     */
    public function supprimerFichier()
    {
        if (Storage::exists($this->chemin_fichier)) {
            Storage::delete($this->chemin_fichier);
        }
    }

    /**
     * Hook : Supprimer le fichier à la suppression du modèle
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($pieceJointe) {
            $pieceJointe->supprimerFichier();
        });
    }
}


