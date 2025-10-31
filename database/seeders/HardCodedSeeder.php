<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Models\User;
use App\Models\Profile;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Support\Carbon;

class HardCodedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // FIX: Temporarily disable foreign key checks to allow TRUNCATE
        // ---------------------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear old data to ensure a clean slate, especially since we're using fixed IDs
        // Order matters when truncating: tables with FKs must be truncated first.
        Comment::truncate();
        Post::truncate();
        Category::truncate();
        Profile::truncate();
        
        // Truncating the pivot table manually, since it doesn't have an Eloquent Model
        DB::table('category_post')->truncate();
        
        User::truncate(); 

        // ---------------------------------------------------------------------
        // FIX: Re-enable foreign key checks after truncating
        // ---------------------------------------------------------------------
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        // --- 1. Seed Users (Including Different Roles) ---
        // Password for all users will be 'password'
        $admin = User::create([
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
        ]);

        $it_support = User::create([
            'id' => 2,
            'name' => 'Jane Tech',
            'email' => 'jane.tech@company.com',
            'password' => Hash::make('password'),
            'role' => 'it',
            'email_verified_at' => Carbon::now(),
        ]);

        $regular_user = User::create([
            'id' => 3,
            'name' => 'Bob Requester',
            'email' => 'bob.r@company.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => Carbon::now(),
        ]);

        // --- 2. Seed Profiles (One-to-One Relationship) ---
        Profile::create([
            'user_id' => $admin->id,
            'bio' => 'Global Administrator for all systems. Escalation point for serious issues.',
        ]);

        Profile::create([
            'user_id' => $it_support->id,
            'bio' => 'Front-line IT specialist. Handles hardware and software requests.',
        ]);

        Profile::create([
            'user_id' => $regular_user->id,
            'bio' => 'Regular employee in the sales department.',
        ]);

        // --- 3. Seed Categories ---
        $category_bug = Category::create(['id' => 1, 'name' => 'Software Bug']);
        $category_hardware = Category::create(['id' => 2, 'name' => 'Hardware Request']);
        $category_network = Category::create(['id' => 3, 'name' => 'Network Issue']);
        $category_account = Category::create(['id' => 4, 'name' => 'Account Management']);

        // --- 4. Seed Posts (Tickets) (One-to-Many: User -> Post) ---
        $post1_open = Post::create([
            'id' => 101,
            'user_id' => $regular_user->id,
            'title' => 'Cannot print from my office desktop.',
            'description' => 'The shared network printer (PRT-101) is unresponsive when printing documents. It worked fine yesterday.',
            'status' => 'open',
        ]);

        $post2_progress = Post::create([
            'id' => 102,
            'user_id' => $admin->id,
            'title' => 'CRM Login authentication failing intermittently.',
            'description' => 'Users are reporting random 500 errors when trying to log into the CRM. This is a critical issue.',
            'status' => 'in_progress',
        ]);

        $post3_resolved = Post::create([
            'id' => 103,
            'user_id' => $regular_user->id,
            'title' => 'Requesting a new external monitor for desk.',
            'description' => 'My current monitor is too small for design work. Requesting a 32-inch 4K screen.',
            'status' => 'resolved',
        ]);

        // --- 5. Seed Comments (One-to-Many: Post -> Comment) ---
        Comment::create([
            'post_id' => $post1_open->id,
            'user_id' => $it_support->id,
            'content' => 'I have initiated a remote connection to check the printer queue and services. Will update soon.',
        ]);

        Comment::create([
            'post_id' => $post1_open->id,
            'user_id' => $regular_user->id,
            'content' => 'Thanks, let me know if you need me to reboot my computer.',
        ]);

        // --- 6. Seed Post Categories (Many-to-Many Relationship) ---
        // Post 101: Printer issue is a Hardware Request and a Network Issue
        $post1_open->categories()->attach([$category_hardware->id, $category_network->id]);

        // Post 102: CRM issue is a Software Bug
        $post2_progress->categories()->attach([$category_bug->id]);

        // Post 103: Monitor request is a Hardware Request
        $post3_resolved->categories()->attach([$category_hardware->id]);
    }
}
