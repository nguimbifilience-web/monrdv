<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Index composites clinic_id + champs fréquents pour le multi-tenancy
        Schema::table('patients', function (Blueprint $table) {
            $table->index(['clinic_id', 'nom', 'prenom'], 'patients_clinic_nom_prenom');
            $table->index(['clinic_id', 'medecin_id'], 'patients_clinic_medecin');
        });

        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->index(['clinic_id', 'date_rv', 'statut'], 'rdv_clinic_date_statut');
            $table->index(['clinic_id', 'medecin_id', 'date_rv'], 'rdv_clinic_medecin_date');
            $table->index(['clinic_id', 'patient_id'], 'rdv_clinic_patient');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->index(['clinic_id', 'created_at'], 'consultations_clinic_created');
            $table->index(['clinic_id', 'medecin_id'], 'consultations_clinic_medecin');
        });

        Schema::table('disponibilites', function (Blueprint $table) {
            $table->index(['medecin_id', 'date_travail'], 'dispo_medecin_date');
            $table->index(['clinic_id', 'date_travail'], 'dispo_clinic_date');
        });

        Schema::table('medecins', function (Blueprint $table) {
            $table->index(['clinic_id', 'nom'], 'medecins_clinic_nom');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['clinic_id', 'role'], 'users_clinic_role');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['clinic_id', 'created_at'], 'logs_clinic_created');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('patients_clinic_nom_prenom');
            $table->dropIndex('patients_clinic_medecin');
        });

        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropIndex('rdv_clinic_date_statut');
            $table->dropIndex('rdv_clinic_medecin_date');
            $table->dropIndex('rdv_clinic_patient');
        });

        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex('consultations_clinic_created');
            $table->dropIndex('consultations_clinic_medecin');
        });

        Schema::table('disponibilites', function (Blueprint $table) {
            $table->dropIndex('dispo_medecin_date');
            $table->dropIndex('dispo_clinic_date');
        });

        Schema::table('medecins', function (Blueprint $table) {
            $table->dropIndex('medecins_clinic_nom');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_clinic_role');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('logs_clinic_created');
        });
    }
};
