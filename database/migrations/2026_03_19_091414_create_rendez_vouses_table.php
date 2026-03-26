<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rendez_vous')) {
            Schema::create('rendez_vous', function (Blueprint $table) {
                $table->id();
                $table->foreignId('patient_id')->constrained()->onDelete('cascade');
                $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
                $table->date('date_rv');
                $table->time('heure_rv');
                $table->string('statut')->default('en_attente');
                $table->text('motif')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};