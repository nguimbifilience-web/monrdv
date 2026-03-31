<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $tables = [
        'users',
        'patients',
        'medecins',
        'rendez_vous',
        'consultations',
        'specialites',
        'assurances',
        'disponibilites',
        'documents_patient',
        'activity_logs',
        'patient_validation_codes',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'clinic_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->foreignId('clinic_id')->nullable()->after('id')->constrained('clinics')->onDelete('cascade');
                    $blueprint->index('clinic_id');
                });
            }
        }

        // Créer la clinique par défaut et assigner toutes les données existantes
        $clinicId = DB::table('clinics')->insertGetId([
            'name' => 'Clinique MonRDV',
            'slug' => 'monrdv',
            'email' => 'contact@monrdv.ga',
            'phone' => '074 00 00 00',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assigner toutes les données existantes à cette clinique
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->whereNull('clinic_id')->update(['clinic_id' => $clinicId]);
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'clinic_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropConstrainedForeignId('clinic_id');
                });
            }
        }
    }
};
