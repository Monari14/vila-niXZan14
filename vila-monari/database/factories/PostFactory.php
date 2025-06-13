<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'content' => fake()->text(100),
            'image' => null,
        ];
    }
    /*
        public function withUser(?User $user = null): self
        {
            return $this->state( [{
                'user_id' => $user ? $user->id : null,
            }]);
        }
    */
}
