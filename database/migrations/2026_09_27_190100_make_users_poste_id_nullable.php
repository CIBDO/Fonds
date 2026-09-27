<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * L'ACCD n'est pas rattaché à un poste du Trésor.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'poste_id')) {
            return;
        }

        DB::statement('ALTER TABLE users MODIFY poste_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        if (!Schema::hasColumn('users', 'poste_id')) {
            return;
        }

        DB::statement('UPDATE users SET poste_id = (SELECT id FROM postes ORDER BY id LIMIT 1) WHERE poste_id IS NULL');
        DB::statement('ALTER TABLE users MODIFY poste_id BIGINT UNSIGNED NOT NULL');
    }
};
