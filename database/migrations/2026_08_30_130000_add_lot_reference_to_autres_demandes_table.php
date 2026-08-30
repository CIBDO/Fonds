<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('autres_demandes', function (Blueprint $table) {
            $table->uuid('lot_reference')->nullable()->after('poste_id');
            $table->index('lot_reference');
        });
    }

    public function down(): void
    {
        Schema::table('autres_demandes', function (Blueprint $table) {
            $table->dropIndex(['lot_reference']);
            $table->dropColumn('lot_reference');
        });
    }
};
