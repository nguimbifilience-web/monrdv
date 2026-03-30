<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->index(['medecin_id', 'date_rv', 'heure_rv'], 'rdv_medecin_date_heure');
            $table->index(['patient_id', 'date_rv'], 'rdv_patient_date');
            $table->index('statut');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->index(['assurance_id', 'created_at'], 'patients_assurance_created');
            $table->index('est_assure');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('medecins', function (Blueprint $table) {
            $table->index('specialite_id');
        });
    }

    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropIndex('rdv_medecin_date_heure');
            $table->dropIndex('rdv_patient_date');
            $table->dropIndex(['statut']);
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_assurance_created');
            $table->dropIndex(['est_assure']);
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('medecins', function (Blueprint $table) {
            $table->dropIndex(['specialite_id']);
        });
    }
};
