<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 4. Seed Posts (Tickets) ---
        Post::create([
            'id' => 101,
            'user_id' => 3, 
            'title' => 'Cannot print from my office desktop.',
            'description' => 'The shared network printer (PRT-101) is unresponsive when printing documents. It worked fine yesterday.',
            'status' => 'open',
        ]);

        Post::create([
            'id' => 102,
            'user_id' => 1, 
            'title' => 'CRM Login authentication failing intermittently.',
            'description' => 'Users are reporting random 500 errors when trying to log into the CRM. This is a critical issue.',
            'status' => 'in_progress',
        ]);

        Post::create([
            'id' => 103,
            'user_id' => 3, 
            'title' => 'Requesting a new external monitor for desk.',
            'description' => 'My current monitor is too small for design work. Requesting a 32-inch 4K screen.',
            'status' => 'resolved',
        ]);
    }
}