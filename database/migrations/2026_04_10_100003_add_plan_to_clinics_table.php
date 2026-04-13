<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'plan_id')) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('city');
            }
            if (!Schema::hasColumn('clinics', 'subscription_started_at')) {
                $table->date('subscription_started_at')->nullable()->after('plan_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            if (Schema::hasColumn('clinics', 'subscription_started_at')) {
                $table->dropColumn('subscription_started_at');
            }
            if (Schema::hasColumn('clinics', 'plan_id')) {
                $table->dropColumn('plan_id');
            }
        });
    }
};
