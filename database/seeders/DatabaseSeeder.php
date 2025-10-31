<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Category;
use App\Models\Post;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // STEP 1: Run the Hard-Coded Seeder FIRST.
        // This creates essential, predictable data (Admin/IT users, specific
        // tickets, and categories) and clears the database beforehand.
        // ---------------------------------------------------------------------
        $this->call(HardCodedSeeder::class);


        // ---------------------------------------------------------------------
        // STEP 2: Run the Factory-Based Seeding Logic.
        // This adds extra random/realistic data on top of the hard-coded data.
        // NOTE: We only generate additional data here, so users/categories/
        // posts generated below do NOT conflict with the hard-coded ones.
        // ---------------------------------------------------------------------

        // 1. Create a few EXTRA normal users (Admin/IT are already created
        // by HardCodedSeeder and cleared the database for us).
        // Since HardCodedSeeder truncated data, the factory users start from a fresh ID.
        \App\Models\User::factory(5)->has(\App\Models\Profile::factory())->create();
        
        // 2. Define ALL users to be used for random assignment (includes hard-coded users + new factory users)
        $allUsers = \App\Models\User::all();

        // 3. Define ALL categories (includes hard-coded categories + the 5 factory categories below)
        $categories = \App\Models\Category::all()->merge(
            \App\Models\Category::factory(5)->sequence(
                ['name' => 'General Inquiry'],
                ['name' => 'Cloud Access'],
                ['name' => 'Data Request'],
                ['name' => 'Training Need'],
                ['name' => 'System Downtime']
            )->create()
        );
        
        // 4. Create 10 additional posts (tickets) randomly assigned to any user
        $posts = \App\Models\Post::factory(10)->create([
            'user_id' => $allUsers->random()->id,
        ]);
        
        // 5. Loop through these new posts to attach categories and comments
        $posts->each(function (\App\Models\Post $post) use ($categories, $allUsers) {
            // Attach 1 to 3 random categories from the full list (hard-coded + new)
            $post->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );

            // Add comments for each post
            \App\Models\Comment::factory(rand(1, 3))->create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
            ]);
        });
    }
}