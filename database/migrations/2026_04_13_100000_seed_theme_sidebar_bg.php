<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('settings')->where('key', 'theme_sidebar_bg')->exists();
        if (!$exists) {
            DB::table('settings')->insert([
                'key' => 'theme_sidebar_bg',
                'value' => '#111827',
                'type' => 'string',
                'group' => 'appearance',
                'label' => 'Fond de la sidebar Super Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'theme_sidebar_bg')->delete();
    }
};
