<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paiements_fnl', function (Blueprint $table) {
            if (!Schema::hasColumn('paiements_fnl', 'libelle')) {
                $table->string('libelle')->nullable()->after('poste_id');
                $table->index(['poste_id', 'mois', 'annee', 'libelle'], 'paiements_fnl_poste_periode_libelle_idx');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paiements_fnl', function (Blueprint $table) {
            if (Schema::hasColumn('paiements_fnl', 'libelle')) {
                $table->dropIndex('paiements_fnl_poste_periode_libelle_idx');
                $table->dropColumn('libelle');
            }
        });
    }
};
