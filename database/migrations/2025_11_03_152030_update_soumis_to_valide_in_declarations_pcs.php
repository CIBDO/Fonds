<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les déclarations en statut "soumis" vers "valide"
        // et remplir date_validation et valide_par si nécessaire
        DB::table('declarations_pcs')
            ->where('statut', 'soumis')
            ->update([
                'statut' => 'valide',
                'date_validation' => DB::raw('COALESCE(date_validation, date_soumission)'),
                'valide_par' => DB::raw('COALESCE(valide_par, saisi_par)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: On ne peut pas vraiment inverser cette migration car on ne sait pas
        // quelles déclarations étaient vraiment "soumis" vs "valide"
        // On laisse vide pour préserver l'historique
    }
};
