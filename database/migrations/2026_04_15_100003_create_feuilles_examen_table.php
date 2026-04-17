<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feuilles_examen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->foreignId('medecin_id')->constrained('medecins')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('consultation_id')->nullable()->constrained('consultations')->nullOnDelete();
            $table->date('date');
            $table->text('motif_clinique')->nullable();
            $table->timestamps();
            $table->index(['clinic_id', 'patient_id']);
            $table->index(['clinic_id', 'medecin_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feuilles_examen');
    }
};
