<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'montant_donne')) {
                $table->decimal('montant_donne', 10, 2)->default(0)->after('montant_patient');
            }
            if (!Schema::hasColumn('consultations', 'montant_rendu')) {
                $table->decimal('montant_rendu', 10, 2)->default(0)->after('montant_donne');
            }
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['montant_donne', 'montant_rendu']);
        });
    }
};
