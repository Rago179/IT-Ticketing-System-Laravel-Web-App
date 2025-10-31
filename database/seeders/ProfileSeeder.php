<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 2. Seed Profiles (One-to-One: must match IDs from UserSeeder) ---
        Profile::create([
            'user_id' => 1,
            'bio' => 'Global Administrator for all systems. Escalation point for serious issues.',
        ]);

        Profile::create([
            'user_id' => 2,
            'bio' => 'Front-line IT specialist. Handles hardware and software requests.',
        ]);

        Profile::create([
            'user_id' => 3,
            'bio' => 'Regular employee in the sales department.',
        ]);
    }
}