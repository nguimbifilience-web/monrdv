<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rows = [
            ['key' => 'theme_bg_page', 'value' => '#f1f5f9', 'type' => 'string', 'group' => 'appearance', 'label' => 'Couleur de fond des pages'],
            ['key' => 'theme_bg_card', 'value' => '#ffffff', 'type' => 'string', 'group' => 'appearance', 'label' => 'Couleur de fond des cartes'],
            ['key' => 'theme_text_primary', 'value' => '#1e3a8a', 'type' => 'string', 'group' => 'appearance', 'label' => 'Couleur du texte principal'],
            ['key' => 'theme_accent', 'value' => '#2563eb', 'type' => 'string', 'group' => 'appearance', 'label' => 'Couleur d\'accent (boutons, liens)'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('settings')->where('key', $row['key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert($row + ['created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'theme_bg_page', 'theme_bg_card', 'theme_text_primary', 'theme_accent',
        ])->delete();
    }
};
