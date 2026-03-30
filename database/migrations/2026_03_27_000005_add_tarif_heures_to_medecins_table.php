<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->decimal('tarif_heure', 10, 2)->default(0)->after('telephone');
            $table->integer('heures_mois')->default(0)->after('tarif_heure');
        });
    }

    public function down(): void
    {
        Schema::table('medecins', function (Blueprint $table) {
            $table->dropColumn(['tarif_heure', 'heures_mois']);
        });
    }
};
