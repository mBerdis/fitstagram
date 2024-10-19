<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get the image contents from the URL
        $imageContent = file_get_contents('https://thispersondoesnotexist.com/');

        // Generate a unique name for the image
        $imageName = uniqid() . '.jpg'; // Assuming it's a .jpg image

        // Store the image in the 'uploads' directory within 'public'
        Storage::disk('public')->put('uploads/' . $imageName, $imageContent);

        // Return the image path (or you can save this path in your database)
        $imagePath = '/storage/uploads/' . $imageName;

        return [
            'username' => fake()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'first_name' => fake()->name(),
            'last_name'=> fake()->name(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'profile_picture' => $imagePath
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
