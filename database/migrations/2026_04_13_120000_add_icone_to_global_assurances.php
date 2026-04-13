<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('global_assurances') && !Schema::hasColumn('global_assurances', 'icone')) {
            Schema::table('global_assurances', function (Blueprint $table) {
                $table->string('icone')->nullable()->after('nom');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('global_assurances', 'icone')) {
            Schema::table('global_assurances', function (Blueprint $table) {
                $table->dropColumn('icone');
            });
        }
    }
};
