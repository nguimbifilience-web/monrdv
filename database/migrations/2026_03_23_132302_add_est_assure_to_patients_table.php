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
        Schema::table('patients', function (Blueprint $table) {
            // On ajoute la colonne après 'quartier' pour garder une table organisée
            $table->boolean('est_assure')->default(false)->after('quartier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Permet de revenir en arrière si nécessaire
            $table->dropColumn('est_assure');
        });
    }
};