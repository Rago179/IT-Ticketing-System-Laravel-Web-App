<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Comment::create([
            'post_id' => 101,
            'user_id' => 2, 
            'content' => 'I have initiated a remote connection to check the printer queue and services. Will update soon.',
        ]);

        Comment::create([
            'post_id' => 101,
            'user_id' => 3, 
            'content' => 'Thanks, let me know if you need me to reboot my computer.',
        ]);
    }
}