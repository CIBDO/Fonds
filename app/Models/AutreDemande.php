<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutreDemande extends Model
{
    use HasFactory;

    protected $table = 'autres_demandes';

    protected $fillable = [
        'poste_id',
        'designation',
        'montant',
        'montant_accord',
        'observation',
        'preuve_paiement',
        'date_demande',
        'annee',
        'statut',
        'date_validation',
        'motif_rejet',
        'saisi_par',
        'valide_par',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'montant_accord' => 'decimal:2',
        'date_demande' => 'date',
        'date_validation' => 'datetime',
    ];

    /**
     * Relation avec le poste
     */
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
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
     * Échéances de paiement (paiement échelonné)
     */
    public function echelons()
    {
        return $this->hasMany(AutreDemandeEchelon::class, 'autre_demande_id')->orderBy('ordre');
    }

    public function getMontantVerseAttribute(): float
    {
        if ($this->relationLoaded('echelons')) {
            return (float) $this->echelons->sum('montant');
        }

        return (float) $this->echelons()->sum('montant');
    }

    public function getMontantRestantAccordAttribute(): float
    {
        $plafond = $this->montant_accord !== null ? (float) $this->montant_accord : (float) $this->montant;

        return max(0, $plafond - $this->montant_verse);
    }

    public function estPartiellementValidee(): bool
    {
        return $this->statut === 'soumis' && $this->montant_verse > 0;
    }

    public function estValidationComplete(): bool
    {
        if ($this->statut === 'valide') {
            return true;
        }

        if ($this->montant_accord === null) {
            return false;
        }

        return $this->montant_verse >= (float) $this->montant_accord - 0.01;
    }

    public function peutRecevoirVersement(): bool
    {
        return $this->statut === 'soumis' && ! $this->estValidationComplete();
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
    public function scopePoste($query, $posteId)
    {
        return $query->where('poste_id', $posteId);
    }

    /**
     * Scope : Demandes validées
     */
    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Scope : Demandes soumises
     */
    public function scopeSoumis($query)
    {
        return $query->where('statut', 'soumis');
    }

    /**
     * Scope : Brouillons
     */
    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'brouillon');
    }

    /**
     * Méthode : Soumettre la demande
     */
    public function soumettre()
    {
        $this->statut = 'soumis';
        $this->save();
    }

    /**
     * Enregistre un versement (avance ou solde). Valide définitivement quand le total versé atteint le plafond.
     */
    public function ajouterVersement(int $valideurId, float $montantPlafond, float $montantVersement, string $dateVersement): void
    {
        if ($this->montant_accord === null) {
            $this->montant_accord = $montantPlafond;
        }

        $ordre = $this->echelons()->count() + 1;
        $this->echelons()->create([
            'ordre' => $ordre,
            'date_echeance' => $dateVersement,
            'montant' => $montantVersement,
        ]);

        $this->valide_par = $valideurId;

        $totalVerse = (float) $this->echelons()->sum('montant');
        if ($totalVerse >= (float) $this->montant_accord - 0.01) {
            $this->statut = 'valide';
            $this->date_validation = now();
        }

        $this->save();
    }

    /**
     * Méthode : Rejeter la demande
     */
    public function rejeter($valideurId, $motif)
    {
        $this->statut = 'rejete';
        $this->motif_rejet = $motif;
        $this->valide_par = $valideurId;
        $this->save();
    }

    /**
     * Accessor : Différence entre montant demandé et accordé
     */
    public function getDifferenceMontantAttribute()
    {
        if ($this->montant_accord !== null) {
            return $this->montant_accord - $this->montant;
        }
        return 0;
    }

    /**
     * Accessor : Pourcentage accordé par rapport au demandé
     */
    public function getPourcentageAccordeAttribute()
    {
        if ($this->montant > 0) {
            $verse = $this->montant_verse > 0 ? $this->montant_verse : (float) ($this->montant_accord ?? 0);

            return round(($verse / $this->montant) * 100, 2);
        }

        return 0;
    }

    /**
     * Scope : Demandes avec montant accordé
     */
    public function scopeAvecMontantAccorde($query)
    {
        return $query->whereNotNull('montant_accord');
    }
}

