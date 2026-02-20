<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Garantit une seule déclaration PCS par (poste_id, bureau_douane_id, programme, mois, année).
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        $table = 'declarations_pcs';
        $indexName = 'unique_declaration';

        if ($driver === 'mysql') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (empty($indexes)) {
                DB::statement("ALTER TABLE {$table} ADD UNIQUE KEY {$indexName} (poste_id, bureau_douane_id, programme, mois, annee)");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        $table = 'declarations_pcs';
        $indexName = 'unique_declaration';

        if ($driver === 'mysql') {
            $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            if (!empty($indexes)) {
                DB::statement("ALTER TABLE {$table} DROP INDEX {$indexName}");
            }
        }
    }
};
