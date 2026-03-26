<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // 1. On n'ajoute la colonne que si elle n'existe pas
            if (!Schema::hasColumn('patients', 'medecin_id')) {
                $table->unsignedBigInteger('medecin_id')->nullable()->after('id');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            // 2. On ajoute la clé étrangère séparément pour éviter les conflits
            $table->foreign('medecin_id')->references('id')->on('medecins')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // On supprime proprement si on fait un rollback
            $table->dropForeign(['medecin_id']);
            $table->dropColumn('medecin_id');
        });
    }
};