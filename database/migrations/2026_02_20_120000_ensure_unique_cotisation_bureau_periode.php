<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Garantit qu'une seule cotisation existe par (bureau, mois, année).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        $table = 'cotisations_trie';
        $indexName = 'unique_cotisation_bureau_periode';

        if ($driver === 'mysql') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (empty($indexes)) {
                DB::statement("ALTER TABLE {$table} ADD UNIQUE KEY {$indexName} (bureau_trie_id, mois, annee)");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        $table = 'cotisations_trie';
        $indexName = 'unique_cotisation_bureau_periode';

        if ($driver === 'mysql') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (!empty($indexes)) {
                DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
            }
        }
    }
};
