<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autre_demande_echelons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('autre_demande_id')->constrained('autres_demandes')->onDelete('cascade');
            $table->unsignedTinyInteger('ordre')->default(1);
            $table->date('date_echeance');
            $table->decimal('montant', 15, 2);
            $table->timestamps();

            $table->index(['autre_demande_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autre_demande_echelons');
    }
};
