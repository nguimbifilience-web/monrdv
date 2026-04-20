<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Flag force l'utilisateur a changer son mot de passe au prochain login.
 * Active par defaut pour tous les users crees via CompteController,
 * PatientController, MedecinController, ClinicController (MDP temporaire
 * genere par l'admin et transmis une fois).
 *
 * Les comptes existants restent a false (pas de changement force).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'must_change_password')) {
                $table->dropColumn('must_change_password');
            }
        });
    }
};
