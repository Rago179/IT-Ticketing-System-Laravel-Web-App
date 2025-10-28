<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Profile;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class; 

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => fake()->sentence(),
        ];
    }
}
