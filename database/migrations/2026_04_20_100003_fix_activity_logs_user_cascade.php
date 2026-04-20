<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RGPD / Audit : supprimer un user ne doit pas supprimer son historique
 * d'actions dans activity_logs. On passe de ON DELETE CASCADE a SET NULL.
 * Les logs restent donc accessibles apres suppression du user associe
 * (user_id devient NULL, description conserve le contexte).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }
};
