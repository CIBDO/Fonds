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
        Schema::table('declarations_pcs', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('montant_reversement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('declarations_pcs', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};
