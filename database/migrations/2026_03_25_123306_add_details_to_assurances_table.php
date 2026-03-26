<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            // On vérifie chaque colonne avant de l'ajouter
            if (!Schema::hasColumn('assurances', 'nom_referent')) {
                $table->string('nom_referent')->nullable()->after('nom');
            }
            
            if (!Schema::hasColumn('assurances', 'telephone')) {
                $table->string('telephone')->nullable()->after('nom_referent');
            }

            if (!Schema::hasColumn('assurances', 'email')) {
                $table->string('email')->nullable()->after('telephone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->dropColumn(['nom_referent', 'telephone', 'email']);
        });
    }
};