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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('peut_saisir_pcs')->default(false)->after('poste_id');
            $table->boolean('peut_valider_pcs')->default(false)->after('peut_saisir_pcs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['peut_saisir_pcs', 'peut_valider_pcs']);
        });
    }
};


