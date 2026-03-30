<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_patient', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('nom');
            $table->string('type')->default('assurance'); // assurance, ordonnance, autre
            $table->string('fichier'); // chemin du fichier
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_patient');
    }
};
