<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['id' => 1, 'name' => 'Software Bug']);
        Category::create(['id' => 2, 'name' => 'Hardware Request']);
        Category::create(['id' => 3, 'name' => 'Network Issue']);
        Category::create(['id' => 4, 'name' => 'Account Management']);
        // Add this new line
        Category::create(['id' => 5, 'name' => 'Other']);
    }
}