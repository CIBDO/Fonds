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
        Schema::create('bureaux_trie', function (Blueprint $table) {
            $table->id();
            
            // Relation avec le poste
            $table->foreignId('poste_id')->constrained('postes')->onDelete('cascade');
            
            // Informations du bureau
            $table->string('code_bureau')->unique();
            $table->string('nom_bureau');
            $table->text('description')->nullable();
            
            // Statut
            $table->boolean('actif')->default(true);
            
            $table->timestamps();
            
            // Index
            $table->index(['poste_id', 'actif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bureaux_trie');
    }
};
