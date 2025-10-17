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
        Schema::create('types_postes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique(); // BUREAU, RGD, ACCT
            $table->string('libelle', 255);
            $table->integer('niveau_hierarchique'); // 1=Bureau, 2=RGD, 3=ACCT
            $table->boolean('peut_saisir')->default(true); // Peut créer des déclarations
            $table->boolean('peut_consolider')->default(false); // Peut consolider/valider
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types_postes');
    }
};




