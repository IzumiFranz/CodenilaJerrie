<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Learning Management System', 'type' => 'string', 'description' => 'Application name', 'is_public' => true],
            ['key' => 'app_logo', 'value' => '/images/logo.png', 'type' => 'string', 'description' => 'Application logo path', 'is_public' => true],
            ['key' => 'academic_year', 'value' => '2024-2025', 'type' => 'string', 'description' => 'Current academic year', 'is_public' => true],
            ['key' => 'current_semester', 'value' => '1st', 'type' => 'string', 'description' => 'Current semester', 'is_public' => true],
            ['key' => 'enrollment_open', 'value' => '1', 'type' => 'boolean', 'description' => 'Is enrollment currently open', 'is_public' => true],
            ['key' => 'max_quiz_attempts', 'value' => '3', 'type' => 'integer', 'description' => 'Default maximum quiz attempts', 'is_public' => false],
            ['key' => 'quiz_time_limit', 'value' => '60', 'type' => 'integer', 'description' => 'Default quiz time limit in minutes', 'is_public' => false],
            ['key' => 'passing_score', 'value' => '60', 'type' => 'integer', 'description' => 'Default passing score percentage', 'is_public' => false],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'description' => 'System maintenance mode', 'is_public' => true],
            ['key' => 'ai_enabled', 'value' => '1', 'type' => 'boolean', 'description' => 'Enable AI features', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}