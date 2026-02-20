<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CotisationTrie extends Model
{
    use HasFactory;

    protected $table = 'cotisations_trie';

    protected $fillable = [
        'poste_id',
        'bureau_trie_id',
        'mois',
        'annee',
        'montant_cotisation_courante',
        'montant_apurement',
        'montant_total',
        'mode_paiement',
        'reference_paiement',
        'preuve_paiement',
        'date_paiement',
        'detail_apurement',
        'observation',
        'statut',
        'date_saisie',
        'date_validation',
        'saisi_par',
        'valide_par',
    ];

    protected $casts = [
        'montant_cotisation_courante' => 'decimal:2',
        'montant_apurement' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'date_paiement' => 'date',
        'date_saisie' => 'datetime',
        'date_soumission' => 'datetime',
        'date_validation' => 'datetime',
    ];

    /**
     * Événement de création : calculer le montant total
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($cotisation) {
            $cotisation->montant_total = 
                $cotisation->montant_cotisation_courante + $cotisation->montant_apurement;
        });
    }

    /**
     * Relation avec le poste
     */
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    /**
     * Relation avec le bureau TRIE
     */
    public function bureauTrie()
    {
        return $this->belongsTo(BureauTrie::class, 'bureau_trie_id');
    }

    /**
     * Relation avec l'utilisateur qui a saisi
     */
    public function saisiPar()
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }

    /**
     * Relation avec l'utilisateur qui a validé
     */
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    /**
     * Accesseur : Nom du mois en français
     */
    public function getNomMoisAttribute()
    {
        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        return $mois[$this->mois] ?? '';
    }

    /**
     * Accesseur : Période complète
     */
    public function getPeriodeCompleteAttribute()
    {
        return $this->nom_mois . ' ' . $this->annee;
    }

    /**
     * Accesseur : Vérifier si un apurement existe
     */
    public function getAApurementAttribute()
    {
        return $this->montant_apurement > 0;
    }

    /**
     * Scope : Par période
     */
    public function scopePeriode($query, $mois, $annee)
    {
        return $query->where('mois', $mois)->where('annee', $annee);
    }

    /**
     * Scope : Par année
     */
    public function scopeAnnee($query, $annee)
    {
        return $query->where('annee', $annee);
    }

    /**
     * Scope : Par poste
     */
    public function scopeParPoste($query, $posteId)
    {
        return $query->where('poste_id', $posteId);
    }

    /**
     * Scope : Cotisations validées
     */
    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Méthode statique : Récupérer les cotisations par poste pour une période
     */
    public static function getCotisationsParPoste($mois, $annee)
    {
        return self::with(['poste', 'bureauTrie'])
            ->where('mois', $mois)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->get()
            ->groupBy('poste_id');
    }

    /**
     * Méthode statique : Récupérer le total des cotisations pour un poste sur une année
     */
    public static function getTotalParPosteAnnee($posteId, $annee)
    {
        return self::where('poste_id', $posteId)
            ->where('annee', $annee)
            ->where('statut', 'valide')
            ->sum('montant_total');
    }
}
