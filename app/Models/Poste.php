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

    // Relations existantes
    public function demandesFonds()
    {
        return $this->hasMany(DemandeFonds::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Nouvelles relations PCS

    /**
     * Relation : Bureaux de douanes (si c'est la RGD)
     */
    public function bureauxDouanes()
    {
        return $this->hasMany(BureauDouane::class, 'poste_rgd_id');
    }

    /**
     * Relation : Déclarations PCS du poste
     */
    public function declarationsPcs()
    {
        return $this->hasMany(DeclarationPcs::class, 'poste_id');
    }

    /**
     * Relation : Autres demandes du poste
     */
    public function autresDemandes()
    {
        return $this->hasMany(AutreDemande::class, 'poste_id');
    }

    /**
     * Relation : Bureaux TRIE du poste
     */
    public function bureauxTrie()
    {
        return $this->hasMany(BureauTrie::class, 'poste_id');
    }

    /**
     * Relation : Cotisations TRIE du poste
     */
    public function cotisationsTrie()
    {
        return $this->hasMany(CotisationTrie::class, 'poste_id');
    }

    /**
     * Méthode : Vérifier si c'est la RGD
     */
    public function isRgd()
    {
        return strtoupper($this->nom) === 'RGD';
    }

    /**
     * Méthode : Vérifier si c'est l'ACCT
     */
    public function isAcct()
    {
        return strtoupper($this->nom) === 'ACCT';
    }

    /**
     * Méthode : Obtenir toutes les déclarations PCS (propres + bureaux)
     */
    public function getToutesDeclarationsPcs($mois, $annee, $programme = null)
    {
        $declarations = collect();

        // Déclarations propres du poste
        $query = $this->declarationsPcs()
            ->where('mois', $mois)
            ->where('annee', $annee);

        if ($programme) {
            $query->where('programme', $programme);
        }

        $declarations = $declarations->merge($query->get());

        // Si c'est la RGD, ajouter les déclarations des bureaux
        if ($this->isRgd()) {
            foreach ($this->bureauxDouanes as $bureau) {
                $declarations = $declarations->merge(
                    $bureau->getDeclarationsMois($mois, $annee, $programme)
                );
            }
        }

        return $declarations;
    }
}
