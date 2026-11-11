<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('destinataires_mail_alertes', function (Blueprint $table) {
            if (!Schema::hasColumn('destinataires_mail_alertes', 'fnl_soumise')) {
                $table->boolean('fnl_soumise')->default(true)->after('vir_paiement');
            }
        });
    }

    public function down(): void
    {
        Schema::table('destinataires_mail_alertes', function (Blueprint $table) {
            if (Schema::hasColumn('destinataires_mail_alertes', 'fnl_soumise')) {
                $table->dropColumn('fnl_soumise');
            }
        });
    }
};
