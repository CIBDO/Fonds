<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeclarationPcs extends Model
{
    use HasFactory;

    protected $table = 'declarations_pcs';

    protected $fillable = [
        'poste_id',
        'bureau_douane_id',
        'programme',
        'mois',
        'annee',
        'montant_recouvrement',
        'montant_reversement',
        'reference',
        'observation',
        'statut',
        'date_saisie',
        'date_soumission',
        'date_validation',
        'motif_rejet',
        'saisi_par',
        'valide_par',
    ];

    protected $casts = [
        'mois' => 'integer',
        'annee' => 'integer',
        'montant_recouvrement' => 'decimal:2',
        'montant_reversement' => 'decimal:2',
        'date_saisie' => 'datetime',
        'date_soumission' => 'datetime',
        'date_validation' => 'datetime',
    ];

    /**
     * Relation avec le poste (pour postes normaux)
     */
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    /**
     * Relation avec le bureau de douane (pour bureaux RGD)
     */
    public function bureauDouane()
    {
        return $this->belongsTo(BureauDouane::class, 'bureau_douane_id');
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
     * Relation avec les pièces jointes
     */
    public function piecesJointes()
    {
        return $this->hasMany(PieceJointePcs::class, 'declaration_pcs_id');
    }

    /**
     * Relation avec l'historique des statuts
     */
    public function historiqueStatuts()
    {
        return $this->hasMany(HistoriqueStatutPcs::class, 'declaration_pcs_id');
    }

    /**
     * Accesseur : Reste à reverser
     */
    public function getResteAReverserAttribute()
    {
        return $this->montant_recouvrement - $this->montant_reversement;
    }

    /**
     * Accesseur : Nom de l'entité (Poste ou Bureau)
     */
    public function getNomEntiteAttribute()
    {
        if ($this->poste_id) {
            return $this->poste->nom;
        }
        if ($this->bureau_douane_id) {
            return $this->bureauDouane->libelle;
        }
        return 'N/A';
    }

    /**
     * Accesseur : Type d'entité
     */
    public function getTypeEntiteAttribute()
    {
        return $this->poste_id ? 'poste' : 'bureau';
    }

    /**
     * Scope : Par programme
     */
    public function scopeProgramme($query, $programme)
    {
        return $query->where('programme', $programme);
    }

    /**
     * Scope : Par mois et année
     */
    public function scopePeriode($query, $mois, $annee)
    {
        return $query->where('mois', $mois)->where('annee', $annee);
    }

    /**
     * Scope : Déclarations validées
     */
    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Scope : Déclarations soumises (en attente)
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
     * Méthode : Soumettre la déclaration
     */
    public function soumettre()
    {
        $this->statut = 'soumis';
        $this->date_soumission = now();
        $this->save();

        // Enregistrer dans l'historique
        $this->historiqueStatuts()->create([
            'ancien_statut' => 'brouillon',
            'nouveau_statut' => 'soumis',
            'utilisateur_id' => auth()->id(),
            'date_changement' => now(),
        ]);
    }

    /**
     * Méthode : Valider la déclaration
     */
    public function valider($valideurId)
    {
        $ancienStatut = $this->statut;
        $this->statut = 'valide';
        $this->date_validation = now();
        $this->valide_par = $valideurId;
        $this->save();

        // Enregistrer dans l'historique
        $this->historiqueStatuts()->create([
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'valide',
            'utilisateur_id' => $valideurId,
            'date_changement' => now(),
        ]);
    }

    /**
     * Méthode : Rejeter la déclaration
     */
    public function rejeter($valideurId, $motif)
    {
        $ancienStatut = $this->statut;
        $this->statut = 'rejete';
        $this->motif_rejet = $motif;
        $this->valide_par = $valideurId;
        $this->save();

        // Enregistrer dans l'historique
        $this->historiqueStatuts()->create([
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'rejete',
            'utilisateur_id' => $valideurId,
            'commentaire' => $motif,
            'date_changement' => now(),
        ]);
    }
}

