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
        // 1. Create essential Admin and IT users
        User::factory()->has(Profile::factory())->create([
            'name' => 'John Admin',
            'email' => 'john.admin@app.com', // Using unique emails
            'role' => 'admin',
        ]);

        User::factory()->has(Profile::factory())->create([
            'name' => 'Jane IT',
            'email' => 'jane.it@app.com', // Using unique emails
            'role' => 'it',
        ]);
        
        // 2. Create 5 normal users, each with a profile
        User::factory(5)->has(Profile::factory())->create();
        
        // 3. Define ALL users to be used for post and comment assignments
        $allUsers = User::all(); // <-- FIXED: Define this variable here

        // 4. Seed Categories
        $categories = Category::factory(5)->sequence(
            ['name' => 'Software Bug'],
            ['name' => 'Hardware Request'],
            ['name' => 'Account Issue'],
            ['name' => 'Network Down'],
            ['name' => 'New Feature']
        )->create();
        
        // 5. Create 10 posts (tickets)
        $posts = Post::factory(10)->create([ // <-- FIXED: Define $posts here
            'user_id' => $allUsers->random()->id,
        ]);
        
        // 6. Loop through created posts to attach categories and comments
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