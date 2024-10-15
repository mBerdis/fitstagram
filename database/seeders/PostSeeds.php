<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeds extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $posts = Post::factory()->count(20)->create();
    }
}
