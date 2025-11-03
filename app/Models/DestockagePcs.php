<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DestockagePcs extends Model
{
    use HasFactory;

    protected $table = 'destockages_pcs';

    protected $fillable = [
        'reference_destockage',
        'programme',
        'periode_mois',
        'periode_annee',
        'montant_total_destocke',
        'date_destockage',
        'observation',
        'statut',
        'cree_par',
    ];

    protected $casts = [
        'montant_total_destocke' => 'decimal:2',
        'date_destockage' => 'date',
        'periode_mois' => 'integer',
        'periode_annee' => 'integer',
    ];

    /**
     * Relation avec l'utilisateur créateur
     */
    public function creePar()
    {
        return $this->belongsTo(User::class, 'cree_par');
    }

    /**
     * Relation avec les postes du déstockage
     */
    public function postes()
    {
        return $this->hasMany(DestockagePcsPoste::class, 'destockage_pcs_id');
    }

    /**
     * Accesseur : Nom du mois en français
     */
    public function getNomMoisAttribute()
    {
        return Carbon::create()->month($this->periode_mois)->locale('fr')->translatedFormat('F');
    }

    /**
     * Scope : Par programme
     */
    public function scopeProgramme($query, $programme)
    {
        return $query->where('programme', $programme);
    }

    /**
     * Scope : Par période
     */
    public function scopePeriode($query, $mois, $annee)
    {
        return $query->where('periode_mois', $mois)->where('periode_annee', $annee);
    }

    /**
     * Scope : Déstockages validés
     */
    public function scopeValide($query)
    {
        return $query->where('statut', 'valide');
    }

    /**
     * Générer une référence unique
     */
    public static function genererReference($programme, $mois, $annee)
    {
        $prefix = $programme;
        $moisFormate = str_pad($mois, 2, '0', STR_PAD_LEFT);
        $date = date('Ymd');
        $numero = self::where('programme', $programme)
            ->whereDate('created_at', today())
            ->count() + 1;
        
        return "DST-{$prefix}-{$moisFormate}-{$annee}-{$date}-" . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
