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
        Schema::create('pieces_jointes_pcs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('declaration_pcs_id')->constrained('declarations_pcs')->onDelete('cascade');
            $table->string('nom_fichier', 255);
            $table->string('nom_original', 255);
            $table->string('chemin_fichier', 500);
            $table->string('type_mime', 100);
            $table->integer('taille'); // en octets
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('declaration_pcs_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes_pcs');
    }
};


