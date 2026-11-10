<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poste extends Model
{
    use HasFactory;

    public const TYPE_SUPERIEUR = 'superieur';
    public const TYPE_RP = 'rp';

    protected $table = 'postes';

    protected $fillable = [
        'nom',
        'type',
        'parent_id',
        'intitule_officiel',
        'region',
        'created_at',
        'updated_at',
    ];

    public function demandesFonds()
    {
        return $this->hasMany(DemandeFonds::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function recettesPerceptions()
    {
        return $this->hasMany(self::class, 'parent_id')->where('type', self::TYPE_RP)->orderBy('nom');
    }

    public function bureauxDouanes()
    {
        return $this->hasMany(BureauDouane::class, 'poste_rgd_id');
    }

    public function declarationsPcs()
    {
        return $this->hasMany(DeclarationPcs::class, 'poste_id');
    }

    public function autresDemandes()
    {
        return $this->hasMany(AutreDemande::class, 'poste_id');
    }

    public function bureauxTrie()
    {
        return $this->hasMany(BureauTrie::class, 'poste_id');
    }

    public function cotisationsTrie()
    {
        return $this->hasMany(CotisationTrie::class, 'poste_id');
    }

    public function paiementsFnl()
    {
        return $this->hasMany(PaiementFnl::class, 'poste_id');
    }

    public function isSuperieur(): bool
    {
        return ($this->type ?? self::TYPE_SUPERIEUR) === self::TYPE_SUPERIEUR;
    }

    public function isRp(): bool
    {
        return ($this->type ?? '') === self::TYPE_RP;
    }

    public function isRgd()
    {
        return strtoupper((string) $this->nom) === 'RGD';
    }

    public function isAcct()
    {
        return strtoupper((string) $this->nom) === 'ACCT';
    }

    public function intitulePourEntete(): string
    {
        if ($this->intitule_officiel) {
            return mb_strtoupper($this->intitule_officiel);
        }

        if ($this->isRp() && $this->parent) {
            return $this->parent->intitulePourEntete();
        }

        return 'TRESORERIE REGIONALE DE '.mb_strtoupper((string) $this->nom);
    }

    public function regionPourEntete(): ?string
    {
        if ($this->region) {
            return $this->region;
        }

        if ($this->isRp() && $this->parent) {
            return $this->parent->regionPourEntete();
        }

        return $this->nom ? 'Région de '.$this->nom : null;
    }

    public function libelleAffiche(): string
    {
        if ($this->isRp()) {
            $nom = (string) $this->nom;
            return str_starts_with(mb_strtoupper($nom), 'RP ') ? $nom : 'RP '.$nom;
        }

        return (string) $this->nom;
    }

    public function getToutesDeclarationsPcs($mois, $annee, $programme = null)
    {
        $declarations = collect();

        $query = $this->declarationsPcs()
            ->where('mois', $mois)
            ->where('annee', $annee);

        if ($programme) {
            $query->where('programme', $programme);
        }

        $declarations = $declarations->merge($query->get());

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
