<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motif_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->onDelete('cascade');
            $table->foreignId('specialite_id')->constrained()->onDelete('cascade');
            $table->string('libelle');
            $table->timestamps();

            $table->unique(['clinic_id', 'specialite_id', 'libelle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motif_consultations');
    }
};
