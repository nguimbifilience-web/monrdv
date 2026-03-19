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
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_rdv'); // Date et Heure du rendez-vous
            $table->string('motif')->nullable(); // Pourquoi le patient vient-il ?
            $table->enum('statut', ['en_attente', 'confirme', 'annule'])->default('en_attente');
            
            // LES CLÉS ÉTRANGÈRES : Le cœur du système
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('medecin_id')->constrained()->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};