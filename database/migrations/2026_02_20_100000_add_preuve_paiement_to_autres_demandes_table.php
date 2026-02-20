<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('autres_demandes', function (Blueprint $table) {
            $table->string('preuve_paiement', 500)->nullable()->after('observation');
        });
    }

    public function down(): void
    {
        Schema::table('autres_demandes', function (Blueprint $table) {
            $table->dropColumn('preuve_paiement');
        });
    }
};
