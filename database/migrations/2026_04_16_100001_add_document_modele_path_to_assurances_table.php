<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->string('document_modele_path')->nullable()->after('taux_couverture');
        });
    }

    public function down(): void
    {
        Schema::table('assurances', function (Blueprint $table) {
            $table->dropColumn('document_modele_path');
        });
    }
};
