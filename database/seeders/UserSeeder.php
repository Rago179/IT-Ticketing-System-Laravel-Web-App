<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'Admin User',
            'email' => 'admin@company.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'id' => 2,
            'name' => 'Jane Tech',
            'email' => 'jane.tech@company.com',
            'password' => Hash::make('password'),
            'role' => 'it',
            'email_verified_at' => Carbon::now(),
        ]);

        User::create([
            'id' => 3,
            'name' => 'Bob Requester',
            'email' => 'bob.r@company.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'email_verified_at' => Carbon::now(),
        ]);
    }
}