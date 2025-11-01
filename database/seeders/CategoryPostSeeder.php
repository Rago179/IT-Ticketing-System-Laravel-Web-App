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
        DB::table('category_post')->insert([
            ['post_id' => 101, 'category_id' => 2],
            ['post_id' => 101, 'category_id' => 3],
            ['post_id' => 102, 'category_id' => 1],
            ['post_id' => 103, 'category_id' => 2],
        ]);
    }
}