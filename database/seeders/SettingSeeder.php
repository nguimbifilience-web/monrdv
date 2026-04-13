<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Général
            ['key' => 'app_name', 'value' => 'MonRDV', 'type' => 'string', 'group' => 'general', 'label' => "Nom de l'application"],
            ['key' => 'default_timezone', 'value' => 'Africa/Douala', 'type' => 'string', 'group' => 'general', 'label' => 'Fuseau horaire par défaut'],
            ['key' => 'default_language', 'value' => 'fr', 'type' => 'string', 'group' => 'general', 'label' => 'Langue par défaut'],

            // Sécurité
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'int', 'group' => 'security', 'label' => 'Durée de session (minutes)'],
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'int', 'group' => 'security', 'label' => 'Longueur minimale du mot de passe'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'int', 'group' => 'security', 'label' => 'Tentatives de connexion max'],

            // Notifications
            ['key' => 'mail_from_address', 'value' => 'noreply@monrdv.ga', 'type' => 'string', 'group' => 'notifications', 'label' => 'Expéditeur par défaut'],
            ['key' => 'mail_from_name', 'value' => 'MonRDV', 'type' => 'string', 'group' => 'notifications', 'label' => "Nom d'expéditeur"],

            // Maintenance
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'bool', 'group' => 'maintenance', 'label' => 'Mode maintenance'],
            ['key' => 'maintenance_message', 'value' => 'Application en maintenance, merci de revenir plus tard.', 'type' => 'string', 'group' => 'maintenance', 'label' => 'Message de maintenance'],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
