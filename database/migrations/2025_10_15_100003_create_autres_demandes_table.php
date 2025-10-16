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
        Schema::create('autres_demandes', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->foreignId('poste_id')->constrained('postes')->onDelete('cascade');
            $table->string('designation', 500);
            $table->decimal('montant', 15, 2);
            $table->text('observation')->nullable();
            $table->date('date_demande');
            $table->integer('annee');

            // Workflow
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete'])->default('brouillon');
            $table->datetime('date_validation')->nullable();
            $table->text('motif_rejet')->nullable();

            // Traçabilité
            $table->foreignId('saisi_par')->constrained('users')->onDelete('cascade');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Index pour recherches
            $table->index(['poste_id', 'annee', 'date_demande']);
            $table->index('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('autres_demandes');
    }
};


