<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'post_id' => Post::inRandomOrder()->first()->id ?? Post::factory()->create()->id,
            'user_id' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'content' => $this->faker->sentence(),
        ];
    }
}