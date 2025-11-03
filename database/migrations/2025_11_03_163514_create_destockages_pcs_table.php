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
        Schema::create('destockages_pcs', function (Blueprint $table) {
            $table->id();
            $table->string('reference_destockage')->unique();
            $table->enum('programme', ['UEMOA', 'AES']);
            $table->integer('periode_mois');
            $table->integer('periode_annee');
            $table->decimal('montant_total_destocke', 15, 2)->default(0);
            $table->date('date_destockage');
            $table->text('observation')->nullable();
            $table->enum('statut', ['brouillon', 'valide', 'annule'])->default('brouillon');
            $table->foreignId('cree_par')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index(['programme', 'periode_mois', 'periode_annee']);
            $table->index('statut');
            $table->index('date_destockage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destockages_pcs');
    }
};
