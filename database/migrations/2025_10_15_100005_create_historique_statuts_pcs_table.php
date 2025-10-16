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
        Schema::create('historique_statuts_pcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_pcs_id')->constrained('declarations_pcs')->onDelete('cascade');
            $table->string('ancien_statut', 50);
            $table->string('nouveau_statut', 50);
            $table->foreignId('utilisateur_id')->constrained('users')->onDelete('cascade');
            $table->text('commentaire')->nullable();
            $table->datetime('date_changement');
            $table->timestamp('created_at')->useCurrent();

            $table->index('declaration_pcs_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historique_statuts_pcs');
    }
};


