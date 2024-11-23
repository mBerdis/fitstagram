<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get the image contents from the URL
        $imageContent = file_get_contents('https://picsum.photos/200/200');

        // Generate a unique name for the image
        $imageName = uniqid() . '.jpg'; // Assuming it's a .jpg image

        // Store the image in the 'uploads' directory within 'public'
        Storage::disk('public')->put('uploads/' . $imageName, $imageContent);

        // Return the image path (or you can save this path in your database)
        $imagePath = env('DB_STORAGE_PATH') . $imageName;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => ucfirst(fake()->word) . ' ' . fake()->word,
            'profile_picture' => $imagePath,
            'description' => fake()->realText(15) . fake()->emoji() . fake()->realText(15) . fake()->emoji()
        ];
    }
}
