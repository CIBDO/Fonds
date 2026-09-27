<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements_fnl', function (Blueprint $table) {
            $table->id();

            $table->foreignId('poste_id')->constrained('postes')->onDelete('cascade');
            $table->integer('mois');
            $table->integer('annee');

            $table->decimal('retenue_fnl', 15, 2)->default(0);
            $table->decimal('montant_vire_accd', 15, 2)->default(0);

            $table->string('reference_paiement')->nullable();
            $table->string('preuve_paiement', 500)->nullable();
            $table->date('date_paiement')->nullable();
            $table->text('observation')->nullable();

            $table->enum('statut', ['soumis', 'valide', 'rejete'])->default('soumis');
            $table->text('motif_rejet')->nullable();
            $table->datetime('date_saisie');
            $table->datetime('date_validation')->nullable();

            $table->foreignId('saisi_par')->constrained('users')->onDelete('cascade');
            $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            $table->unique(['poste_id', 'mois', 'annee'], 'unique_paiement_fnl_poste_periode');
            $table->index(['statut', 'mois', 'annee']);
            $table->index(['poste_id', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements_fnl');
    }
};
