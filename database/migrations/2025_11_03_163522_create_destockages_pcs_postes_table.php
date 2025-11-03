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
        Schema::create('destockages_pcs_postes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destockage_pcs_id')->constrained('destockages_pcs')->onDelete('cascade');
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('cascade');
            $table->foreignId('bureau_douane_id')->nullable()->constrained('bureaux_douanes')->onDelete('cascade');
            $table->decimal('montant_collecte', 15, 2)->default(0);
            $table->decimal('montant_destocke', 15, 2)->default(0);
            $table->decimal('solde_avant', 15, 2)->default(0);
            $table->decimal('solde_apres', 15, 2)->default(0);
            $table->timestamps();
            
            // Index pour optimiser les requêtes
            $table->index('poste_id');
            $table->index('bureau_douane_id');
            $table->index('destockage_pcs_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destockages_pcs_postes');
    }
};
