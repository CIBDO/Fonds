<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BureauTrie extends Model
{
    use HasFactory;

    protected $table = 'bureaux_trie';

    protected $fillable = [
        'poste_id',
        'code_bureau',
        'nom_bureau',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Relation avec le poste
     */
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    /**
     * Relation avec les cotisations TRIE
     */
    public function cotisations()
    {
        return $this->hasMany(CotisationTrie::class, 'bureau_trie_id');
    }

    /**
     * Scope pour les bureaux actifs
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope pour filtrer par poste
     */
    public function scopeParPoste($query, $posteId)
    {
        return $query->where('poste_id', $posteId);
    }

    /**
     * Récupérer les cotisations pour une période donnée
     */
    public function getCotisationsPeriode($mois, $annee)
    {
        return $this->cotisations()
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->first();
    }

    /**
     * Récupérer les cotisations validées pour une année
     */
    public function getCotisationsAnnee($annee)
    {
        return $this->cotisations()
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get();
    }

    /**
     * Calculer le total des cotisations pour une année
     */
    public function getTotalCotisationsAnnee($annee)
    {
        return $this->cotisations()
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->sum('montant_total');
    }

    /**
     * Accesseur : Nom complet du bureau
     */
    public function getNomCompletAttribute()
    {
        return $this->code_bureau . ' - ' . $this->nom_bureau;
    }
}
