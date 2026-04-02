<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            // Branding
            $table->string('logo_path')->nullable()->after('address');
            $table->string('primary_color', 7)->nullable()->after('logo_path');
            $table->string('secondary_color', 7)->nullable()->after('primary_color');

            // Blocage abonnement
            $table->boolean('is_blocked')->default(false)->after('is_active');
            $table->text('blocked_reason')->nullable()->after('is_blocked');
            $table->timestamp('blocked_at')->nullable()->after('blocked_reason');
            $table->date('subscription_expires_at')->nullable()->after('blocked_at');
        });
    }

    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'primary_color', 'secondary_color',
                'is_blocked', 'blocked_reason', 'blocked_at', 'subscription_expires_at',
            ]);
        });
    }
};
