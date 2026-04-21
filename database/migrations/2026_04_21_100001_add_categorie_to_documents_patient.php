<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents_patient', function (Blueprint $table) {
            $table->enum('categorie', ['informations', 'medical'])
                ->default('informations')
                ->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('documents_patient', function (Blueprint $table) {
            $table->dropColumn('categorie');
        });
    }
};
