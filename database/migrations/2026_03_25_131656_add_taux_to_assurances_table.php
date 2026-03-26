<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('assurances', function (Blueprint $table) {
        if (!Schema::hasColumn('assurances', 'taux_couverture')) {
            // On l'ajoute en integer (pour le 75%) après le nom
            $table->integer('taux_couverture')->default(0)->after('nom');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            //
        });
    }
};
