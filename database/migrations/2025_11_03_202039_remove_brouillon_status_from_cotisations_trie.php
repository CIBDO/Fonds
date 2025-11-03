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
        // Mettre à jour toutes les cotisations en brouillon vers validé
        DB::table('cotisations_trie')
            ->where('statut', 'brouillon')
            ->update(['statut' => 'valide']);
        
        // Modifier le enum pour n'avoir que 'valide'
        DB::statement("ALTER TABLE cotisations_trie MODIFY COLUMN statut ENUM('valide') DEFAULT 'valide'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer l'enum avec brouillon et valide
        DB::statement("ALTER TABLE cotisations_trie MODIFY COLUMN statut ENUM('brouillon', 'valide') DEFAULT 'brouillon'");
    }
};
