<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feuille_examen_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feuille_examen_id')->constrained('feuilles_examen')->cascadeOnDelete();
            $table->string('type_examen')->default('biologie'); // biologie, imagerie, autre
            $table->string('libelle');
            $table->boolean('urgence')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feuille_examen_lignes');
    }
};
