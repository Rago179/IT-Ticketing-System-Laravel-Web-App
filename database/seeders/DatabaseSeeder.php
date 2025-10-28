<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin and IT users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'IT Support',
            'email' => 'it@example.com',
            'role' => 'it',
        ]);

        // Create 5 normal users
        $users = User::factory(5)->create();

        // Create 10 posts and link each to a random user
        $posts = Post::factory(10)->create([
            'user_id' => $users->random()->id,
        ]);

        // Add comments for each post
        $posts->each(function ($post) use ($users) {
            Comment::factory(rand(1, 5))->create([
                'post_id' => $post->id,
                'user_id' => $users->random()->id,
            ]);
        });

        

    }
}
