<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FriendFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        do {
            $user1 = User::inRandomOrder()->first()->id;
            $user2 = User::inRandomOrder()->first()->id;
        } while ($user1 === $user2); // Tento cyklus zabezpečí, že user1 nebude rovnaký ako user2

        return [
            'user1' => $user1,
            'user2' => $user2
        ];
    }
}
