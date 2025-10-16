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
        Schema::create('bureaux_douanes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poste_rgd_id')->constrained('postes')->onDelete('cascade');
            $table->string('code', 50);
            $table->string('libelle', 255);
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('poste_rgd_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bureaux_douanes');
    }
};


