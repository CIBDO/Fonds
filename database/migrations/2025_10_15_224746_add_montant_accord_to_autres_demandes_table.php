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
        Schema::table('autres_demandes', function (Blueprint $table) {
            $table->decimal('montant_accord', 15, 2)->nullable()->after('montant')
                  ->comment('Montant effectivement accordé par l\'ACCT');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('autres_demandes', function (Blueprint $table) {
            $table->dropColumn('montant_accord');
        });
    }
};
