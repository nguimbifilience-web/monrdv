<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('patients', function (Blueprint $table) {

            if (!Schema::hasColumn('patients', 'quartier')) {
                $table->string('quartier')->nullable()->after('telephone');
            }

            if (!Schema::hasColumn('patients', 'ville')) {
                $table->string('ville')->nullable()->after('quartier');
            }

            if (!Schema::hasColumn('patients', 'date_naissance')) {
                $table->date('date_naissance')->nullable()->after('ville');
            }

            if (!Schema::hasColumn('patients', 'sexe')) {
                $table->string('sexe')->nullable()->after('date_naissance');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('patients', function (Blueprint $table) {

            if (Schema::hasColumn('patients', 'quartier')) {
                $table->dropColumn('quartier');
            }

            if (Schema::hasColumn('patients', 'ville')) {
                $table->dropColumn('ville');
            }

            if (Schema::hasColumn('patients', 'date_naissance')) {
                $table->dropColumn('date_naissance');
            }

            if (Schema::hasColumn('patients', 'sexe')) {
                $table->dropColumn('sexe');
            }

        });
    }
};