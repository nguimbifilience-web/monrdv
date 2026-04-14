<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Doublon de la migration 2026_03_23_132302_add_est_assure_to_patients_table.
        // Conservee pour l'historique mais rendue idempotente pour ne pas casser les
        // environnements ou elle a deja ete jouee (prod InfinityFree) ni ceux qui
        // repartent de zero (tests, nouveaux dev).
        if (!Schema::hasColumn('patients', 'est_assure')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->boolean('est_assure')->default(false)->after('telephone');
            });
        }
    }

    public function down(): void
    {
        // Le rollback est gere par la migration originale 2026_03_23.
    }
};