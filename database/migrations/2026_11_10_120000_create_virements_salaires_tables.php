<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banques', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('nom');
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('bordereau_virements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_fonds_id')->constrained('demande_fonds')->cascadeOnDelete();
            $table->foreignId('poste_id')->constrained('postes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('mois', 20);
            $table->unsignedSmallInteger('annee');
            $table->date('periode_debut')->nullable();
            $table->date('periode_fin')->nullable();
            $table->string('status', 20)->default('brouillon'); // brouillon|soumis
            $table->string('fichier_joint')->nullable();
            $table->timestamp('soumis_at')->nullable();
            $table->timestamps();

            $table->unique(['demande_fonds_id']);
        });

        Schema::create('virements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bordereau_virement_id')->constrained('bordereau_virements')->cascadeOnDelete();
            $table->foreignId('poste_id')->constrained('postes')->cascadeOnDelete();
            $table->foreignId('banque_id')->constrained('banques')->restrictOnDelete();
            $table->string('numero_vir');
            $table->string('mois', 20);
            $table->unsignedSmallInteger('annee');
            $table->decimal('montant', 18, 2);
            $table->string('statut_paiement', 20)->default('non_paye'); // non_paye|paye
            $table->timestamp('date_paiement')->nullable();
            $table->foreignId('paye_par')->nullable()->constrained('users')->nullOnDelete();
            $table->text('observation')->nullable();
            $table->timestamps();

            $table->unique(['bordereau_virement_id', 'banque_id']);
            $table->index(['mois', 'annee', 'poste_id']);
            $table->index(['numero_vir', 'annee']);
            $table->index('statut_paiement');
        });

        Schema::create('lignes_virements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virement_id')->constrained('virements')->cascadeOnDelete();
            $table->string('num_be')->nullable();
            $table->string('num_mdt')->nullable();
            $table->string('ord')->nullable();
            $table->string('programme')->nullable();
            $table->string('creancier_libelle')->nullable();
            $table->string('num_compte')->nullable()->default('NEANT');
            $table->decimal('montant', 18, 2);
            $table->timestamps();
        });

        Schema::create('destinataires_mail_alertes', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('nom')->nullable();
            $table->boolean('actif')->default(true);
            $table->boolean('fonds_soumise')->default(true);
            $table->boolean('fonds_statut')->default(false);
            $table->boolean('autre_soumise')->default(true);
            $table->boolean('autre_validee')->default(false);
            $table->boolean('autre_rejetee')->default(false);
            $table->boolean('vir_soumission')->default(true);
            $table->boolean('vir_paiement')->default(true);
            $table->boolean('fnl_soumise')->default(true);
            $table->timestamps();

            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinataires_mail_alertes');
        Schema::dropIfExists('lignes_virements');
        Schema::dropIfExists('virements');
        Schema::dropIfExists('bordereau_virements');
        Schema::dropIfExists('banques');
    }
};
