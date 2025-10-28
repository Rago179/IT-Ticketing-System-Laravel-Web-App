<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Profile;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
// 1. Create admin user with a profile
        User::factory()->has(Profile::factory())->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        // 2. Create IT user with a profile
        User::factory()->has(Profile::factory())->create([
            'name' => 'IT Support',
            'email' => 'it@example.com',
            'role' => 'it',
        ]);

        // 3. Create 5 normal users, each with a profile
        $users = User::factory(5)->has(Profile::factory())->create();
        
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
