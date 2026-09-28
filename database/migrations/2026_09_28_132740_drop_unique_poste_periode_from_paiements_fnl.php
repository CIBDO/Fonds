<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('paiements_fnl', function (Blueprint $table) {
            $table->dropUnique('unique_paiement_fnl_poste_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paiements_fnl', function (Blueprint $table) {
            $table->unique(['poste_id', 'mois', 'annee'], 'unique_paiement_fnl_poste_periode');
        });
    }
};
