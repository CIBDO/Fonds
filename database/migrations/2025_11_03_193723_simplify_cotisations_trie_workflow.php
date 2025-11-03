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
        Schema::table('cotisations_trie', function (Blueprint $table) {
            // Modifier le enum pour n'avoir que brouillon et valide
            DB::statement("ALTER TABLE cotisations_trie MODIFY COLUMN statut ENUM('brouillon', 'valide') DEFAULT 'brouillon'");
            
            // Supprimer les colonnes non nécessaires
            $table->dropColumn(['date_soumission', 'motif_rejet']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotisations_trie', function (Blueprint $table) {
            // Restaurer l'enum original
            DB::statement("ALTER TABLE cotisations_trie MODIFY COLUMN statut ENUM('brouillon', 'soumis', 'valide', 'rejete') DEFAULT 'brouillon'");
            
            // Restaurer les colonnes
            $table->datetime('date_soumission')->nullable()->after('statut');
            $table->text('motif_rejet')->nullable()->after('date_validation');
        });
    }
};
