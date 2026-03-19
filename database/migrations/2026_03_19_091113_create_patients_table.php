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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique(); // Unique pour éviter les doublons
            $table->string('telephone');
            
            // RELATION : On lie le patient à son assurance
            // On met ->nullable() car un patient n'a pas forcément d'assurance
            $table->foreignId('assurance_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('numero_assurance')->nullable(); // Ex: Numéro de carte CNAMGS
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};