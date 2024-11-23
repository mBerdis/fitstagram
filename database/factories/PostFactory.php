<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
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
        // Get the image contents from the URL
        $imageContent = file_get_contents('https://picsum.photos/600/400');

        // Generate a unique name for the image
        $imageName = uniqid() . '.jpg'; // Assuming it's a .jpg image

        // Store the image in the 'uploads' directory within 'public'
        Storage::disk('public')->put('uploads/' . $imageName, $imageContent);

        // Return the image path (or you can save this path in your database)
        $imagePath = env('DB_STORAGE_PATH') . $imageName;

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'photo' => $imagePath,
            'is_public' => fake()->boolean(),
            'description' => fake()->realText(50) . fake()->emoji(),
            'like_count' => 0
        ];
    }
}
