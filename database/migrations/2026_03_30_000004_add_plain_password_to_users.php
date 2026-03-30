<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('plain_password')->nullable()->after('password');
        });

        // Mettre le mot de passe par défaut pour les comptes existants
        \Illuminate\Support\Facades\DB::table('users')->update(['plain_password' => 'password']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('plain_password');
        });
    }
};
