<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medecin_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rendez_vous_id')->nullable()->constrained('rendez_vous')->nullOnDelete();
            $table->decimal('montant_total', 10, 2);
            $table->integer('taux_couverture')->default(0);
            $table->decimal('montant_assurance', 10, 2)->default(0);
            $table->decimal('montant_patient', 10, 2);
            $table->decimal('montant_donne', 10, 2)->default(0);
            $table->decimal('montant_rendu', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
