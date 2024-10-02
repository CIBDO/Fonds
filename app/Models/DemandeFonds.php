<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeFonds extends Model
{
    use HasFactory;

    protected $table = 'demande_fonds';

    protected $fillable = [
        'user_id',
        'mois',
        'annee',
        'total_demande',
        'status',
        'fonctionnaires_bcs_net',
        'fonctionnaires_bcs_revers',
        'fonctionnaires_bcs_total_courant',
        'fonctionnaires_bcs_salaire_ancien',
        'fonctionnaires_bcs_total_demande',
        'collectivite_sante_net',
        'collectivite_sante_revers',
        'collectivite_sante_total_courant',
        'collectivite_sante_salaire_ancien',
        'collectivite_sante_total_demande',
        'collectivite_education_net',
        'collectivite_education_revers',
        'collectivite_education_total_courant',
        'collectivite_education_salaire_ancien',
        'collectivite_education_total_demande',
        'personnels_saisonniers_net',
        'personnels_saisonniers_revers',
        'personnels_saisonniers_total_courant',
        'personnels_saisonniers_salaire_ancien',
        'personnels_saisonniers_total_demande',
        'epn_net',
        'epn_revers',
        'epn_total_courant',
        'epn_salaire_ancien',
        'epn_total_demande',
        'poste_id',
        'date_reception',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec le poste
    public function poste()
    {
        return $this->belongsTo(Poste::class);
    }
}
