<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On vérifie si la colonne existe AVANT de tenter de l'ajouter
        if (!Schema::hasColumn('assurances', 'description')) {
            Schema::table('assurances', function (Blueprint $table) {
                $table->text('description')->nullable()->after('nom');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assurances', 'description')) {
            Schema::table('assurances', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};