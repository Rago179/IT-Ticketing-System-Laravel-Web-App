<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Profile;
use App\Models\Category;

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

        $categories = Category::factory(5)->sequence(
            ['name' => 'Software Bug'],
            ['name' => 'Hardware Request'],
            ['name' => 'Account Issue'],
            ['name' => 'Network Down'],
            ['name' => 'New Feature']
        )->create();

        // Create 10 posts and link each to a random user
        $posts->each(function (Post $post) use ($categories, $allUsers) {
                    // Attach 1 to 3 random categories to each post
                    $post->categories()->attach(
                        $categories->random(rand(1, 3))->pluck('id')->toArray()
                    );

                    // Add comments for each post
                    Comment::factory(rand(1, 3))->create([
                        'post_id' => $post->id,
                        'user_id' => $allUsers->random()->id,
                    ]);
                });
    }
}
