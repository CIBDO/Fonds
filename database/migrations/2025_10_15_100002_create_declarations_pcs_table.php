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
        Schema::create('declarations_pcs', function (Blueprint $table) {
            $table->id();

            // Qui déclare : poste OU bureau (jamais les deux)
            $table->foreignId('poste_id')->nullable()->constrained('postes')->onDelete('cascade');
            $table->foreignId('bureau_douane_id')->nullable()->constrained('bureaux_douanes')->onDelete('cascade');

            // Programme et période
            $table->enum('programme', ['UEMOA', 'AES']);
            $table->integer('mois'); // 1-12
            $table->integer('annee');

            // Montants
            $table->decimal('montant_recouvrement', 15, 2);
            $table->decimal('montant_reversement', 15, 2);
            $table->text('observation')->nullable();

            // Workflow
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete'])->default('brouillon');
            $table->datetime('date_saisie');
            $table->datetime('date_soumission')->nullable();
            $table->datetime('date_validation')->nullable();
            $table->text('motif_rejet')->nullable();

            // Traçabilité
            $table->foreignId('saisi_par')->constrained('users')->onDelete('cascade');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Contraintes
            $table->unique(['poste_id', 'bureau_douane_id', 'programme', 'mois', 'annee'], 'unique_declaration');
            $table->index(['statut', 'programme', 'mois', 'annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations_pcs');
    }
};

