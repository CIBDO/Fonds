<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('paiements_fnl', 'montant_vire_accd')) {
            Schema::table('paiements_fnl', function (Blueprint $table) {
                $table->dropColumn('montant_vire_accd');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('paiements_fnl', 'montant_vire_accd')) {
            Schema::table('paiements_fnl', function (Blueprint $table) {
                $table->decimal('montant_vire_accd', 15, 2)->default(0)->after('retenue_fnl');
            });
        }
    }
};
