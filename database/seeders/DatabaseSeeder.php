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
        
        
        $this->call([
            UserSeeder::class,
            ProfileSeeder::class, 
            CategorySeeder::class,
            PostSeeder::class, 
            CommentSeeder::class,
            CategoryPostSeeder::class, 
        ]);


       
        \App\Models\User::factory(5)->has(\App\Models\Profile::factory())->create();
        
        $allUsers = \App\Models\User::all();

        $categories = \App\Models\Category::all()->merge(
            \App\Models\Category::factory(5)->sequence(
                ['name' => 'General Inquiry'],
                ['name' => 'Cloud Access'],
                ['name' => 'Data Request'],
                ['name' => 'Training Need'],
                ['name' => 'System Downtime']
            )->create()
        );
        

        $posts = \App\Models\Post::factory(10)->create([
            'user_id' => $allUsers->random()->id,
        ]);
        

        $posts->each(function (\App\Models\Post $post) use ($categories, $allUsers) {
            $post->categories()->attach(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );

            \App\Models\Comment::factory(rand(1, 3))->create([
                'post_id' => $post->id,
                'user_id' => $allUsers->random()->id,
            ]);
        });
    }
}