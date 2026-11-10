<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postes', function (Blueprint $table) {
            if (!Schema::hasColumn('postes', 'type')) {
                $table->string('type', 20)->default('superieur')->after('nom');
            }
            if (!Schema::hasColumn('postes', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('type')
                    ->constrained('postes')->nullOnDelete();
            }
            if (!Schema::hasColumn('postes', 'intitule_officiel')) {
                $table->string('intitule_officiel')->nullable()->after('parent_id');
            }
            if (!Schema::hasColumn('postes', 'region')) {
                $table->string('region')->nullable()->after('intitule_officiel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('postes', function (Blueprint $table) {
            if (Schema::hasColumn('postes', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
            foreach (['type', 'intitule_officiel', 'region'] as $column) {
                if (Schema::hasColumn('postes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
