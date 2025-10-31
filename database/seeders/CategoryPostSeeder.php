<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 6. Seed Post Categories (Pivot Table) ---
        // Relates hard-coded post IDs (101, 102, 103) to category IDs (1, 2, 3, 4)
        DB::table('category_post')->insert([
            // Post 101: Hardware Request (2) and Network Issue (3)
            ['post_id' => 101, 'category_id' => 2],
            ['post_id' => 101, 'category_id' => 3],
            // Post 102: Software Bug (1)
            ['post_id' => 102, 'category_id' => 1],
            // Post 103: Hardware Request (2)
            ['post_id' => 103, 'category_id' => 2],
        ]);
    }
}