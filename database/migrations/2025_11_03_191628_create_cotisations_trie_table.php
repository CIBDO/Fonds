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
        Schema::create('cotisations_trie', function (Blueprint $table) {
            $table->id();
            
            // Relations
            $table->foreignId('poste_id')->constrained('postes')->onDelete('cascade');
            $table->foreignId('bureau_trie_id')->constrained('bureaux_trie')->onDelete('cascade');
            
            // Période de la cotisation
            $table->integer('mois'); // 1-12
            $table->integer('annee');
            
            // Montants
            $table->decimal('montant_cotisation_courante', 15, 2)->default(0); // Cotisation du mois courant
            $table->decimal('montant_apurement', 15, 2)->default(0); // Rattrapage/Apurement solde antérieur
            $table->decimal('montant_total', 15, 2); // Total = cotisation_courante + apurement
            
            // Détails du paiement
            $table->enum('mode_paiement', ['cheque', 'virement', 'especes', 'autre'])->nullable();
            $table->string('reference_paiement')->nullable(); // Ex: "CHQ BDM n°8903232"
            $table->date('date_paiement')->nullable();
            
            // Précisions sur l'apurement (rattrapage)
            $table->text('detail_apurement')->nullable(); // Ex: "Apurement janvier-mars 2024"
            
            // Observations
            $table->text('observation')->nullable();
            
            // Workflow de validation
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete'])->default('brouillon');
            $table->datetime('date_saisie');
            $table->datetime('date_soumission')->nullable();
            $table->datetime('date_validation')->nullable();
            $table->text('motif_rejet')->nullable();
            
            // Traçabilité
            $table->foreignId('saisi_par')->constrained('users')->onDelete('cascade');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Contraintes et index
            $table->unique(['bureau_trie_id', 'mois', 'annee'], 'unique_cotisation_bureau_periode');
            $table->index(['statut', 'mois', 'annee']);
            $table->index(['poste_id', 'annee']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations_trie');
    }
};
