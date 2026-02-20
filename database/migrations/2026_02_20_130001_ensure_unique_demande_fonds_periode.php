<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Garantit une seule demande de fonds par (poste_id, mois, année).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        $table = 'demande_fonds';
        $indexName = 'unique_demande_fonds_poste_mois_annee';

        if ($driver !== 'mysql' || !Schema::hasTable($table)) {
            return;
        }

        if (!Schema::hasColumns($table, ['poste_id', 'mois', 'annee'])) {
            return;
        }

        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        if (empty($indexes)) {
            DB::statement("ALTER TABLE {$table} ADD UNIQUE KEY {$indexName} (poste_id, mois, annee)");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        $table = 'demande_fonds';
        $indexName = 'unique_demande_fonds_poste_mois_annee';

        if ($driver === 'mysql') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (!empty($indexes)) {
                DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
            }
        }
    }
};
